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

class AutoDeleteEc2UserJob implements ShouldQueue
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
        if (config('app.env') == 'production') {
            $users = User::query()->whereDate(User::RETIREMENT_DATE, Carbon::now()->format('Y-m-d'))->get();
            foreach ($users as $user) {
                Common::deleteUserEc2($user);
            }
        }
    }
}
