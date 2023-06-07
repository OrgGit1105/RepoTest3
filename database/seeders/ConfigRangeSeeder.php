<?php

namespace Database\Seeders;

use App\Models\ConfigRange;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigRangeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      // total company
      ['from' => NULL, 'to' => NULL, 'rank' => NULL, 'number_of_studies'=> 50, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'C'],
      ['from' => 0, 'to' => 1, 'rank' => 0, 'number_of_studies'=> 50, 'average_total'=> 0.46653536112227, 'type' => 3,  'ranges'=> 'B'],
      ['from' => 1, 'to' => 2, 'rank' => 1, 'number_of_studies'=> 69.9417989417989, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'A'],
      ['from' => 2, 'to' => 3, 'rank' => 2, 'number_of_studies'=> 23.3228346456693, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'D'],
      ['from' => 3, 'to' => 4, 'rank' => 3, 'number_of_studies'=> 24.896174863388, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'D'],
      ['from' => 4, 'to' => 5, 'rank' => 4, 'number_of_studies'=> 23.5542168674699, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'D'],
      ['from' => 5, 'to' => 6, 'rank' => 5, 'number_of_studies'=> 27.8015873015873, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'D'],
      ['from' => 6, 'to' => 7, 'rank' => 6, 'number_of_studies'=> 24.6964285714286, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'D'],
      ['from' => 7, 'to' => 999, 'rank' => 7, 'number_of_studies'=> 24.70, 'average_total'=> 0.46653536112227, 'type' => 3, 'ranges'=> 'D'],
      // data old
      ['from' => NULL, 'to' => NULL, 'rank' => 0, 'number_of_studies'=> 50, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'C'],
      ['from' => 0, 'to' => 20, 'rank' => 1, 'number_of_studies'=> 56.8, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'B'],
      ['from' => 20, 'to' => 25, 'rank' => 2, 'number_of_studies'=> 40.0851063829787, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'C'],
      [ 'from' => 25, 'to' => 30, 'rank' => 3, 'number_of_studies'=> 65.0917431192661, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'B'],
      [ 'from' => 30, 'to' => 35, 'rank' => 4, 'number_of_studies'=> 78.2944785276074, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'A'],
      [ 'from' => 35, 'to' => 40, 'rank' => 5, 'number_of_studies'=> 66.5797101449275, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'B'],
      [ 'from' => 40, 'to' => 45, 'rank' => 6, 'number_of_studies'=> 58.7920792079208, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'B'],
      [ 'from' => 45, 'to' => 50, 'rank' => 7, 'number_of_studies'=> 43.0148514851485, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'C'],
      [ 'from' => 50, 'to' => 55, 'rank' => 8, 'number_of_studies'=> 37.6491228070175, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'D'],
      [ 'from' => 55, 'to' => 999, 'rank' => 9, 'number_of_studies'=> 35.4177215189873, 'average_total'=> 0.226051196102059, 'type' => 0, 'ranges'=> 'E'],
      // data degree bang cap
      ['from' => 0, 'to' => 1, 'rank' => 0, 'number_of_studies'=> 50, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'C'],
      ['from' => 1, 'to' => 2, 'rank' => 1, 'number_of_studies'=> 102.313, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'A'],
      ['from' => 2, 'to' => 3, 'rank' => 2, 'number_of_studies'=> 41.7121280487805, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'D'],
      ['from' => 3, 'to' => 4, 'rank' => 3, 'number_of_studies'=> 50.0123029827316, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'C'],
      ['from' => 4, 'to' => 5, 'rank' => 4, 'number_of_studies'=> 57.86256, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'A'],
      ['from' => 5, 'to' => 6, 'rank' => 5, 'number_of_studies'=> 53.03055, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'B'],
      ['from' => 6, 'to' => 7, 'rank' => 6, 'number_of_studies'=> 53.8756052631579, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'B'],
      ['from' => 7, 'to' => 8, 'rank' => 7, 'number_of_studies'=> 53.8756052631579, 'average_total'=> 0.0219889132446044, 'type' => 4, 'ranges'=> 'B'],
      // data longest working time
      ['from' => NULL, 'to' => NULL, 'rank' => null,'number_of_studies'=> 50, 'average_total'=> 0.429117446881595, 'type' => 5, 'ranges'=> 'C'],
      ['from' => 0, 'to' => 4, 'rank' => 1, 'number_of_studies'=> 35.4744807692308, 'average_total'=> 0.429117446881595, 'type' => 5, 'ranges'=> 'E'],
      ['from' => 4, 'to' => 12, 'rank' => 2, 'number_of_studies'=> 46.7325390625, 'average_total'=> 0.429117446881595, 'type' => 5, 'ranges'=> 'D'],
      ['from' => 12, 'to' => 20, 'rank' => 3, 'number_of_studies'=> 48.5250976331361, 'average_total'=> 0.429117446881595, 'type' => 5, 'ranges'=> 'C'],
      ['from' => 20, 'to' => 42, 'rank' => 4, 'number_of_studies'=> 53.0164344262295, 'average_total'=> 0.429117446881595, 'type' => 5, 'ranges'=> 'B'],
      ['from' => 42, 'to' => 999, 'rank' => 5, 'number_of_studies'=> 67.3982666666667, 'average_total'=> 0.429117446881595, 'type' => 5, 'ranges'=> 'A'],
      // Dpendent person
      ['from' => NULL, 'to' => NULL, 'rank' => NULL, 'number_of_studies'=> 50, 'average_total'=> 0.308097664642514, 'type' => 2, 'ranges'=> 'C',],
      ['from' => 0, 'to' => 1, 'rank' => 0, 'number_of_studies'=> 43.0625, 'average_total'=> 0.308097664642514, 'type' => 2, 'ranges'=> 'E'],
      ['from' => 1, 'to' => 2, 'rank' => 1, 'number_of_studies'=> 74.6047904191617, 'average_total'=> 0.308097664642514, 'type' => 2, 'ranges'=> 'B'],
      ['from' => 2, 'to' => 3, 'rank' => 2, 'number_of_studies'=> 89.0458015267176, 'average_total'=> 0.308097664642514, 'type' => 2, 'ranges'=> 'B'],
      ['from' => 3, 'to' => 999, 'rank' => 3, 'number_of_studies'=> 99.7213114754098, 'average_total'=> 0.308097664642514, 'type' => 2, 'ranges'=> 'A'],
      // người phối ngẫu
      [ 'rank' => NULL, 'to' => NULL,'from' => NULL,'number_of_studies'=> 50, 'average_total'=> 0.241347095552777, 'type' => 1, 'ranges'=> 'C'],
      [ 'rank' => 0, 'to' => NULL,'from' => NULL,'number_of_studies'=> 44.7410714285714, 'average_total'=> 0.241347095552777, 'type' => 1, 'ranges'=> 'E'],
      [ 'rank' => 1, 'to' => NULL,'from' => NULL,'number_of_studies'=> 77.4143222506394, 'average_total'=> 0.241347095552777, 'type' => 1, 'ranges'=> 'A'],
    ];
      DB::table('config_ranges')->insert($data);
  }
}
