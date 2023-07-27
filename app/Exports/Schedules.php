<?php

namespace App\Exports;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class Schedules implements FromView, WithStyles
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
        foreach($result as $val){
            $days[] = $val->format('Y-m-d');
        }
        return view('excel.schedule', [
            'days' => collect($days),
            'datas' => $this->data
        ]);
    }
    // public function styles(Worksheet $sheet)
    // {
    //     $cell = 'B2'; // Change this to the cell you want to add the text to
    //     $sheet->getStyle($cell)->applyFromArray([
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_RIGHT,
    //             'vertical' => Alignment::VERTICAL_TOP,
    //         ],
    //     ]);
    //     $sheet->setCellValue('A2', $sheet->getCell('A2')->getValue());
    //     $sheet->mergeCells('A2:B2', 'merge');
        
    // }
}
