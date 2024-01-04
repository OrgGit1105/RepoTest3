<?php

namespace App\Jobs;

use App\Models\User;
use Aws\Iam\IamClient;
use Carbon\Carbon;
use Helper\Common;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoDeleteIamUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $names = User::query()
            ->whereDate(User::RETIREMENT_DATE, Carbon::now()->format('Y-m-d'))
            ->pluck('name', 'id')
            ->toArray();

        $param = Common::configAwsSDK();
        $iamClient = new IamClient($param);
        $iamAws = $iamClient->listUsers()['Users'];

        foreach ($iamAws as $user) {
            if(array_search($user['UserName'], $names) && config('app.env') == 'production') {
                $iamClient->deleteUser([
                    'UserName' => $user['UserName']
                ]);
            }
        }
    }
}
