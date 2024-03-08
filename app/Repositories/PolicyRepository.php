<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace Repository;

use App\Http\Resources\BaseResource;
use App\Models\Policy;
use App\Models\VIAMUser;
use App\Models\VIAMUserPolicy;
use App\Repositories\Contracts\PolicyRepositoryInterface;
use Aws\Ec2\Ec2Client;
use Aws\Exception\AwsException;
use Aws\Iam\IamClient;
use Aws\Ssm\SsmClient;
use Aws\Sts\StsClient;
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

    public function listAll($attributes)
    {
        return $this->model->get();
    }

    public function listOption()
    {
        return $this->model->where(Policy::TYPE, '!=', POLICY_TYPE['AWS'])->get();
    }

    public function create(array $attributes)
    {
        $type = $attributes['type'];
        try {
            if ($type == POLICY_TYPE['EC2_admin'] || $type == POLICY_TYPE['EC2_deploy']) {
                $instanceId = $attributes['instance_id'];
                $projectName = @$attributes['project_name'];
                $isExisted = $this->model
                    ->where(Policy::TYPE, $type)
                    ->when($type == POLICY_TYPE['EC2_deploy'], function ($query) use ($projectName) {
                        $query->where(Policy::PROJECT_NAME, $projectName);
                    })
                    ->where(Policy::INSTANCE_ID, $instanceId)
                    ->exists();
                if ($isExisted) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.policy_existed'));
                }

                if ($type == POLICY_TYPE['EC2_admin']) {
                    $attributes['project_name'] = null;
                }
                if ($type == POLICY_TYPE['EC2_deploy'] && config('app.env') === ENVIRONMENT_UPDATE) {
                    $projectName = $attributes['project_name'];
                    $instanceId = $attributes['instance_id'];
                    $projects = $this->getListData($instanceId, 'project');
                    $groups = $this->getListData($instanceId, 'group');
                    if (array_search($projectName, $projects) === false) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.project_do_not_existed'));
                    }
                    if (array_search($attributes['name'], $groups) !== false) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.name_existed'));
                    }
                    if ($attributes['name'] && $projectName) {
                        Common::createGroupEc2($instanceId, $attributes['name'], $projectName);
                    }
                }
            }

            if ($type == POLICY_TYPE['AWS']) {
                $arnRoleExist = $this->model->where(Policy::ARN_ROLE, $attributes['arn_role'])
                    ->where(Policy::TYPE, POLICY_TYPE['AWS'])
                    ->exists();
                if ($arnRoleExist) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.aws_existed'), trans('api.policy.aws_existed'));
                }
                $createAws = $this->createPolicyAws($attributes['arn_role']);
                if ($createAws->original['code'] != CODE_SUCCESS) {
                    return $createAws;
                }
            }
            if ($type != POLICY_TYPE['EC2_deploy']) {
                $attributes['project_name'] = null;
            }
            if (!in_array($type, [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])) {
                $attributes['instance_id'] = null;
            }
            if ($type != POLICY_TYPE['AWS']) {
                $attributes['arn_role'] = null;
            }
            $model = $this->model->create($attributes);
            return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }

    private function getInfoIamRoleSelf(Ec2Client $ec2Client, IamClient $iamClient)
    {
        $instanceIdSelf = 'i-0553d99830b279164';
        $result = $ec2Client->describeInstances();
        $instances = $result->get('Reservations');
        $arnIamRoleSelf = null;
        $ec2Exist = false;

        foreach ($instances as $instance) {
            if ($instance['Instances'][0]['InstanceId'] == $instanceIdSelf) {
                $ec2Exist = true;
                $arnIamRoleSelf = @$instance['Instances'][0]['IamInstanceProfile']['Arn'] ?? null;
                break;
            }
        }
        if (!$ec2Exist || !$arnIamRoleSelf) {
            return false;
        }

        $iamInstanceProfileArn = explode('/', $arnIamRoleSelf);
        $iamRoleSelf = end($iamInstanceProfileArn);
        $result = $iamClient->getRole([
            'RoleName' => $iamRoleSelf,
        ]);
        $currentTrustPolicy = json_decode(urldecode($result['Role']['AssumeRolePolicyDocument']), true);

        return [
            'iamRoleSelf' => $iamRoleSelf,
            'currentTrustPolicy' => $currentTrustPolicy
        ];
    }

    private function createPolicyAws($arnIamRoleAdd)
    {
        if (config('app.env') === ENVIRONMENT_UPDATE) {
            $param = Common::configAwsSDK();
            $ec2Client = new Ec2Client($param);
            $iamClient = new IamClient($param);
            try {
                $infoIamRoleSelf = $this->getInfoIamRoleSelf($ec2Client, $iamClient);
                if (!$infoIamRoleSelf) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.instance_id_not_found'), trans('api.policy.instance_id_not_found'));
                }

                $currentTrustPolicy = $infoIamRoleSelf['currentTrustPolicy'];
                $currentTrustPolicy['Statement'][] = [
                    'Effect' => 'Allow',
                    'Principal' => ['AWS' => $arnIamRoleAdd],
                    'Action' => 'sts:AssumeRole'
                ];
                $iamClient->updateAssumeRolePolicy([
                    'PolicyDocument' => json_encode($currentTrustPolicy),
                    'RoleName' => $infoIamRoleSelf['iamRoleSelf'],
                ]);
                return ResponseService::responseJson(CODE_SUCCESS);
            } catch (AwsException $e) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage(), $e->getMessage());
            }
        }
    }

    public function update(array $attributes, $id)
    {
        $policy = $this->model->find($id);
        if ($policy == null) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.data_not_found'));
        }

        if (in_array($id, POLICY_V_FACE_ID)) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.update_fail'));
        }

        $typeEC2 = [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']];
        $policyTypeOld = $policy->type;
        $policyTypeNew = $attributes['type'];
        if (in_array($policyTypeOld, $typeEC2) || in_array($policyTypeNew, $typeEC2)) {
            $updateEc2 = $this->updatePolicyEc2($id, $policy, $attributes);
            if ($updateEc2->original['code'] != CODE_SUCCESS) {
                return $updateEc2;
            }
        }

        if (in_array(POLICY_TYPE['AWS'], [$policyTypeOld, $policyTypeNew])) {
            $updateAws = $this->updatePolicyAws($policy, $attributes);
            if ($updateAws->original['code'] != CODE_SUCCESS) {
                return $updateAws;
            }
        }

        if($policyTypeNew == POLICY_TYPE['AWS'] && $policyTypeOld != POLICY_TYPE['AWS']) {
            VIAMUserPolicy::query()->where(VIAMUserPolicy::POLICY_ID, $id)->delete(); // loại policy có type là AWS ra khỏi viam_user
        }
        if ($attributes['type'] != POLICY_TYPE['EC2_deploy']) {
            $attributes['project_name'] = null;
        }
        if (!in_array($attributes['type'], [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])) {
            $attributes['instance_id'] = null;
        }
        if ($attributes['type'] != POLICY_TYPE['AWS']) {
            $attributes['arn_role'] = null;
        }
        return ResponseService::responseJson(CODE_SUCCESS, parent::update($attributes, $id));
    }

    public function updatePolicyEc2($id, $policy, $attributes)
    {
        $typeEC2 = [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']];
        $policyTypeOld = $policy->type;
        $policyTypeNew = $attributes['type'];
        if($policy->name != $attributes['name'] || $policyTypeOld != $policyTypeNew
            || $policy->instance_id != $attributes['instance_id'] || $policy->project_name != $attributes['project_name']) {
            $instanceOld = $policy->instance_id;
            $projectOld = $policy->project_name;
            $instanceNew = @$attributes['instance_id'];
            $projectNew = @$attributes['project_name'];
            $nameNew = $attributes['name'];
            $nameOld = $policy->name;

            if (in_array($policyTypeNew, $typeEC2)) {
                $isExisted = $this->model
                    ->where(Policy::TYPE, $policyTypeNew)
                    ->when($policyTypeNew == POLICY_TYPE['EC2_deploy'], function ($query) use ($projectNew) {
                        $query->where(Policy::PROJECT_NAME, $projectNew);
                    })
                    ->where(Policy::INSTANCE_ID, $instanceNew)
                    ->where('id', '!=', $id)
                    ->exists();
                if ($isExisted) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.policy_existed'));
                }

                if($policyTypeOld != $policyTypeNew) {
                    //case: tồn tại viam_user thuộc policy hiện tại có chứa 1 policy khác có type là EC2 nhưng khác type EC2 của policy hiện tại
                    // => không cho phép 1 viam_user vừa có quyền deploy, vừa có quyền admin trên cùng 1 instance
                    $typeToFind = array_diff($typeEC2, [$policyTypeNew]);
                    $isSame = VIAMUser::whereHas('policies', function ($query) use ($id, $typeToFind, $instanceNew) {
                        $query->where('policies.id', '!=', $id)
                            ->where('type', $typeToFind)
                            ->where(Policy::INSTANCE_ID, $instanceNew);
                    })->whereHas('policies', function ($query) use ($id) {
                        $query->where('policies.id', $id);
                    })->get();
                    if($isSame->count() > 0) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.viam_user.policy_id_ec2'));
                    }
                }

                if ($policyTypeNew == POLICY_TYPE['EC2_deploy'] && config('app.env') === ENVIRONMENT_UPDATE) {
                    $projects = $this->getListData($instanceNew, 'project');
                    if (array_search($projectNew, $projects) === false) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.project_do_not_existed'));
                    }
                }
            }

            if (config('app.env') === ENVIRONMENT_UPDATE) {
                $deleteAccountUser = $instanceOld != $instanceNew; //xóa luôn quyền ssh nếu đổi instanceId và policy là duy nhất trong nhóm quyền
                if (in_array(POLICY_TYPE['EC2_admin'], [$policyTypeNew, $policyTypeOld]) && !in_array(POLICY_TYPE['EC2_deploy'], [$policyTypeNew, $policyTypeOld])) {
                    if ($policyTypeOld != POLICY_TYPE['EC2_admin'] && $policyTypeNew == POLICY_TYPE['EC2_admin']) { // other -> admin: create admin
                        $this->createUserAdminOrDeployWithPolicy($policy, $instanceNew, $policyTypeNew, null, null);
                    }
                    elseif ($policyTypeOld == POLICY_TYPE['EC2_admin'] && $policyTypeNew != POLICY_TYPE['EC2_admin']) { //admin => other: delete
                        $this->deleteUserAdminOrDeployWithPolicy($policy, $instanceOld, true, POLICY_TYPE['EC2_admin']);
                    }
                    else { // admin <=> admin
                        $this->deleteUserAdminOrDeployWithPolicy($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_admin']);
                        sleep(2);
                        $this->createUserAdminOrDeployWithPolicy($policy, $instanceNew, $policyTypeNew, null, null);
                    }
                }
                if (in_array(POLICY_TYPE['EC2_deploy'], [$policyTypeNew, $policyTypeOld]) && !in_array(POLICY_TYPE['EC2_admin'], [$policyTypeNew, $policyTypeOld])) {
                    if ($policyTypeOld != POLICY_TYPE['EC2_deploy'] && $policyTypeNew == POLICY_TYPE['EC2_deploy']) { // other -> deploy: create group with user of group
                        $this->createUserAdminOrDeployWithPolicy($policy, $instanceNew, $policyTypeNew, $nameNew, $projectNew);
                    }
                    elseif ($policyTypeOld == POLICY_TYPE['EC2_deploy'] && $policyTypeNew != POLICY_TYPE['EC2_deploy']) { //deploy => other: delete group
                        $this->deleteUserAdminOrDeployWithPolicy($policy, $instanceOld, true, POLICY_TYPE['EC2_deploy']);
                    }
                    else { // deploy <=> deploy
                        if ($instanceNew == $instanceOld) { // only update name, project_name => update group/project
                            $groups = $this->getListData($instanceNew, 'group');
                            if (array_search($nameNew, $groups) !== false && $nameNew != $nameOld) {
                                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.name_existed'));
                            }
                            $this->updateGroupEc2($instanceNew, $projectOld, $projectNew, $nameOld, $nameNew);
                        } else {
                            $this->deleteUserAdminOrDeployWithPolicy($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_deploy']);
                            sleep(2);
                            $this->createUserAdminOrDeployWithPolicy($policy, $instanceNew, $policyTypeNew, $nameNew, $projectNew);
                        }
                    }
                }
                if ($policyTypeOld == POLICY_TYPE['EC2_deploy'] && $policyTypeNew == POLICY_TYPE['EC2_admin']) { // deploy => admin
                    $this->deleteUserAdminOrDeployWithPolicy($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_deploy']); //delete deploy
                    $this->createUserAdminOrDeployWithPolicy($policy, $instanceNew, $policyTypeNew, null, null); //create user admin
                }
                if ($policyTypeOld == POLICY_TYPE['EC2_admin'] && $policyTypeNew == POLICY_TYPE['EC2_deploy']) { //admin =>deploy
                    $this->deleteUserAdminOrDeployWithPolicy($policy, $instanceOld, $deleteAccountUser, POLICY_TYPE['EC2_admin']); //delete admin
                    $this->createUserAdminOrDeployWithPolicy($policy, $instanceNew, $policyTypeNew, $nameNew, $projectNew); //create user deploy
                }
            }
        }
        return ResponseService::responseJson(CODE_SUCCESS);
    }

    private function updatePolicyAws(Policy $policy, array $attributes)
    {
        if (config('app.env') === ENVIRONMENT_UPDATE && ($policy->type != $attributes['type'] || $policy->arn_role != $attributes['arn_role'])) {
            $arnRoleExist = $this->model->where(Policy::ARN_ROLE, $attributes['arn_role'])
                ->where(Policy::TYPE, POLICY_TYPE['AWS'])
                ->where('id', '!=', $policy->id)
                ->exists();
            if ($arnRoleExist) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.aws_existed'), trans('api.policy.aws_existed'));
            }

            $typeAws = POLICY_TYPE['AWS'];
            $arnIamRoleAdd = $attributes['arn_role'];
            $typeOld = $policy->type;
            $typeNew = $attributes['type'];
            $param = Common::configAwsSDK();
            $ec2Client = new Ec2Client($param);
            $iamClient = new IamClient($param);
            try {
                $infoIamRoleSelf = $this->getInfoIamRoleSelf($ec2Client, $iamClient);
                if (!$infoIamRoleSelf) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.instance_id_not_found'), trans('api.policy.instance_id_not_found'));
                }
                $currentTrustPolicy = $infoIamRoleSelf['currentTrustPolicy'];

                if ($typeOld != $typeAws && $typeNew == $typeAws) {//other => AWS
                    $currentTrustPolicy['Statement'][] = [
                        'Effect' => 'Allow',
                        'Principal' => ['AWS' => $arnIamRoleAdd],
                        'Action' => 'sts:AssumeRole'
                    ];
                } elseif ($typeOld == $typeAws && $typeNew != $typeAws) {//AWS => other
                    $policyDelete = [
                        'Effect' => 'Allow',
                        'Principal' => ['AWS' => $policy->arn_role],
                        'Action' => 'sts:AssumeRole'
                    ];
                    $currentTrustPolicy['Statement'] = array_filter($currentTrustPolicy['Statement'], function ($policy) use ($policyDelete) {
                        return $policy != $policyDelete;
                    });
                } else { //AWS -> AWS
                    $policyDelete = [
                        'Effect' => 'Allow',
                        'Principal' => ['AWS' => $policy->arn_role],
                        'Action' => 'sts:AssumeRole'
                    ];
                    $currentTrustPolicy['Statement'] = array_filter($currentTrustPolicy['Statement'], function ($policy) use ($policyDelete) {
                        return $policy != $policyDelete;
                    });
                    $currentTrustPolicy['Statement'][] = [
                        'Effect' => 'Allow',
                        'Principal' => ['AWS' => $arnIamRoleAdd],
                        'Action' => 'sts:AssumeRole'
                    ];
                }
                $iamClient->updateAssumeRolePolicy([
                    'PolicyDocument' => json_encode($currentTrustPolicy),
                    'RoleName' => $infoIamRoleSelf['iamRoleSelf'],
                ]);
            } catch (AwsException $e) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage(), $e->getMessage());
            }
        }
        return ResponseService::responseJson(CODE_SUCCESS);
    }

    public function delete($id)
    {
        $policy = $this->model->find($id);
        if ($policy == null) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.data_not_found'));
        }

        if (in_array($id, POLICY_V_FACE_ID)) {
            return ResponseService::responseJson(Response::HTTP_UNPROCESSABLE_ENTITY, null, trans('messages.mes.delete_fail'));
        }
        if (config('app.env') === ENVIRONMENT_UPDATE && in_array($policy->type, [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])) {
            $this->deleteUserAdminOrDeployWithPolicy($policy, $policy->instance_id, true, $policy->type); //delete admin
        }
        if (config('app.env') === ENVIRONMENT_UPDATE && $policy->type == POLICY_TYPE['AWS']) {
            $deleteAws = $this->deletePolicyAws($policy);
            if ($deleteAws->original['code'] != CODE_SUCCESS)
                return $deleteAws;
        }
        VIAMUserPolicy::query()->where(VIAMUserPolicy::POLICY_ID, $id)->delete();
        parent::delete($id);
        return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
    }

    public function getListData($instanceId, $typeList = 'project')
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);

        if ($typeList == 'project') {
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
            if ($status == 'Success') {
                if ($typeList == 'project') {
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
        if ($projectOld == $projectNew) {
            $commands[] = "sudo groupmod --new-name $groupNew $groupOld";
        } else {
            if ($projectOld) {
                $command[] = "sudo chown -R :apache /var/www/$projectOld";
                $command[] = "find /var/www/$projectOld -type d -name \"storage\" -exec chmod -R 777 {} \;";
                $command[] = "find /var/www/$projectOld -type d -name \".git\" -exec chmod -R 777 {} \;";
                $command[] = "sudo chmod g+s /var/www/$projectOld";
            }

            if ($groupNew && $groupOld && $projectNew) {
                $commands[] = "sudo groupmod --new-name $groupNew $groupOld";

                $command[] = "sudo chown -R :$groupNew /var/www/$projectNew";
                $command[] = "find /var/www/$projectNew -type d -name \"storage\" -exec chmod -R 777 {} \;";
                $command[] = "find /var/www/$projectNew -type d -name \".git\" -exec chmod -R 777 {} \;";
                $command[] = "sudo chmod g+s /var/www/$projectNew";
            }
        }

        $ssmClient->sendCommand([
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => $commands,
            ],
        ]);
    }

    private function deleteUserAdminOrDeployWithPolicy(Policy $policy, string $instanceId, $deleteAccountUser = false, int $typeAccount)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);
        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript'
        ];
        $command = [];
        $id = $policy->id;
        $usernames = [];
        foreach ($policy->viam_users as $viam) {
            foreach ($viam->users as $user) {
                $usernames[] = $user->name;
            }
        }

        $userNotExists = Common::checkUserExist($ssmClient, $parameters, $instanceId, $usernames);
        $userExists = $usernames;
        if ($userNotExists) {
            $userExists = array_diff($usernames, $userNotExists);
        }
        // trường hợp policy bị xóa là policy có type = EC2 duy nhất trong VIAM_USER liên quan đến policy bị xóa
        // => xóa tài khoản user trên EC2
        if ($deleteAccountUser) {
            $viamUserOfPolicy = VIAMUser::query()
                ->whereHas('policies', function ($e) use ($id) {
                    $e->where('policies.id', $id);
                })->get();
            foreach ($viamUserOfPolicy as $viamUser) {
                $viamUserOfPolicyEC2Other = VIAMUser::query()->where('id', $viamUser->id)
                    ->whereHas('policies', function ($e) use ($id, $instanceId) {
                        $e->where('policies.id', '!=', $id)
                            ->where(Policy::INSTANCE_ID, $instanceId)
                            ->whereIn(Policy::TYPE, [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']]);
                    })->exists();
                $userOfViamUser = $viamUser->users->pluck('name')->toArray();
                $userDel = array_intersect($userOfViamUser, $userExists);
                if ($viamUserOfPolicyEC2Other && $typeAccount == POLICY_TYPE['EC2_admin']) { // chỉ xóa quyền admin của tk, ko xóa tk
                    foreach ($userDel as $name) {
                        $command[] = "sudo sed -i '/^$name ALL=(ALL) NOPASSWD: ALL/d' /etc/sudoers";
                    }
                }
                if (!$viamUserOfPolicyEC2Other) {
                    foreach ($userDel as $nameDel) {
                        $command[] = "echo '' | sudo -u $nameDel tee /home/$nameDel/.ssh/authorized_keys > /dev/null";
                        $command[] = "sudo sed -i '/^$nameDel ALL=(ALL) NOPASSWD:/d' /etc/sudoers";
                        $command[] = "sudo pkill -u $nameDel";
                        $command[] = "sudo userdel -r $nameDel";
                    }
                }
            }
        } else {
            if ($typeAccount == POLICY_TYPE['EC2_admin']) {
                foreach ($userExists as $username) {
                    $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD: ALL/d' /etc/sudoers";
                }
            }
        }

        if ($typeAccount == POLICY_TYPE['EC2_deploy']) {
            $projectName = $policy->project_name;
            $groupName = $policy->name;
            if ($projectName && $groupName) {
                $command[] = "sudo chown -R :apache /var/www/$projectName";
                $command[] = "find /var/www/$projectName -type d -name \"storage\" -exec chmod -R 777 {} \;";
                $command[] = "find /var/www/$projectName -type d -name \".git\" -exec chmod -R 777 {} \;";
                $command[] = "sudo chmod g+s /var/www/$projectName";
                $command[] = "sudo groupdel $groupName";
            }
        }
        if ($command) {
            $parameters['Parameters']['commands'] = $command;
            $ssmClient->sendCommand($parameters);
        }
    }

    private function createUserAdminOrDeployWithPolicy($policy, $instanceId, $type, $groupName = null, $projectName = null)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);

        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript'
        ];
        $command = [];
        $names = [];
        $sshKey = [];
        $githubGmail = [];
        $isUserDeploy = false;
        if ($type == POLICY_TYPE['EC2_deploy'] && $groupName && $projectName) {
            Common::createGroupEc2($instanceId, $groupName, $projectName);
        }
        foreach ($policy->viam_users as $viam) {
            foreach ($viam->users as $user) {
                $username = $user->name;
                $names[] = $username;
                $sshKey[$username] = $user->ssh_public_key;
                $githubGmail[$username] = $user->github_gmail;
                if ($type == POLICY_TYPE['EC2_admin']) {
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers";
                } else {
                    $isUserDeploy = true;
                    $command [] = "sudo usermod -aG $groupName $username";
                }
            }
        }
        $userNotExists = Common::checkUserExist($ssmClient, $parameters, $instanceId, $names);
        if (!empty($userNotExists)) {
            $nodePath = Common::getNodePath($instanceId);
            foreach ($userNotExists as $userNotExist) {
                $commands = [];
                $commands[] = "sudo adduser $userNotExist";
                $commands[] = "sudo -u $userNotExist mkdir -p /home/$userNotExist/.ssh";
                $commands[] = "echo $sshKey[$userNotExist] | sudo -u $userNotExist tee /home/$userNotExist/.ssh/authorized_keys > /dev/null";
                $commands[] =  "sudo -u $userNotExist ssh-keygen -t rsa -b 4096 -C \"$githubGmail[$userNotExist]\" -N \"\" -f \"/home/$userNotExist/.ssh/id_rsa\" > /dev/null";
                $commandNode = !empty($nodePath) ? ["grep -qxF 'export PATH=\"$nodePath:\$PATH\"' /home/$userNotExist/.bashrc || echo 'export PATH=\"$nodePath:\$PATH\"' | sudo tee -a /home/$userNotExist/.bashrc"] : [];
                $commandSudo = $isUserDeploy ? Common::addCommandSudo($userNotExist) : [];
                $parameters['Parameters']['commands'] = array_merge($commands, $commandNode, $commandSudo);
                $ssmClient->sendCommand($parameters);
            }
        }

        if($command) {
            $parameters['Parameters']['commands'] = $command;
            $ssmClient->sendCommand($parameters);
        }
    }

    private function deletePolicyAws(Policy $policy)
    {
        $param = Common::configAwsSDK();
        $ec2Client = new Ec2Client($param);
        $iamClient = new IamClient($param);
        try {
            $infoIamRoleSelf = $this->getInfoIamRoleSelf($ec2Client, $iamClient);
            if (!$infoIamRoleSelf) {
                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.instance_id_not_found'), trans('api.policy.instance_id_not_found'));
            }
            $currentTrustPolicy = $infoIamRoleSelf['currentTrustPolicy'];

            $policyDelete = [
                'Effect' => 'Allow',
                'Principal' => ['AWS' => $policy->arn_role],
                'Action' => 'sts:AssumeRole'
            ];
            $currentTrustPolicy['Statement'] = array_filter($currentTrustPolicy['Statement'], function ($policy) use ($policyDelete) {
                return $policy != $policyDelete;
            });

            $iamClient->updateAssumeRolePolicy([
                'PolicyDocument' => json_encode($currentTrustPolicy),
                'RoleName' => $infoIamRoleSelf['iamRoleSelf'],
            ]);
            return ResponseService::responseJson(CODE_SUCCESS);
        } catch (AwsException $e) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage(), $e->getMessage());
        }
    }
}
