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
use Aws\Iam\IamClient;
use Carbon\Carbon;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Http\Response;
use Repository\BaseRepository;
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
        $param = Common::configAwsSDK();
        $iamClient = new IamClient($param);
        try {
            $iamAWS = $iamClient->listUsers()['Users'];
            foreach ($iamAWS as $user) {
                if ($attributes['name'] == $user['UserName']) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.name_existed'));
                }
            }
            $iamClient->createUser([
                'UserName' => $attributes['name']
            ]);

            if (!isset($attributes['entry_date']) || empty($attributes['entry_date'])) {
                $attributes['entry_date'] = null;
            }
            $attributes['paid_off'] = 0;
            $attributes['created_at'] = Carbon::now();
            $attributes['password'] = bcrypt($attributes['password']);
            $model = $this->model->create($attributes);

            return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }

    public function update(array $attributes, $id)
    {
        $user = $this->model->find($id);
        if ($user == null) {
            return ResponseService::responseJsonError(Response::HTTP_NOT_FOUND, trans('api.user.not.found'), trans('api.user.not.found'));
        }

        $nameOld = $user->name;
            $param = Common::configAwsSDK();
            $iamClient = new IamClient($param);
        $iamAws = $iamClient->listUsers()['Users'];
        if ($nameOld != $attributes['name']) {
            try {
                $isAwsUserName = false;
                foreach ($iamAws as $userAws) {
                    if ($attributes['name'] == $userAws['UserName']) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.name_existed'));
                    }
                    if ($nameOld == $userAws['UserName']) {
                        $isAwsUserName = true;
                    }
                }

                if ($isAwsUserName) {
                    $iamClient->updateUser([
                        'UserName' => $nameOld,
                        'NewUserName' => $attributes['name']
                    ]);
                }
            } catch (AwsException $e) {
                return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
            }
        }

        $retirement_date = $user->retirement_date;
        $retirement_date_update = $attributes['retirement_date'];
        if ($retirement_date != $retirement_date_update) {
            try {
                $isCreateNew = true;
                $isDelete = false;

                foreach ($iamAws as $userAws) {
                    if ($attributes['name'] == $userAws['UserName']) {
                        $isCreateNew = false;
                        $isDelete = true;
                    }
                }

                if(Carbon::now() >= Carbon::parse($retirement_date_update) && $isDelete) {
                    $iamClient->deleteUser([
                        'UserName' => $attributes['name']
                    ]);
                }
                if(Carbon::now() < Carbon::parse($retirement_date_update) && $isCreateNew) {
                    $iamClient->createUser([
                        'UserName' => $attributes['name']
                    ]);
                }
            } catch (AwsException $e) {
                return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
            }
        }

        if ($user->email != $attributes['email']) {
            $userCheckEmail = $this->model->where('email', $attributes['email'])->first();
            if ($userCheckEmail) {
                return ResponseService::responseJsonError(Response::HTTP_BAD_REQUEST, trans('api.user.email.exist'), trans('api.user.email.exist'));
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

        $param = Common::configAwsSDK();
        $iamClient = new IamClient($param);
        try {
            $name = $user->name;
            $iamAWS = $iamClient->listUsers()['Users'];
            foreach ($iamAWS as $user) {
                if($name == $user['UserName']) {
                    $iamClient->deleteUser([
                        'UserName' => $name
                    ]);
                    break;
                }
            }
            parent::delete($id);
            return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }
}
