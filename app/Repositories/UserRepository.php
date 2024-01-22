<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace Repository;

use App\Exports\UserExport;
use App\Http\Resources\BaseResource;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Aws\Ssm\SsmClient;
use Carbon\Carbon;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    /**
     * Instantiate model
     *
     * @param User $model
     */

    public function model()
    {
        return User::class;
    }

    public function pagination($request)
    {
        $limit = is_null(request('per_page')) ? 15 : request('per_page');

        $data = $this->model->query()->with(['viam_user'])
            ->whereNull('deleted_at')
            ->FindByName($request)
            ->FindByEmail($request)
            ->FindByVIAMUser($request);

        if ($limit > 0) {
            return $data->paginate($limit);
        }
        return $data->get();
    }

//    public function export($request){
//      $data = $this->pagination($request);
//      $fileName = "VFaceExport" . ".xlsx";
//      return (new UserExport($data))->download($fileName);
//    }

    public function create(array $attributes)
    {
        $crateUser = Common::createUserEc2($attributes['name'], @$attributes['ssh_public_key'], $attributes['viam_user_id']);
        if($crateUser->original['code'] != CODE_SUCCESS) {
            return $crateUser;
        }

        if (!isset($attributes['entry_date']) || empty($attributes['entry_date'])) {
            $attributes['entry_date'] = null;
        }
        $attributes['paid_off'] = 0;
        $attributes['paid_off_start'] = $attributes['paid_off_start'] ?? 0;
        $attributes['created_at'] = Carbon::now();
        $attributes['password'] = bcrypt($attributes['password']);
        $model = $this->model->create($attributes);

        return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
    }

    public function update(array $attributes, $id)
    {
        $user = $this->model->find($id);
        if ($user == null) {
            return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND, trans('api.user.not.found'), trans('api.user.not.found'));
        }

        if ($user->email != $attributes['email']) {
            $userCheckEmail = $this->model->where('email', $attributes['email'])->first();
            if ($userCheckEmail) {
                return ResponseService::responseJsonError(Response::HTTP_BAD_REQUEST, trans('api.user.email.exist'), trans('api.user.email.exist'));
            }
        }

        $oldName = $user->name;
        $oldRetirementDate = Carbon::parse($user->retirement_date)->format('Y-m-d');
        $oldViamUserId = $user->viam_user_id;
        $updateName = $attributes['name'];
        $updateRetirementDate = $attributes['retirement_date'];
        $updateViamUserId = $attributes['viam_user_id'];
        $publicKey = @$attributes['ssh_public_key'];

        if($oldRetirementDate != $updateRetirementDate || ($oldName != $updateName) || ($oldViamUserId != $updateViamUserId)) {
            if(($user->retirement_date != $attributes['retirement_date']) && (Carbon::now() >= Carbon::parse($updateRetirementDate))) {
                $delete = Common::deleteUserEc2($user);
                if($delete->original['code'] != CODE_SUCCESS) {
                    return $delete;
                }
            } else {
                $delete = Common::deleteUserEc2($user);
                if($delete->original['code'] != CODE_SUCCESS) {
                    return $delete;
                }

                $crateUser = Common::createUserEc2($updateName, $publicKey, $updateViamUserId);
                if($crateUser->original['code'] != CODE_SUCCESS) {
                    return $crateUser;
                }
            }
        } else {
            if(!$this->updateSshKey($user, $publicKey)) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.ssh_key'));
            }
        }

        $attributes['updated_at'] = Carbon::now();
        if (isset($attributes['password']) && !empty($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        } else {
            unset($attributes['password'], $attributes['password_confirmation']);
        }
        return ResponseService::responseJson(CODE_SUCCESS, new BaseResource(parent::update($attributes, $id)));
    }

    private function updateSshKey(User $user, $publicKey)
    {
        if($user->ssh_public_key != $publicKey) {
            $param = Common::configAwsSDK();
            $ssmClient = new SsmClient($param);
            $policies = $user->viam_user->policies;
            $username = $user->name;
            $instanceIds = [];

            foreach ($policies as $policy) {
                if ($policy->type == POLICY_TYPE['AWS_admin'] || $policy->type == POLICY_TYPE['AWS_deploy']) {
                    if (empty($publicKey)) {
                        return false;
                    }
                    $instanceIds[] = $policy->instance_id;
                }
            }
            $instanceIds = array_unique($instanceIds);
            foreach ($instanceIds as $instanceId) {
                $parameters = [
                    'InstanceIds' => [$instanceId],
                    'DocumentName' => 'AWS-RunShellScript',
                    'Parameters' => [
                        'commands' => ["echo $publicKey | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null"]
                    ]
                ];
                $ssmClient->sendCommand($parameters);
            }
        }
        return true;
    }

    public function getAll()
    {
        return $this->model->select(['id', 'name'])->get();
    }

    public function detail($id)
    {
        $roleUser = User::getRoleVFace(Auth::user());
        if ($roleUser == POLICY_V_FACE_ID['Normal'] && $id != Auth::id()) {
            return false;
        }

        $data = $this->model->with(['viam_user'])->find($id);
        $now = Carbon::now();
        $entry_date = Carbon::parse($data['entry_date'])->addMonth(2);
        $data['paid_off'] = ($now >= $entry_date) ? $data['paid_off'] : 0;
        return $data;
    }

    public function delete($id)
    {
        $user = $this->model->find($id);
        if($user == null) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.not.found'));
        }

        try {
            $delete = Common::deleteUserEc2($user);
            if($delete->original['code'] != CODE_SUCCESS) {
                return $delete;
            }
            parent::delete($id);
            return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }
}
