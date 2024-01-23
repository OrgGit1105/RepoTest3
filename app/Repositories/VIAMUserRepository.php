<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace Repository;

use App\Http\Resources\BaseResource;
use App\Jobs\CreatePolicyUserJob;
use App\Models\Policy;
use App\Models\User;
use App\Models\VIAMUser;
use App\Models\VIAMUserPolicy;
use App\Repositories\Contracts\VIAMUserRepositoryInterface;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

    public function list()
    {
        $items = $this->model->with('policies')->get();
        $dataItems = [];
        foreach ($items as $item) {
            $policyName = '';
            foreach ($item->policies as $policy) {
                $policyName .= $policy->name . ', ';
            }
            $dataItems[] = [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'policy_list' => trim($policyName, ', '),
            ];
        }
        return collect($dataItems);
    }

    private function checkPolicy($policies)
    {
        if(count(array_intersect(POLICY_V_FACE_ID, $policies)) >= 2) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.viam_user.policy_id'));
        }

        $isSame = Policy::whereIn('id', $policies)
            ->select('project_name', 'instance_id', DB::raw('COUNT(*) as count'))
            ->whereIn('type', [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])
            ->groupBy('project_name', 'instance_id')
            ->having('count', '>', 1)
            ->exists();
        if($isSame) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.viam_user.policy_id'));
        }
        return ResponseService::responseJson(CODE_SUCCESS);
    }

    public function create(array $attributes)
    {
        $policies = array_unique($attributes['policy_id']);
        $check = $this->checkPolicy($policies);
        if($check->original['code'] != CODE_SUCCESS) {
            return $check;
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
        $viamUser = $this->model->find($id);
        if($viamUser == null) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.data_not_found'));
        }

        $policies = array_unique($attributes['policy_id']);
        $check = $this->checkPolicy($policies);
        if($check->original['code'] != CODE_SUCCESS) {
            return $check;
        }

        $oldPolicies = VIAMUserPolicy::query()->where(VIAMUserPolicy::VIAM_USER_ID, $id)->pluck(VIAMUserPolicy::POLICY_ID)->toArray();
        $addPolicies = array_diff($policies, $oldPolicies);
        $removePolicies = array_diff($oldPolicies, $policies);
        foreach ($removePolicies as $removePolicy) {
            $policy = Policy::query()->find($removePolicy);
            Common::deletePolicyUser($id, $policy->instance_id, $policy->project_name);
        }

        foreach ($addPolicies as $addPolicy) {
            $policy = Policy::query()->find($addPolicy);
            CreatePolicyUserJob::dispatch($id, $policy->type, $policy->instance_id, $policy->project_name);
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

    public function delete($id)
    {
        $user = User::where(User::VIAM_USER_ID, $id)->count();
        if($user > 0) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.viam_user.cannot_delete'));
        }

        VIAMUserPolicy::query()->where(VIAMUserPolicy::VIAM_USER_ID, $id)->delete();
        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
    }
}
