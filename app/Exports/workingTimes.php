<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class workingTimes implements FromView, WithEvents
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('excel.working-time', ['data'=> $this->data]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('H')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('H')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $event->sheet->getStyle('H')->getAlignment()->setShrinkToFit(true);
            },
        ];
    }
}
