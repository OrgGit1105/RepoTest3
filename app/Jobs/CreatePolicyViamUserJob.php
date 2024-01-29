<?php

namespace App\Jobs;

use App\Models\VIAMUser;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreatePolicyViamUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id, $type, $instanceId, $project;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($viamUserId, $type, $instanceId, $project)
    {
        $this->id = $viamUserId;
        $this->type = $type;
        $this->instanceId = $instanceId;
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $viamUser = VIAMUser::query()->find($this->id);
        $param = Common::configAwsSDK();
        $ssmClient = new SsmClient($param);

        if($this->type == POLICY_TYPE['EC2_admin'] || $this->type == POLICY_TYPE['EC2_deploy']) {
            $parameters = [
                'InstanceIds' => [$this->instanceId],
                'DocumentName' => 'AWS-RunShellScript'
            ];
            $command = [];
            $names = [];
            $sshKey = [];
            foreach ($viamUser->users as $user) {
                $username = $user->name;
                $names[] = $username;
                $sshKey[$username] = $user->ssh_public_key;
                if ($this->type == POLICY_TYPE['EC2_admin']) {
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/* /var/www/$this->project/*' >> /etc/sudoers";
                } else {
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/chmod 775 /var/www/$this->project/*' >> /etc/sudoers";
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD:/usr/bin/rm /var/www/$this->project/*' >> /etc/sudoers";
                }
            }
            $command[] = "echo '' >> /etc/sudoers";
            $commands = [];
            $userNotExists = Common::checkUserExist($ssmClient, $parameters, $this->instanceId, $names);
            if (!empty($userNotExists)) {
                foreach ($userNotExists as $userNotExist) {
                    $commands[] = "sudo adduser $userNotExist";
                    $commands[] = "sudo -u $userNotExist mkdir -p /home/$userNotExist/.ssh";
                    $commands[] = "echo $sshKey[$userNotExist] | sudo -u $userNotExist tee /home/$userNotExist/.ssh/authorized_keys > /dev/null";
                    $command = [
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/bin/ls,/usr/bin/yum,/usr/bin/systemctl' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/chmod 775 /etc/httpd/conf.d/*' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/cp /etc/httpd/conf.d/*' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/rm /etc/httpd/conf.d/*i' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/systemctl restart httpd.service' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/sbin/service httpd restart' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/vim' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/usr/bin/certbot' >> /etc/sudoers",
                        "echo '$userNotExist ALL=(ALL) NOPASSWD:/bin/chmod 777 /var/log/letsencrypt/*' >> /etc/sudoers"
                    ];
                }
            }

            $commandAdd = implode(' && ', $commands);
            $parameters['Parameters']['commands'] = array_merge([$commandAdd], $command);
            $ssmClient->sendCommand($parameters);
        }
    }
}
