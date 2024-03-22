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
use Illuminate\Support\Facades\Hash;

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
        if($attributes['name'] == 'ec2-user') {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.name_existed'));
        }

        if(config('app.env') === ENVIRONMENT_UPDATE) {
            $crateUser = Common::createUserEc2($attributes['name'], @$attributes['ssh_public_key'], $attributes['viam_user_id'], @$attributes['github_gmail']);
            if ($crateUser->original['code'] != CODE_SUCCESS) {
                return $crateUser;
            }
        }

        if (!isset($attributes['entry_date']) || empty($attributes['entry_date'])) {
            $attributes['entry_date'] = null;
        }
        $attributes['paid_off'] = 0;
        $attributes['paid_off_start'] = $attributes['paid_off_start'] ?? 0;
        $attributes['created_at'] = Carbon::now();
        $attributes['password'] = Hash::make($attributes['password']);
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
        $oldRetirementDate = $user->retirement_date ? Carbon::parse($user->retirement_date)->format('Y-m-d') : null;
        $oldViamUserId = $user->viam_user_id;
        $oldGithubGmail = $user->github_gmail;
        $updateName = $attributes['name'];
        $updateRetirementDate = $attributes['retirement_date'] ? Carbon::parse($attributes['retirement_date'])->format('Y-m-d') : null;
        $updateViamUserId = $attributes['viam_user_id'];
        $updateGithubGmail = @$attributes['github_gmail'];
        $publicKey = @$attributes['ssh_public_key'];

        if(config('app.env') === ENVIRONMENT_UPDATE) {
            if($oldRetirementDate != $updateRetirementDate || ($oldName != $updateName)
                || ($oldViamUserId != $updateViamUserId) || $oldGithubGmail != $updateGithubGmail) {
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
                    sleep(2);
                    $crateUser = Common::createUserEc2($updateName, $publicKey, $updateViamUserId, $updateGithubGmail);
                    if($crateUser->original['code'] != CODE_SUCCESS) {
                        return $crateUser;
                    }
                }
            } else {
                if(!$this->updateSshKey($user, $publicKey)) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.ssh_key_and_gmail_github'));
                }
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
            $gmailGithub = $user->github_gmail;
            $instanceIds = [];

            foreach ($policies as $policy) {
                if ($policy->type == POLICY_TYPE['EC2_admin'] || $policy->type == POLICY_TYPE['EC2_deploy']) {
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
                    'DocumentName' => 'AWS-RunShellScript'
                ];
                $userNotExists = Common::checkUserExist($ssmClient, $parameters, $instanceId, [$username]);
                $command = [];
                if (!empty($userNotExists)) {
                    $command[] = "sudo adduser $username";
                    $command[] = "sudo -u $username mkdir -p /home/$username/.ssh";
                    $command[] =  "sudo -u $username ssh-keygen -t rsa -b 4096 -C \"$gmailGithub\" -N \"\" -f \"/home/$username/.ssh/id_rsa\" > /dev/null";
                    $nodePath = self::getNodePath($instanceId);
                    if($nodePath) {
                        $command[] = "grep -qxF 'export PATH=\"$nodePath:\$PATH\"' /home/$username/.bashrc || echo 'export PATH=\"$nodePath:\$PATH\"' | sudo tee -a /home/$username/.bashrc";
                    }
                } else {
                    $command = [
                        "echo $publicKey | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null"
                    ];
                }
                $parameters['Parameters']['commands'] = $command;
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
            if(config('app.env') === ENVIRONMENT_UPDATE) {
                $delete = Common::deleteUserEc2($user);
                if ($delete->original['code'] != CODE_SUCCESS) {
                    return $delete;
                }
            }
            parent::delete($id);
            return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }
}
