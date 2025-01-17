<?php


namespace Helper;

use App\Models\Policy;
use App\Models\RDSManager;
use App\Models\User;
use App\Models\VIAMUser;
use Aws\Ssm\SsmClient;
use Aws\Sts\StsClient;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public static function configAwsSDK($instanceId = null)
    {
        if (!App::environment('local')) {
            $param = [
                'version' => 'latest',
                'region' => config('services.aws.AWS_DEFAULT_REGION')
            ];

            if($instanceId) { // case: access a server other than server 240
                $assumeRole = Policy::query()->where(Policy::TYPE, POLICY_TYPE['AWS'])
                    ->where(Policy::INSTANCE_ID, $instanceId)
                    ->first();
                if($assumeRole) {
                    $stsClient = new StsClient($param);

                    // Assume IAM role atmtc để lấy temporary credentials
                    $assumeRoleResult = $stsClient->assumeRole([
                        'RoleArn' => $assumeRole->arn_role,
                        'RoleSessionName' => 'VFaceSession'
                    ]);

                    // Lấy temporary credentials từ AssumeRoleResult
                    $credentials = $assumeRoleResult['Credentials'];
                    $param = [
                        'version' => 'latest',
                        'region' => config('services.aws.AWS_DEFAULT_REGION'),
                        'credentials' => [
                            'key' => $credentials['AccessKeyId'],
                            'secret' => $credentials['SecretAccessKey'],
                            'token' => $credentials['SessionToken']
                        ]
                    ];
                }
            }
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

    public function checkUserExist(SsmClient $ssmClient, $parameters, $instanceId, array $username)
    {
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
            if ($status == 'Success') {
                $names = explode("\n", $output['StandardOutputContent']);
                return array_diff($username, $names); //return [] if user existed
            }
            $attempts++;
        } while ($status != 'Success' && $attempts <= $maxAttempts);
    }

    public function getNodePath($instanceId)
    {
        $path = '';
        switch ($instanceId) {
            case INSTANCE_ID_240:
                $path = '/home/ec2-user/.nvm/versions/node/v14.5.0/bin/node'; //node của 240
                break;
            case INSTANCE_ID_142:
            case INSTANCE_ID_223:
            case INSTANCE_ID_22:
                $path = '/usr/bin/node';
                break;
            case INSTANCE_ID_176:
                $path = '/home/ec2-user/.nvm/versions/node/v14.21.3/bin/node';
                break;
            case INSTANCE_ID_235:
                $path = '/home/ec2-user/.nvm/versions/node/v20.14.0/bin/node';
                break;
        }

        return $path;
    }

    public function addCommandSudo($username)
    {
        return [
            "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/chmod,/usr/bin/yum,/usr/bin/systemctl' >> /etc/sudoers",
            "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/cp /etc/httpd/conf.d/*' >> /etc/sudoers",
            "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/rm /etc/httpd/conf.d/*i' >> /etc/sudoers",
            "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/systemctl restart httpd.service' >> /etc/sudoers",
            "echo '$username ALL=(ALL) NOPASSWD:/usr/sbin/service httpd restart' >> /etc/sudoers",
            "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/vim' >> /etc/sudoers",
            "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/certbot' >> /etc/sudoers",
            "echo '' >> /etc/sudoers"
        ];
    }

    public function createUserEc2($username, $publicKey, $viam_user_id, $gmailGithub)
    {
        try {
            $policies = VIAMUser::query()->find($viam_user_id)->policies;
            $instanceData = [];
            foreach ($policies as $policy) {
                if ($policy->type == POLICY_TYPE['EC2_admin'] || $policy->type == POLICY_TYPE['EC2_deploy']) {
                    if (empty($publicKey) || empty($gmailGithub)) {
                        return ResponseService::responseJsonError(Response::HTTP_UNPROCESSABLE_ENTITY, trans('api.user.ssh_key_and_gmail_github'));
                    }

                    $instanceData[$policy->instance_id][] = [
                        'type' => $policy->type,
                        'name' => $policy->name
                    ];
                }
            }
            foreach ($instanceData as $instanceId => $instance) {
                if($instanceId != INSTANCE_ID_240) {
                    $param = Common::configAwsSDK($instanceId);
                } else {
                    $param = Common::configAwsSDK();
                }

                $ssmClient = new SsmClient($param);
                $parameters = [
                    'InstanceIds' => [$instanceId],
                    'DocumentName' => 'AWS-RunShellScript'
                ];

                $command = [];
                $isCreateAccount = false;
                $isUserDeploy = false;
                if (self::checkUserExist($ssmClient, $parameters, $instanceId, [$username])) {
                    $isCreateAccount = true;
                    $command[] = "sudo adduser $username";
                    $command[] = "sudo -u $username mkdir -p /home/$username/.ssh";
                    $command[] = "sudo -u $username ssh-keygen -t rsa -b 4096 -C \"$gmailGithub\" -N \"\" -f \"/home/$username/.ssh/id_rsa\" > /dev/null";

                    $nodePath = self::getNodePath($instanceId); //thêm đường dẫn đến thư mục chứa tệp thực thi Node.js vào biến PATH
                    if ($nodePath) {
                        $command[] = "grep -qxF 'export PATH=\"$nodePath:\$PATH\"' /home/$username/.bashrc || echo 'export PATH=\"$nodePath:\$PATH\"' | sudo tee -a /home/$username/.bashrc";
                    }
                }
                $command[] = "echo $publicKey | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null";

                foreach ($instance as $item) {
                    if ($item['type'] == POLICY_TYPE['EC2_admin']) {
                        $command[] = "echo '$username ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers";
                    } else {
                        $isUserDeploy = true;
                        $groupName = $item['name'];
                        $command [] = "sudo usermod -aG $groupName $username";
                    }
                }
                if ($isCreateAccount && $isUserDeploy) {
                    $commandSudo = self::addCommandSudo($username);
                    $command = array_merge($command, $commandSudo);
                }
                $parameters['Parameters']['commands'] = $command;
                $ssmClient->sendCommand($parameters);
                sleep(5);
            }
            return ResponseService::responseJson(CODE_SUCCESS);
        } catch (AwsException $e) {
            Log::error("AWS Exception: {$e->getMessage()}");
            return ResponseService::responseJson(CODE_ERROR_SERVER, $e->getMessage());
        }
    }

    public function deleteUserEc2($user)
    {
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
                if($instanceId != INSTANCE_ID_240) {
                    $param = Common::configAwsSDK($instanceId);
                } else {
                    $param = Common::configAwsSDK();
                }
                $ssmClient = new SsmClient($param);

                if (!self::checkUserExist($ssmClient, $parameters, $instanceId, [$username])) {
                    $command[] = "echo '' | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null";
                    $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD:/d' /etc/sudoers";
                    $command[] = "sudo pkill -u $username";
                    $command[] = "sudo userdel -r $username";
                    $parameters['Parameters']['commands'] = $command;
                    $ssmClient->sendCommand($parameters);
                    sleep(3);
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
        $groupOldOfProject = 'apache';
        if($instanceId != INSTANCE_ID_240) {
            $param = Common::configAwsSDK($instanceId);
        } else {
            $param = Common::configAwsSDK();
        }

        $ssmClient = new SsmClient($param);
        $commands = [
            "if ! grep -q \"^$groupName:\" /etc/group; then sudo groupadd $groupName; fi",
            "sudo chown -R :$groupName /var/www/$projectName", // thư mục thuộc về group, thuộc sở hữu của người dùng root
//            "find /var/www/$projectName -type d -name \"storage\" -exec chmod -R 777 {} \;",
            "find /var/www/$projectName -type d -name \".git\" -exec chmod -R 777 {} \;",
            "find /var/www/$projectName -type d -path \"*/public/js\" -exec chmod -R 775 {} \;",
            "sudo chmod -R g+s /var/www/$projectName", // đảm bảo rằng tất cả các thư mục con được tạo trong đó sẽ kế thừa nhóm của thư mục gốc
            "for user in \$(getent group $groupOldOfProject | cut -d: -f4 | tr ',' ' '); do sudo usermod -aG $groupName \$user; done", // thêm tk ec2-user, apache, deploy vào nhóm
        ];

        $commandAdd = [];
        $commandAddDefault = [
            "if id -u apache > /dev/null 2>&1; then sudo usermod -aG $groupName apache; fi", // thêm tk apache vào nhóm
            "if id -u ec2-user > /dev/null 2>&1; then sudo usermod -aG $groupName ec2-user; fi", // thêm tk ec2-user vào nhóm
        ];
        switch ($instanceId) {
            case INSTANCE_ID_240:
                $commandAdd = $commandAddDefault;
                break;
            case INSTANCE_ID_142:
            case INSTANCE_ID_235:
                $commandAdd = array_merge($commandAddDefault, [
                    "if id -u admin > /dev/null 2>&1; then sudo usermod -aG $groupName admin; fi", // thêm tk admin vào nhóm
                ]);
                break;
            default:
                $commandAdd = array_merge($commandAddDefault, []);
                break;
        }

        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript',
            'Parameters' => [
                'commands' => array_merge($commands, $commandAdd),
            ],
        ];
        $ssmClient->sendCommand($parameters);
    }

    public function connectRDS(array $data, $filePath, $openConnect = true, $query = null)
    {
        try {
            if ($openConnect) {
                $host = $data[RDSManager::URL_END_POINT];
                $username = $data[RDSManager::USERNAME];
                $password = $data[RDSManager::PASSWORD];
                $ec2Username = $data[RDSManager::EC2_USERNAME];
                $ec2IpAddress = $data[RDSManager::EC2_IP_ADDRESS];

                $localFile = base_path("connect.php");
                $remoteFile = "/var/www/html/connect.php";
                $scpCommand = "scp -o StrictHostKeyChecking=no -i $filePath $localFile $ec2Username@$ec2IpAddress:$remoteFile 2>&1";
                exec($scpCommand, $scpOutput, $scpReturnVar);
                $dataFile = compact("host", "username", "password", "query");
                $serialized = serialize($dataFile);
                $encoded = escapeshellarg(base64_encode($serialized));
                $command = "ssh -i $filePath $ec2Username@$ec2IpAddress \"php $remoteFile $encoded\"";
                exec($command, $output, $code);
                return json_decode($output[0], true);
            }

            $host = $data[RDSManager::URL_END_POINT];
            $username = $data[RDSManager::USERNAME];
            $password = $data[RDSManager::PASSWORD];
            $database = '';
            $port = config('database.connections.mysql.port');
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $option = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $pdo = new \PDO($dsn, $username, $password, $option);
            if ($query) {
                $query = $pdo->query($query);
                $result = $query->fetchAll(\PDO::FETCH_ASSOC);
                return [
                    'code' => CODE_SUCCESS,
                    'data' => $result
                ];
            } else {
                return  [
                    'code' => 200,
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::info('query: '. $query);
            return [
                'code' => CODE_ERROR_SERVER,
                'message' => trans('api.rds_manager.connect_failed'),
                'data' => null
            ];
        }
    }

    public function getData($rds_manager_id)
    {
        $rdsManager = RDSManager::query()->find($rds_manager_id);
        $rdsManagerLocal = RDSManager::query()
            ->where(RDSManager::URL_END_POINT, config('database.connections.mysql.host'))
            ->where(RDSManager::USERNAME, config('database.connections.mysql.username'))
            ->where(RDSManager::TYPE, RDS_LOCAL)
            ->first();
        $openConnect = ($rds_manager_id != $rdsManagerLocal->id);
        $filePath = @$rdsManager->file->file_path;
        $data = [
            RDSManager::USERNAME => $rdsManager->username,
            RDSManager::PASSWORD => $rdsManager->password,
            RDSManager::URL_END_POINT => $rdsManager->url_end_point,
            RDSManager::EC2_USERNAME => $rdsManager->ec2_username,
            RDSManager::EC2_IP_ADDRESS => $rdsManager->ec2_ip_address,
            RDSManager::PHPMYADMIN_URL => @$rdsManager->phpmyadmin_url
        ];
        return compact('data', 'filePath', 'openConnect');
    }

    public function deleteAccountRDS($user_id)
    {
        $userData = User::query()
            ->where('id', $user_id)
            ->select('id', 'name')
            ->with(['rdsManagers', 'databases'])
            ->first();
        if(config('app.env') === ENVIRONMENT_UPDATE_RDS) {
            foreach ($userData->rdsManagers as $rdsManager) {
                $dataConnect = self::getData($rdsManager->id);
                Common::connectRDS(
                    $dataConnect['data'],
                    $dataConnect['filePath'],
                    $dataConnect['openConnect'],
                    "DROP USER '{$userData->name}'@'%'"
                );
            }
        }

        foreach ($userData->databases as $database) {
            $database->rdsPermissions()->detach();
            $database->delete();
        }
        $userData->rdsManagers()->detach();
    }
}
