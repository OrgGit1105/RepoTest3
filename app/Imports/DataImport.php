<?php


namespace App\Imports;


use App\Models\Import;
use App\Models\DataManagement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class DataImport implements ToCollection
{
  public function __construct()
  {

  }

  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    foreach ($collection as $key => $value){
      if($key > 0){
        DataManagement::create([
//          DataManagement::IMPORT_ID => $this->import->id,
          DataManagement::EMPLOYEE_CODE =>$value[0],
          DataManagement::STAFFS_NAME =>$value[1],
          DataManagement::JOINING_AGE_COMPANY =>$value[2],
          DataManagement::DATE_JOINING_COMPANY =>$value[3],
          DataManagement::DATE_OUT_COMPANY =>$value[4],
          DataManagement::SPOUSE =>$value[5],
          DataManagement::DEPENDENTS =>$value[6],
          DataManagement::WORKED_YEAR =>$value[7],
          DataManagement::FINAL_EDUCATION =>$value[8],
          DataManagement::SHORTEST_SERVICE =>$value[9],
          DataManagement::TOTAL_WORKED =>$value[10],
          DataManagement::COMPANY_BRANCH =>$value[11],
//          DataManagement::FILE_NAME =>$value[11]
        ]);
      }
    }
    DB::commit();
  }
}
