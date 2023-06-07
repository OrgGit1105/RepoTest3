<?php


namespace App\Imports;


use App\Models\Import;
use App\Models\DataManagement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class FileImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
//    dd($row["エンジン最高回転数（一般） [rpm]"]);
//    dd($row["乗務員：事業所コード"]);
  }
}
