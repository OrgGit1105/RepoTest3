<?php

namespace App\Jobs;

use App\Models\Policy;
use App\Models\VIAMUser;
use Aws\Ssm\SsmClient;
use Helper\Common;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUserAdminOrDeployWithPolicyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $policy, $instanceId, $deleteAccountUser, $typeAccount;

    /**
     * Create a new job instance.
     * @param Policy $policy
     * @param string $instanceId
     * @param bool $deleteAccountUser
     * @param int $typeAccount
     */
    public function __construct(Policy $policy, string $instanceId, $deleteAccountUser = false, int $typeAccount)
    {
        $this->policy = $policy;
        $this->instanceId = $instanceId;
        $this->deleteAccountUser = $deleteAccountUser;
        $this->typeAccount = $typeAccount;
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
        $id = $this->policy->id;
        $delete = false;
        $usernames = [];
        foreach ($this->policy->viam_users as $viam) {
            foreach ($viam->users as $user) {
                $usernames[] = $user->name;
            }
        }

        $userNotExists = Common::checkUserExist($ssmClient, $parameters, $this->instanceId, $usernames);
        $userExists = $usernames;
        if ($userNotExists) {
            $userExists = array_diff($usernames, $userNotExists);
        }
        // trường hợp policy bị xóa là policy có type = EC2 duy nhất trong VIAM_USER liên quan đến policy bị xóa
        // => xóa tài khoản user trên EC2
        if($this->deleteAccountUser) {
            $viamUserOfPolicy = VIAMUser::query()
                ->whereHas('policies', function ($e) use ($id) {
                    $e->where('policies.id', $id);
                })
                ->get();
            foreach ($viamUserOfPolicy as $viamUser) {
                $viamUserOfPolicyEC2Other = VIAMUser::query()->where('id', $viamUser->id)
                    ->whereHas('policies', function ($e) use ($id) {
                        $e->where('policies.id', '!=', $id)
                            ->whereIn(Policy::TYPE, [POLICY_TYPE['EC2_admin'], POLICY_TYPE['EC2_deploy']]);
                    })->exists();

                if ($viamUserOfPolicyEC2Other) {
                    $delete = true;
                }
                if (!$viamUserOfPolicyEC2Other) {
                    foreach ($userExists as $username) {
                        $command[] = "echo '' | sudo -u $username tee /home/$username/.ssh/authorized_keys > /dev/null";
                        $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD: ALL/d' /etc/sudoers";
                        $command[] = "sudo pkill -u $username";
                        $command[] = "sudo userdel -r $username";
                    }
                }
            }
        } else {
            $delete = true;
        }
        if($delete && $this->typeAccount == POLICY_TYPE['EC2_admin']) {
            foreach ($userExists as $username) {
                $command[] = "sudo sed -i '/^$username ALL=(ALL) NOPASSWD: ALL/d' /etc/sudoers";
            }
        }
        if($delete && $this->typeAccount == POLICY_TYPE['EC2_deploy']) {
            $projectName = $this->policy->project_name;
            $groupName = $this->policy->name;
            $command[] = "sudo chown -R root:root /var/www/$projectName";
            $command[] = "sudo chmod -R g=rwx,o= /var/www/$projectName";
            $command[] = "sudo chmod g+s /var/www/$projectName";
            $command[] = "sudo groupdel $groupName";
        }
        if($command) {
            $parameters['Parameters']['commands'] = $command;
            $ssmClient->sendCommand($parameters);
        }
    }
}
