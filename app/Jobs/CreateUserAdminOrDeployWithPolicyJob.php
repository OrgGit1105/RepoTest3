<?php

namespace App\Jobs;

use App\Models\Policy;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateUserAdminOrDeployWithPolicyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $policy, $instanceId, $type, $groupName, $projectName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($policy, $instanceId, $type, $groupName = null, $projectName = null)
    {
        $this->policy = $policy;
        $this->instanceId = $instanceId;
        $this->type = $type;
        $this->groupName = $groupName;
        $this->projectName = $projectName;
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

        $parameters = [
            'InstanceIds' => [$this->instanceId],
            'DocumentName' => 'AWS-RunShellScript'
        ];
        $command = [];
        $names = [];
        $sshKey = [];
        if ($this->type == POLICY_TYPE['EC2_deploy']) {
            Common::createGroupEc2($this->instanceId, $this->groupName, $this->projectName);
        }
        foreach ($this->policy->viam_users as $viam) {
            foreach ($viam->users as $user) {
                $username = $user->name;
                $names[] = $username;
                $sshKey[$username] = $user->ssh_public_key;
                if ($this->type == POLICY_TYPE['EC2_admin']) {
                    $command[] = "echo '$username ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers";
                } else {
                    $command [] = "sudo usermod -aG $this->groupName $username";
                }
            }
        }
        $commands = [];
        $userNotExists = Common::checkUserExist($ssmClient, $parameters, $this->instanceId, $names);
        if (!empty($userNotExists)) {
            foreach ($userNotExists as $userNotExist) {
                $commands[] = "sudo adduser $userNotExist";
                $commands[] = "sudo -u $userNotExist mkdir -p /home/$userNotExist/.ssh";
                $commands[] = "echo $sshKey[$userNotExist] | sudo -u $userNotExist tee /home/$userNotExist/.ssh/authorized_keys > /dev/null";
            }
        }

        $commandAdd = implode(' && ', $commands);
        $parameters['Parameters']['commands'] = array_merge([$commandAdd], $command);
        $ssmClient->sendCommand($parameters);
    }
}
