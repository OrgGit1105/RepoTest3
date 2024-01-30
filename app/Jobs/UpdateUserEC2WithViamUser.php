<?php

namespace App\Jobs;

use App\Models\Policy;
use App\Models\VIAMUser;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateUserEC2WithViamUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $viamUser, $policies, $action;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($viamUser, $policies, $action)
    {
        $this->viamUser = $viamUser;
        $this->policies = $policies;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);
        if($this->action === 'create') {
            foreach ($this->policies as $addPolicy) {
                $policy = Policy::query()->find($addPolicy);
                $this->createUser($ssmClient, $policy);
                sleep(10);
            }
        } else {
            $instanceList = [];
            foreach ($this->policies as $removePolicy) {
                $instanceList[] = Policy::query()->find($removePolicy)->instance_id;
            }
            $this->deleteUser($ssmClient, $instanceList);
        }
    }

    private function createUser(SsmClient $ssmClient, $policy) {
        $type = $policy->type;
        $project = $policy->project_name;
        $instanceId = $policy->instance_id;
        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript'
        ];
        if($type == POLICY_TYPE['EC2_admin'] || $type == POLICY_TYPE['EC2_deploy']) {
            $command = [];
            $names = [];
            $sshKey = [];
            foreach ($this->viamUser->users as $user) {
                $username = $user->name;
                $names[] = $username;
                $sshKey[$username] = $user->ssh_public_key;
                if ($type == POLICY_TYPE['EC2_admin']) {
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/* /var/www/$project/*' >> /etc/sudoers";
                } else {
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/chmod 775 /var/www/$project/*' >> /etc/sudoers";
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/rm /var/www/$project/*' >> /etc/sudoers";
                }
            }
            $command[] = "echo '' >> /etc/sudoers";
            $commands = [];
            $userNotExists = Common::checkUserExist($ssmClient, $parameters, $instanceId, $names);
            if (!empty($userNotExists)) {
                foreach ($userNotExists as $userNotExist) {
                    $commands[] = "sudo adduser $userNotExist";
                    $commands[] = "sudo -u $userNotExist mkdir -p /home/$userNotExist/.ssh";
                    $commands[] = "echo $sshKey[$userNotExist] | sudo -u $userNotExist tee /home/$userNotExist/.ssh/authorized_keys > /dev/null";
                    $command = array_merge($command, [
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/bin/ls,/usr/bin/yum,/usr/bin/systemctl' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/chmod 775 /etc/httpd/conf.d/*' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/cp /etc/httpd/conf.d/*' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/rm /etc/httpd/conf.d/*i' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/systemctl restart httpd.service' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/sbin/service httpd restart' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/vim' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/certbot' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/bin/chmod 777 /var/log/letsencrypt/*' >> /etc/sudoers",
                        "echo '' >> /etc/sudoers"
                    ]);
                }
            }

            $commandAdd = implode(' && ', $commands);
            $parameters['Parameters']['commands'] = array_merge([$commandAdd], $command);
            $ssmClient->sendCommand($parameters);
        }
    }

    private function deleteUser(SsmClient $ssmClient, $instanceList)
    {
        $usernames = [];
        foreach ($this->viamUser->users as $user) {
            $usernames[] = $user->name;
        }
        foreach ($instanceList as $instanceId) {
            $parameters = [
                'InstanceIds' => [$instanceId],
                'DocumentName' => 'AWS-RunShellScript'
            ];
            $userNotExists = Common::checkUserExist($ssmClient, $parameters, $instanceId, $usernames);
            $userExists = $usernames;
            if($userNotExists) {
                $userExists = array_diff($usernames, $userNotExists);
            }

            $command = [];
            foreach ($userExists as $username) {
                $command[] = "echo '' | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null";
                $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD:/d' /etc/sudoers";
                $command[] = "sudo pkill -u $username";
                $command[] = "sudo userdel -r $username";
            }
            if($command) {
                $parameters['Parameters']['commands'] = $command;
                $ssmClient->sendCommand($parameters);
            }
        }
    }
}
