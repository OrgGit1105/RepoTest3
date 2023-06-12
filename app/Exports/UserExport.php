<?php

namespace App\Exports;

use App\Exports\sheet\UserSheet;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserExport implements WithMultipleSheets
{
  use Exportable;
  protected $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  public function sheets(): array
  {
    $sheets = [];
    $sheets[0] = new UserSheet(
      $this->data
    );
    return $sheets;
  }
}
