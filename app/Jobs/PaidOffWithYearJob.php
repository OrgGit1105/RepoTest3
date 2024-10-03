<?php

namespace App\Jobs;

use App\Models\HistoryUpdatePaidOff;
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
        $date = Carbon::now()->format('Y-m-d');
        $employees = User::query()
            ->whereNotNull('entry_date')
            ->where(function ($query) use ($date) {
                $query->whereNull('retirement_date')
                    ->orWhere('retirement_date', '>=', $date);
            })
            ->get();

        foreach ($employees as $employee)
        {
            $currentDate = Carbon::now();
            $thirteenMonthsLater = Carbon::parse($employee->entry_date)->addMonth(13);
            if($currentDate >= $thirteenMonthsLater) {
                $paid_off = $employee->paid_off;
                $paid_off = ($paid_off > 0) ? $paid_off + 12 : 12;

                $paid_off_before = $employee->paid_off;
                $employee->paid_off = $paid_off;
                $employee->save();

                if($paid_off_before != $paid_off) {
                    HistoryUpdatePaidOff::create([
                        HistoryUpdatePaidOff::USER_ID => $employee->id,
                        HistoryUpdatePaidOff::TYPE => UPDATE_PAID_OFF_AUTO_YEAR,
                        HistoryUpdatePaidOff::PAID_OFF_BEFORE => $paid_off_before,
                        HistoryUpdatePaidOff::PAID_OFF_AFTER => $paid_off
                    ]);
                }
            }
        }
    }
}
