<?php

namespace App\Exports\sheet;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserSheet implements WithTitle,FromView,WithEvents
{

    protected $data;
    public function __construct($data)
    {
      $this->data = $data;
    }

    public function title(): string
    {
      return "Employee";
    }

    public function view(): View
    {
      return view("excel.user-sheet-view",
        [
          'data'=>$this->data,
        ]);
    }

    public function registerEvents(): array
    {
      return [];
    }

}
