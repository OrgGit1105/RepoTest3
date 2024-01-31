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
use App\Models\VIAMUserPolicy;
use App\Repositories\Contracts\PolicyRepositoryInterface;
use Aws\Ssm\SsmClient;
use Carbon\Carbon;
use Helper\Common;
use Helper\ResponseService;
use Illuminate\Http\Response;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;

class PolicyRepository extends BaseRepository implements PolicyRepositoryInterface
{

     public function __construct(Application $app)
     {
         parent::__construct($app);

     }

    /**
       * Instantiate model
       *
       * @param Policy $model
       */

    public function model()
    {
        return Policy::class;
    }

    public function list($attributes)
    {
        return $this->model->get();
    }

    public function create(array $attributes)
    {
        try {
            if($attributes['type'] == POLICY_TYPE['EC2_admin'] || $attributes['type'] == POLICY_TYPE['EC2_deploy']) {
                $projectName = $attributes['project_name'];
                $instanceId = $attributes['instance_id'];
                $projects = $this->getListProject($instanceId);
                if(array_search($projectName, $projects) === false) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.project_do_not_existed'));
                }

                $isExisted = Policy::query()->where(Policy::TYPE, $attributes['type'])
                    ->where(Policy::PROJECT_NAME, $projectName)
                    ->where(Policy::INSTANCE_ID, $instanceId)
                    ->exists();
                if($isExisted) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.policy_existed'));
                }
            }
            $model = $this->model->create($attributes);
            return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
        return parent::create($attributes);
    }

    public function update(array $attributes, $id)
    {
        $policy = $this->model->find($id);
        if($policy == null) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.data_not_found'));
        }

        if(in_array($id, POLICY_V_FACE_ID)) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.update_fail'));
        }

        $typeEC2 = [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']];
        $policyTypeOld = $policy->type;
        $policyTypeNew = $attributes['type'];

        if(in_array($policyTypeOld, $typeEC2) || in_array($policyTypeNew, $typeEC2)) {
            $instanceOld = $policy->instance_id;
            $projectOld = $policy->project_name;
            $instanceNew = @$attributes['instance_id'];
            $projectNew = @$attributes['project_name'];

            if(in_array($policyTypeNew, $typeEC2)) {
                $projects = $this->getListProject($instanceNew);
                if(array_search($projectNew, $projects) === false) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.project_do_not_existed'));
                }

                $isExisted = $this->model->where(Policy::TYPE, $policyTypeNew)
                    ->where(Policy::PROJECT_NAME, $projectNew)
                    ->where(Policy::INSTANCE_ID, $instanceNew)
                    ->where('id', '!=', $id)
                    ->exists();
                if($isExisted) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.policy_existed'));
                }
            }

            if(config('app.env') === ENVIRONMENT_UPDATE && ($projectNew != $projectOld || $policyTypeNew != $policyTypeOld || $instanceOld != $instanceNew)) {
                if(in_array($policyTypeOld, $typeEC2) && !in_array($policyTypeNew, $typeEC2)) { //AWS => other
                    Common::deletePolicyUser($policy, true);
                } elseif (!in_array($policyTypeOld, $typeEC2) && in_array($policyTypeNew, $typeEC2)) { // other => AWS
                    CreatePolicyUserJob::dispatch($id,$policyTypeNew, $instanceNew, $projectNew);
                } else { //AWS <=> AWS
                    Common::deletePolicyUser($policy, false);
                    CreatePolicyUserJob::dispatch($id,$policyTypeNew, $instanceNew, $projectNew);
                }
            }
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::update($attributes, $id));
    }

    public function delete($id)
    {
        $policy = $this->model->find($id);
        if($policy == null) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.data_not_found'));
        }

        if(in_array($id, POLICY_V_FACE_ID)) {
            return ResponseService::responseJson(Response::HTTP_UNPROCESSABLE_ENTITY, null, trans('messages.mes.delete_fail'));
        }
        if(config('app.env') === ENVIRONMENT_UPDATE) {
            Common::deletePolicyUser($policy, true);
        }
        VIAMUserPolicy::query()->where(VIAMUserPolicy::POLICY_ID, $id)->delete();
        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
    }

    public function getListProject($instanceId)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);
        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => ["mkdir -p /var/www && cd /var/www && ls -d */"],
            ],
        ];
        $response = $ssmClient->sendCommand($parameters);
        $commandId = $response['Command']['CommandId'];

        $waitTime = 1;
        $maxAttempts = 10;
        $attempts = 0;
        $projects = [];
        do {
            sleep($waitTime);
            $output = $ssmClient->getCommandInvocation([
                'CommandId' => $commandId,
                'InstanceId' => $instanceId,
            ]);
            $status = $output['Status'];
            if($status == 'Success') {
                $outputs = explode("/\n", $output['StandardOutputContent']);
                $projects = array_filter($outputs, function ($value) {
                    return $value !== "" && $value !== "conf.d";
                });
            }
            $attempts++;
        } while ($status != 'Success' && $attempts <= $maxAttempts);
        return $projects;
    }
}
