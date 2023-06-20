<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WorkingTimeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $now = Carbon::now()->format('Y-m-d');

      DB::table('arriving_reports')->truncate();
      DB::table('arriving_reports')->insert([
        [
          'user_id' => 3,
          'in_time' => "$now 08:30:00",
          'out_time' => "$now 08:30:00",
          'status' => 1
        ],
        [
          'user_id' => 4,
          'in_time' => "$now 08:30:00",
          'out_time' => "$now 08:30:00",
          'status' => 1
        ],
        [
          'user_id' => 5,
          'in_time' => "$now 08:30:00",
          'out_time' => "$now 08:30:00",
          'status' => 1
        ],
      ]);
    }
}
