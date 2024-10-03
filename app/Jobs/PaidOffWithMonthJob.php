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

class PaidOffWithMonthJob implements ShouldQueue
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
        $date = Carbon::now()->format('Y-m-15'); // phép chỉ đc tính khi làm 1/2 số công của tháng đó=> nghỉ trc ngày 15 sẽ ko tính phép
        $employees = User::query()
            ->whereNotNull('entry_date')
            ->where(function ($query) use ($date) {
                $query->whereNull('retirement_date')
                    ->orWhere('retirement_date', '>=', $date);
            })
            ->get();

        foreach ($employees as $employee)
        {
            $dateStart = Carbon::parse($employee->entry_date);
            $currentDate = Carbon::now();
            $probationary_staff = Carbon::parse($employee->entry_date)->addMonth(2);
            $threeMonthsLater = Carbon::parse($employee->entry_date)->addMonth(3);
            $nextYear = Carbon::parse($employee->entry_date)->addYear();
            $thirteenMonthsLater = Carbon::parse($employee->entry_date)->addMonth(13);
            $paid_off_start = $employee->paid_off_start;
            $paid_off = $employee->paid_off;
            if ($currentDate < $nextYear) {
                if($currentDate < $probationary_staff) {
                    $paid_off = 0;
                }
                elseif ($currentDate == $probationary_staff) {
                    $paid_off = ($paid_off_start > 0 ) ? ($paid_off_start + 1) : 3;
                }
                elseif ($currentDate > $probationary_staff && $currentDate < $threeMonthsLater) {
                    if($dateStart->format('Y-m-d') <= $dateStart->format('Y-m-15'))
                        $paid_off = ($paid_off_start > 0) ? ($paid_off_start + 2) : 4;
                    else
                        $paid_off = ($paid_off_start > 0) ? ($paid_off_start + 1) : 3;
                } else {
                    $paid_off += 1;
                }
            }

            if($currentDate >= $nextYear && $currentDate < $thirteenMonthsLater) {
                $startOfYear = Carbon::parse($currentDate->format('Y') . '-01-01');
                $monthsPassed = $startOfYear->diffInMonths($currentDate);
                $paid_off += 12 - $monthsPassed;
            }

            $paid_off_before = $employee->paid_off;
            $employee->paid_off = $paid_off;
            $employee->save();

            if($paid_off_before != $paid_off) {
                HistoryUpdatePaidOff::create([
                    HistoryUpdatePaidOff::USER_ID => $employee->id,
                    HistoryUpdatePaidOff::TYPE => UPDATE_PAID_OFF_AUTO_MONTH,
                    HistoryUpdatePaidOff::PAID_OFF_BEFORE => $paid_off_before,
                    HistoryUpdatePaidOff::PAID_OFF_AFTER => $paid_off
                ]);
            }
        }
    }
}
