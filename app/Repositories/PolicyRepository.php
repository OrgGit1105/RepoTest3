<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace Repository;

use App\Http\Resources\BaseResource;
use App\Jobs\CreateUserAdminOrDeployWithPolicyJob;
use App\Jobs\DeleteUserAdminOrDeployWithPolicyJob;
use App\Models\Policy;
use App\Models\VIAMUserPolicy;
use App\Repositories\Contracts\PolicyRepositoryInterface;
use Aws\Ssm\SsmClient;
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
        $type = $attributes['type'];
        try {
            if($type == POLICY_TYPE['EC2_admin'] || $type == POLICY_TYPE['EC2_deploy']) {
                $instanceId = $attributes['instance_id'];
                $projectName = @$attributes['project_name'];
                $isExisted = $this->model
                    ->where(Policy::TYPE, $type)
                    ->when($type == POLICY_TYPE['EC2_deploy'], function ($query) use ($projectName) {
                        $query->where(Policy::PROJECT_NAME, $projectName);
                    })
                    ->where(Policy::INSTANCE_ID, $instanceId)
                    ->exists();
                if($isExisted) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.policy_existed'));
                }

                if($type == POLICY_TYPE['EC2_admin']) {
                    $attributes['project_name'] = null;
                }
                if($type == POLICY_TYPE['EC2_deploy'] && config('app.env') === ENVIRONMENT_UPDATE) {
                    $projectName = $attributes['project_name'];
                    $instanceId = $attributes['instance_id'];
                    $projects = $this->getListData($instanceId, 'project');
                    $groups = $this->getListData($instanceId, 'group');
                    if(array_search($projectName, $projects) === false) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.project_do_not_existed'));
                    }
                    if(array_search($attributes['name'], $groups) !== false) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.name_existed'));
                    }
                    Common::createGroupEc2($instanceId, $attributes['name'], $projectName);
                }
            } else {
                $attributes['project_name'] = null;
                $attributes['instance_id'] = null;
            }
            $model = $this->model->create($attributes);
            return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
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

        if($policy->name != $attributes['name'] || $policy->type != $attributes['type']
            || $policy->instance_id != $attributes['instance_id'] || $policy->project_name != $attributes['project_name']) {
            $updateEc2 = $this->updatePolicyEc2($id, $policy, $attributes);
            if($updateEc2->original['code'] != CODE_SUCCESS) {
                return $updateEc2;
            }
        }
        if($attributes['type'] == POLICY_TYPE['EC2_admin']) {
            $attributes['project_name'] = null;
        }
        if(!in_array($attributes['type'], [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])) {
            $attributes['project_name'] = null;
            $attributes['instance_id'] = null;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::update($attributes, $id));
    }

    public function updatePolicyEc2($id, $policy, $attributes)
    {
        $typeEC2 = [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']];
        $policyTypeOld = $policy->type;
        $policyTypeNew = $attributes['type'];
        if(in_array($policyTypeOld, $typeEC2) || in_array($policyTypeNew, $typeEC2)) {
            $instanceOld = $policy->instance_id;
            $projectOld = $policy->project_name;
            $instanceNew = @$attributes['instance_id'];
            $projectNew = @$attributes['project_name'];
            $nameNew = $attributes['name'];
            $nameOld = $policy->name;

            if(in_array($policyTypeNew, $typeEC2)) {
                $isExisted = $this->model
                    ->where(Policy::TYPE, $policyTypeNew)
                    ->when($policyTypeNew == POLICY_TYPE['EC2_deploy'], function ($query) use ($projectNew) {
                        $query->where(Policy::PROJECT_NAME, $projectNew);
                    })
                    ->where(Policy::INSTANCE_ID, $instanceNew)
                    ->where('id', '!=', $id)
                    ->exists();
                if($isExisted) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.policy_existed'));
                }

                if($policyTypeNew == POLICY_TYPE['EC2_deploy'] && config('app.env') === ENVIRONMENT_UPDATE) {
                    $projects = $this->getListData($instanceNew, 'project');
                    if(array_search($projectNew, $projects) === false) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.project_do_not_existed'));
                    }
                }
            }

            if(config('app.env') === ENVIRONMENT_UPDATE) {
                $deleteAccountUser = $instanceOld != $instanceNew; //xóa luôn quyền ssh nếu đổi instanceId và policy là duy nhất trong nhóm quyền
                if(in_array(POLICY_TYPE['EC2_admin'], [$policyTypeNew, $policyTypeOld]) && !in_array(POLICY_TYPE['EC2_deploy'], [$policyTypeNew, $policyTypeOld])) {
                    if($policyTypeOld != POLICY_TYPE['EC2_admin'] && $policyTypeNew == POLICY_TYPE['EC2_admin']) { // other -> admin: create admin
                        CreateUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceNew, $policyTypeNew, null, null);
                    }
                    elseif ($policyTypeOld == POLICY_TYPE['EC2_admin'] && $policyTypeNew != POLICY_TYPE['EC2_admin']) { //admin => other: delete
                        DeleteUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceOld, true, POLICY_TYPE['EC2_admin']);
                    }
                    else { // admin <=> admin
                        DeleteUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_admin']);
                        sleep(2);
                        CreateUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceNew, $policyTypeNew, null, null);
                    }
                }
                if (in_array(POLICY_TYPE['EC2_deploy'], [$policyTypeNew, $policyTypeOld]) && !in_array(POLICY_TYPE['EC2_admin'], [$policyTypeNew, $policyTypeOld])) {
                    if($policyTypeOld != POLICY_TYPE['EC2_deploy'] && $policyTypeNew == POLICY_TYPE['EC2_deploy']) { // other -> deploy: create group with user of group
                        CreateUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceNew, $policyTypeNew, $nameNew, $projectNew);
                    }
                    elseif ($policyTypeOld == POLICY_TYPE['EC2_deploy'] && $policyTypeNew != POLICY_TYPE['EC2_deploy']) { //deploy => other: delete group
                        DeleteUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceOld, true, POLICY_TYPE['EC2_deploy']);
                    }
                    else { // deploy <=> deploy
                        if($instanceNew == $instanceOld) { // only update name, project_name => update group/project
                            $groups = $this->getListData($instanceNew, 'group');
                            if(array_search($nameNew, $groups) !== false && $nameNew != $nameOld) {
                                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.name_existed'));
                            }
                            $this->updateGroupEc2($instanceNew, $projectOld, $projectNew, $nameOld, $nameNew);
                        } else {
                            DeleteUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_deploy']);
                            sleep(2);
                            CreateUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceNew, $policyTypeNew, $nameNew, $projectNew);
                        }
                    }
                }
                if ($policyTypeOld == POLICY_TYPE['EC2_deploy'] && $policyTypeNew == POLICY_TYPE['EC2_admin']) { // deploy => admin
                    DeleteUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_deploy']); //delete deploy
                    CreateUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceNew, $policyTypeNew, null, null); //create user admin
                }
                if ($policyTypeOld == POLICY_TYPE['EC2_admin'] && $policyTypeNew == POLICY_TYPE['EC2_deploy']) { //admin =>deploy
                    DeleteUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_admin']); //delete admin
                    CreateUserAdminOrDeployWithPolicyJob::dispatch($policy, $instanceNew, $policyTypeNew, $nameNew, $projectNew); //create user deploy
                }
            }
        }
        return ResponseService::responseJson(CODE_SUCCESS);
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
        if(config('app.env') === ENVIRONMENT_UPDATE && in_array($policy->type, [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])) {
            DeleteUserAdminOrDeployWithPolicyJob::dispatch($policy, $policy->instance_id, true, $policy->type); //delete admin
        }
        VIAMUserPolicy::query()->where(VIAMUserPolicy::POLICY_ID, $id)->delete();
        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
    }

    public function getListData($instanceId, $typeList = 'project')
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);

        if($typeList == 'project') {
            $command = "mkdir -p /var/www && cd /var/www && ls -d */";
        } else {
            $command = "cat /etc/group";
        }

        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => [$command],
            ],
        ];
        $response = $ssmClient->sendCommand($parameters);
        $commandId = $response['Command']['CommandId'];

        $waitTime = 1;
        $maxAttempts = 10;
        $attempts = 0;
        $data = [];
        do {
            sleep($waitTime);
            $output = $ssmClient->getCommandInvocation([
                'CommandId' => $commandId,
                'InstanceId' => $instanceId,
            ]);
            $status = $output['Status'];
            if($status == 'Success') {
                if($typeList == 'project') {
                    $outputs = explode("/\n", $output['StandardOutputContent']);
                    $data = array_filter($outputs, function ($value) {
                        return $value !== "" && $value !== "conf.d";
                    });
                } else {
                    $lines = explode("\n", $output['StandardOutputContent']);
                    $data = array_map(function ($line) {
                        $colonPos = strpos($line, ':');
                        return trim(substr($line, 0, $colonPos));
                    }, $lines);
                }
            }
            $attempts++;
        } while ($status != 'Success' && $attempts <= $maxAttempts);
        return $data;
    }

    private function updateGroupEc2($instanceId, $projectOld, $projectNew, $groupOld, $groupNew)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);
        $commands = [];
        if($projectOld == $projectNew) {
            $commands[] = "sudo groupmod --new-name $groupNew $groupOld";
        } else {
            $command[] = "sudo chown -R :root /var/www/$projectOld";
            $command[] = "sudo chmod -R 775 /var/www/$projectOld";
            $command[] = "sudo chmod -R 777 /var/www/$projectOld/storage/";
            $command[] = "sudo chmod -R 777 /var/www/$projectOld/.git/";
            $command[] = "sudo chmod g+s /var/www/$projectOld";

            $commands[] = "sudo groupmod --new-name $groupNew $groupOld";

            $command[] = "sudo chown -R :$groupNew /var/www/$projectNew";
            $command[] = "sudo chmod -R 775 /var/www/$projectNew";
            $command[] = "sudo chmod -R 777 /var/www/$projectNew/storage/";
            $command[] = "sudo chmod -R 777 /var/www/$projectNew/.git/";
            $command[] = "sudo chmod g+s /var/www/$projectNew";
        }

        $ssmClient->sendCommand([
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => $commands,
            ],
        ]);
    }
}
