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
use Aws\Iam\IamClient;
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

    private function checkProjectExist($instanceId, $projectName)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);

        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => ["cd /var/www && ls"],
            ],
        ];
        $response = $ssmClient->sendCommand($parameters);
        $commandId = $response['Command']['CommandId'];

        $waitTime = 1;
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $output = $ssmClient->getCommandInvocation([
                'CommandId' => $commandId,
                'InstanceId' => $instanceId,
            ]);
            $status = $output['Status'];
            if($status == 'Success') {
                $projects = explode("\n", $output['StandardOutputContent']);
                if(array_search($projectName, $projects)) {
                    return true;
                }
            }
            sleep($waitTime);
            $attempts++;
        } while ($status != 'Success' && $attempts <= $maxAttempts);
        return false;
    }

    public function create(array $attributes)
    {
//        $param = Common::configAwsSDK();
//        $iamClient = new IamClient($param);

        try {
            if($attributes['type'] == POLICY_TYPE['AWS_admin'] || $attributes['type'] == POLICY_TYPE['AWS_deploy']) {
                $instanceId = $attributes['instance_id'];
                if(!$this->checkProjectExist($instanceId, $attributes['project_name'])) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.project_do_not_existed'));
                }

                $isExisted = Policy::query()->where(Policy::TYPE, $attributes['type'])
                    ->where(Policy::PROJECT_NAME, $attributes['project_name'])
                    ->where(Policy::INSTANCE_ID, $attributes['instance_id'])
                    ->exists();
                if($isExisted) {
                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.policy_existed'));
                }
            }

//            $iamAWS = $iamClient->listPolicies()['Policies'];
//            if ($this->isPolicyNameExisted($attributes['name'], $iamAWS)) {
//                return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.name_existed'));
//            }
//
//            $policyDocument = $this->generatePolicyDocument($attributes, $param);
//
//            $result = $iamClient->createPolicy([
//                'PolicyName' => $attributes['name'],
//                'PolicyDocument' => json_encode($policyDocument),
//            ]);
//
//            $attributes[Policy::POLICY_ARN] = $result['Policy']['Arn'];
            $model = $this->model->create($attributes);
            return ResponseService::responseJson(CODE_SUCCESS, new BaseResource($model));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
        return parent::create($attributes);
    }

    private function isPolicyNameExisted($name, $policies)
    {
        foreach ($policies as $policy) {
            if ($name == $policy['PolicyName']) {
                return true;
            }
        }
        return false;
    }

    private function generatePolicyDocument($attributes, $param)
    {
        if (!empty($attributes['instance_id'])) {
            if ($attributes['type'] == POLICY_TYPE['AWS_admin']) {
                return [
                    'Version' => '2012-10-17',
                    'Statement' => [
                        [
                            "Effect" => "Allow",
                            "Action" => "ec2:*",
                            "Resource" => "*"
                        ]
                    ]
                ];
            }
            if ($attributes['type'] == POLICY_TYPE['AWS_deploy']) {
                return [
                    'Version' => '2012-10-17',
                    'Statement' => [
                        [
                            "Effect" => 'Allow',
                            "Action" => [
                                'ec2:RunInstances',
                                'ec2:DescribeInstances',
                            ],
                            "Resource" => ['arn:aws:ec2:' . $param['region'] . ':*:instance/' . $attributes['instance_id']]
                        ]
                    ]
                ];
            }
        }

        return [
            'Version' => '2012-10-17',
            'Statement' => [
                [
                    "Action" => [
                        "s3:Get*",
                        "s3:List*"
                    ],
                    "Resource" => "arn:aws:s3:::*"
                ]
            ]
        ];
    }

    public function update(array $attributes, $id)
    {
        if(in_array($id, POLICY_V_FACE_ID)) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.update_fail'));
        }

        $policy = $this->model->find($id);
        if($policy == null) {
            return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('messages.mes.data_not_found'));
        }

        $typeAws = [POLICY_TYPE['AWS_admin'], POLICY_TYPE['AWS_deploy']];
        $policyTypeOld = $policy->type;
        $policyTypeNew = $attributes['type'];

        if(in_array($policyTypeOld, $typeAws) || in_array($policyTypeNew, $typeAws)) {
            $instanceOld = $policy->instance_id;
            $projectOld = $policy->project_name;
            $instanceNew = @$attributes['instance_id'];
            $projectNew = @$attributes['project_name'];

            if(in_array($policyTypeNew, $typeAws)) {
                if(!$this->checkProjectExist($instanceNew, $projectNew)) {
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

            if(($projectNew != $projectOld) || ($policyTypeNew != $policyTypeOld) || ($instanceOld != $instanceNew)) {
                if(in_array($policyTypeOld, $typeAws) && !in_array($policyTypeNew, $typeAws)) { //AWS => other
                    Common::deletePolicyUser($id, $instanceOld, $projectOld);
                } elseif (!in_array($policyTypeOld, $typeAws) && in_array($policyTypeNew, $typeAws)) { // other => AWS
                    CreatePolicyUserJob::dispatch($id,$policyTypeNew, $instanceNew, $projectNew);
                } else { //AWS <=> AWS
                    Common::deletePolicyUser($id, $instanceOld, $projectOld);
                    CreatePolicyUserJob::dispatch($id,$policyTypeNew, $instanceNew, $projectNew);
                }
            }
        }

        try {
//            $param = Common::configAwsSDK();
//            $iamClient = new IamClient($param);
//            $iamAWS = $iamClient->listPolicies()['Policies'];
//
//            if($policy->name != $attributes['name']) {
//                if ($this->isPolicyNameExisted($attributes['name'], $iamAWS)) {
//                    return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.policy.name_existed'));
//                }
//            }
//
//            $policyDocument = $this->generatePolicyDocument($attributes, $param);
//            $iamClient->createPolicyVersion([
//                'PolicyArn' => $policy->policy_arn,
//                'PolicyDocument' => json_encode($policyDocument),
//                'SetAsDefault' => true,
//            ]);

            return parent::update($attributes, $id); // TODO: Change the autogenerated stub
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }

    public function delete($id)
    {
        if(in_array($id, POLICY_V_FACE_ID)) {
            return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_fail'));
        }

        $policy = Policy::query()->find($id);
        Common::deletePolicyUser($id, $policy->instance_id, $policy->project_name);
        $param = Common::configAwsSDK();
//        $iamClient = new IamClient($param);

        try {
//            $policyArn = $this->model->find($id)->policy_arn;
//            $iamAWS = $iamClient->listPolicies()['Policies'];
//            foreach ($iamAWS as $policy) {
//                if($policyArn == $policy['Arn']) {
//                    $iamClient->deletePolicyAsync([
//                        'PolicyArn' => $policyArn,
//                    ]);
//                    break;
//                }
//            }
            VIAMUserPolicy::query()->where(VIAMUserPolicy::POLICY_ID, $id)->delete();
            parent::delete($id);
            return ResponseService::responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
        } catch (AwsException $e) {
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }
}
