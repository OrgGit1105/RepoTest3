<?php


namespace Helper;

use App\Models\VIAMUser;
use Aws\Ssm\SsmClient;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Common
{
    public function myPaginate($items, $perPage = 20, $page = null, $options = [])
    {
        $result = $this->paginate($items, $perPage, $page);

        return [
            'result' => array_values($result->all()),
            'pagination' => [
                'display' => $result->count(),
                'total_records' => $result->total(),
                'per_page' => $perPage,
                'current_page' => $result->currentPage(),
                'total_pages' => $result->lastPage()
            ]
        ];
    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function configAwsSDK()
    {
        if (!App::environment('local')) {
            $param = [
                'version' => 'latest',
                'region' => config('services.aws.AWS_DEFAULT_REGION')
            ];
        } else {
            $param = [
                'version' => 'latest',
                'region' => config('services.aws.AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => config('services.aws.AWS_ACCESS_KEY_ID'),
                    'secret' => config('services.aws.AWS_SECRET_ACCESS_KEY'),
                ]
            ];
        }
        return $param;
    }

    public function checkUserExist(SsmClient $ssmClient, $parameters, $instanceId, array $username) {
        $parameters['Parameters']['commands'] = ["ls /home"];
        $response = $ssmClient->sendCommand($parameters);
        $commandId = $response['Command']['CommandId'];
        $waitTime = 1;
        $maxAttempts = 10;
        $attempts = 0;

        do {
            sleep($waitTime);
            $output = $ssmClient->getCommandInvocation([
                'CommandId' => $commandId,
                'InstanceId' => $instanceId,
            ]);
            $status = $output['Status'];
            if($status == 'Success') {
                $names = explode("\n", $output['StandardOutputContent']);
                return array_diff($username, $names); //return [] if user existed
            }
            $attempts++;
        } while ($status != 'Success' && $attempts <= $maxAttempts);
    }

    public function createUserEc2($username, $publicKey, $viam_user_id)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);
        try {
            $policies = VIAMUser::query()->find($viam_user_id)->policies;
            $instanceData = [];
            foreach ($policies as $policy) {
                if ($policy->type == POLICY_TYPE['EC2_admin'] || $policy->type == POLICY_TYPE['EC2_deploy']) {
                    if (empty($publicKey)) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.ssh_key'));
                    }

                    $instanceData[$policy->instance_id][] = [
                        'type' => $policy->type,
                        'name' => $policy->name
                    ];
                }
            }
            foreach ($instanceData as $instanceId => $instance) {
                $parameters = [
                    'InstanceIds' => [$instanceId],
                    'DocumentName' => 'AWS-RunShellScript'
                ];

                $commands = [];
                if (self::checkUserExist($ssmClient, $parameters, $instanceId, [$username])) {
                    $commands[] = "sudo adduser $username";
                    $commands[] = "sudo -u $username mkdir -p /home/$username/.ssh";
                }
                $commands[] = "echo $publicKey | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null";
                $commandAdd = implode(' && ', $commands);

                foreach ($instance as $item) {
                    $groupName = $item['name'];
                    if ($item['type'] == POLICY_TYPE['EC2_admin']) {
                        $command[] = "echo '$username ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers";
                    } else {
                        $command [] = "sudo usermod -aG $groupName $username";
                    }
                }
                $parameters['Parameters']['commands'] = array_merge([$commandAdd], $command);
                $ssmClient->sendCommand($parameters);
            }
            return ResponseService::responseJson(CODE_SUCCESS);
        } catch (AwsException $e) {
            Log::error("AWS Exception: {$e->getMessage()}");
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }

    public function deleteUserEc2($user)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);
        try {
            $policies = $user->viam_user->policies;
            $instanceIds = [];
            $username = $user->name;
            foreach ($policies as $policy) {
                if ($policy->type == POLICY_TYPE['EC2_admin'] || $policy->type == POLICY_TYPE['EC2_deploy']) {
                    $instanceIds[] = $policy->instance_id;
                }
            }
            $instanceIds = array_unique($instanceIds);
            foreach ($instanceIds as $instanceId) {
                $parameters = [
                    'InstanceIds' => [$instanceId],
                    'DocumentName' => 'AWS-RunShellScript'
                ];

                if(!self::checkUserExist($ssmClient, $parameters, $instanceId, [$username])) {
                    $command[] = "echo '' | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null";
                    $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD: ALL/d' /etc/sudoers";
                    $command[] = "sudo pkill -u $username";
                    $command[] = "sudo userdel -r $username";
                    $parameters['Parameters']['commands'] = $command;
                    $ssmClient->sendCommand($parameters);
                }
            }
            return ResponseService::responseJson(CODE_SUCCESS);
        } catch (AwsException $e) {
            Log::error("AWS Exception: {$e->getMessage()}");
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }

    public function createGroupEc2($instanceId, $groupName, $projectName)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);

        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => [
                    "if ! grep -q \"^$groupName:\" /etc/group; then sudo groupadd $groupName; fi",
                    "sudo chown -R root:$groupName /var/www/$projectName", // thư mục thuộc về group, thuộc sở hữu của người dùng root
                    "sudo chmod -R 775 /var/www/$projectName", // các user thuộc group sẽ có quyền rwx với thư mục
                    "sudo chmod g+s /var/www/$projectName" // đảm bảo rằng tất cả các thư mục con được tạo trong đó sẽ kế thừa nhóm của thư mục gốc
                ],
            ],
        ];
        $ssmClient->sendCommand($parameters);
    }

    public function deletePolicyViamUser($viamUserId, $type, $instanceId, $project)
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);
        $viamUser = VIAMUser::query()->find($viamUserId);
        if($type == POLICY_TYPE['EC2_admin'] || $type == POLICY_TYPE['EC2_deploy']) {
            $parameters = [
                'InstanceIds' => [$instanceId],
                'DocumentName' => 'AWS-RunShellScript'
            ];
            $command = [];
            foreach ($viamUser->users as $user) {
                $username = $user->name;
                if ($type == POLICY_TYPE['EC2_admin']) {
                    $command[] = "sudo sed -i '/$username ALL=(ALL) NOPASSWD:\/usr\/bin\/\* \/var\/www\/$project\//d' /etc/sudoers";
                } else {
                    $command [] = "sudo sed -i '/$username ALL=(ALL) NOPASSWD:\/usr\/bin\/chmod 775 \/var\/www\/$project\//d' /etc/sudoers";
                    $command[] = "sudo sed -i '/$username ALL=(ALL) NOPASSWD:\/usr\/bin\/rm \/var\/www\/$project\//d' /etc/sudoers";
                }
            }
            $parameters['Parameters']['commands'] = $command;
            $ssmClient->sendCommand($parameters);
        }
    }
}
