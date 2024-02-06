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

class UpdateUserEC2WithViamUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $viamUser, $policies, $action, $deleteAccountUser;

    /**
     * Create a new job instance.
     *
     * @param VIAMUser $viamUser
     * @param array $policies
     * @param string $action
     */
    public function __construct(VIAMUser $viamUser, array $policies, string $action, $deleteAccountUser = false)
    {
        $this->viamUser = $viamUser;
        $this->policies = $policies;
        $this->action = $action;
        $this->deleteAccountUser = $deleteAccountUser;
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
        if ($this->action === 'create') {
            foreach ($this->policies as $addPolicy) {
                $policy = Policy::query()->find($addPolicy);
                if (in_array($policy->type, [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])) {
                    $this->createUser($ssmClient, $policy);
                }
            }
        }
        if ($this->action === 'delete') {
            foreach ($this->policies as $removePolicy) {
                $policy = Policy::query()->find($removePolicy);
                if (in_array($policy->type, [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']])) {
                    $this->deleteUser($ssmClient, $policy);
                }
            }
        }
    }

    private function createUser(SsmClient $ssmClient, $policy)
    {
        $type = $policy->type;
        $groupName = $policy->name;
        $instanceId = $policy->instance_id;
        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript'
        ];
        $command = [];
        $names = [];
        $sshKey = [];
        foreach ($this->viamUser->users as $user) {
            $username = $user->name;
            $names[] = $username;
            $sshKey[$username] = $user->ssh_public_key;
            if ($type == POLICY_TYPE['EC2_admin']) {
                $command[] = "echo '$username ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers";
            } else {
                $command[] = "sudo usermod -aG $groupName $username";
            }
        }
        $commands = [];
        $userNotExists = Common::checkUserExist($ssmClient, $parameters, $instanceId, $names);
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
        sleep(count($userNotExists));
    }

    private function deleteUser(SsmClient $ssmClient, $policy)
    {
        $instanceId = $policy->instance_id;
        $usernames = [];
        foreach ($this->viamUser->users as $user) {
            $usernames[] = $user->name;
        }
        $parameters = [
            'InstanceIds' => [$instanceId],
            'DocumentName' => 'AWS-RunShellScript'
        ];
        $userNotExists = Common::checkUserExist($ssmClient, $parameters, $instanceId, $usernames);
        $userExists = $usernames;
        if ($userNotExists) {
            $userExists = array_diff($usernames, $userNotExists);
        }

        $command = [];
        if ($this->deleteAccountUser) {
            foreach ($userExists as $username) {
                $command[] = "echo '' | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null";
                $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD:/d' /etc/sudoers";
                $command[] = "sudo pkill -u $username";
                $command[] = "sudo userdel -r $username";
            }
        } else {
            $type = $policy->type;
            if ($type == POLICY_TYPE['EC2_admin']) {
                foreach ($userExists as $username) {
                    $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD: ALL/d' /etc/sudoers";
                }
            } else {
                $groupName = $policy->name;
                foreach ($userExists as $username) {
                    $command[] = "sudo gpasswd -d $username $groupName";
                }
            }
        }

        if ($command) {
            $parameters['Parameters']['commands'] = $command;
            $ssmClient->sendCommand($parameters);
        }
    }
}
