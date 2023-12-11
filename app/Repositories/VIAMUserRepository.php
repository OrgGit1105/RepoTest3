<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace Repository;

use App\Http\Resources\BaseResource;
use App\Models\Emotion;
use App\Models\VIAMUser;
use App\Models\VIAMUserPolicy;
use App\Repositories\Contracts\VIAMUserRepositoryInterface;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;

class VIAMUserRepository extends BaseRepository implements VIAMUserRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param VIAMUser $model
       */

    public function model()
    {
        return VIAMUser::class;
    }

    public function list($attributes)
    {
        if(@$attributes['page'] || @$attributes['per_page']) {
            return $this->model->paginate($attributes['per_page']);
        }
        return $this->model->select('id', 'name')->get();
    }

    public function create(array $attributes)
    {
        $policies = array_unique($attributes['policy_id']);
        if(count(array_intersect(POLICY_V_FACE_ID, $policies)) >= 2) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.viam_user.policy_id'));
        }
        $model = $this->model->create($attributes);
        foreach ($policies as $policy) {
            VIAMUserPolicy::create([
                VIAMUserPolicy::VIAM_USER_ID => $model->id,
                VIAMUserPolicy::POLICY_ID => $policy
            ]);
        }
        $model->load('policies');
        return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
    }

    public function update(array $attributes, $id)
    {
        $policies = array_unique($attributes['policy_id']);
        if(count(array_intersect(POLICY_V_FACE_ID, $policies)) >= 2) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.viam_user.policy_id'));
        }
        $model = parent::update($attributes, $id);
        VIAMUserPolicy::query()->where(VIAMUserPolicy::VIAM_USER_ID, $id)->delete();
        foreach ($policies as $policy) {
            VIAMUserPolicy::create([
                VIAMUserPolicy::VIAM_USER_ID => $id,
                VIAMUserPolicy::POLICY_ID => $policy
            ]);
        }
        $model->load('policies');
        return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
    }
}
