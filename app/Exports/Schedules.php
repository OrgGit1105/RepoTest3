<?php

namespace App\Exports;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class Schedules implements FromView
{
    protected $y_month;
    protected $data;
    public function __construct($y_month, $data)
    {
        $this->y_month = $y_month;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $start = Carbon::parse($this->y_month)->firstOfMonth()->startOfWeek()->subDay()->format("Y-m-d");
        $end = Carbon::parse($this->y_month)->endOfMonth()->endOfWeek()->subDay()->format("Y-m-d");
        $result = CarbonPeriod::create($start, '1 day', $end);

        $days = [];
        foreach ($result as $val) {
            $days[] = $val->format('Y-m-d');
        }
        return view('excel.schedule', [
            'days' => collect($days),
            'datas' => $this->data,
            'year_month' => $this->y_month
        ]);
    }
}
