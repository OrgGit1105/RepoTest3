<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PaidOffWithYearJob implements ShouldQueue
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
        $employees = User::query()->whereNotNull('entry_date')->get();
        foreach ($employees as $employee)
        {
            $currentDate = Carbon::now();
            $thirteenMonthsLater = Carbon::parse($employee->entry_date)->addMonth(13);
            if($currentDate >= $thirteenMonthsLater) {
                $paid_off = $employee->paid_off;
                $paid_off = ($paid_off > 0) ? $paid_off + 12 : 12;
                $employee->paid_off = $paid_off;
                $employee->save();
            }
        }
    }
}
