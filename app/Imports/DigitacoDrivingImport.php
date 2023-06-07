<?php


namespace App\Imports;

use App\Models\Import;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

HeadingRowFormatter::default('none');


class DigitacoDrivingImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {

  }
}
