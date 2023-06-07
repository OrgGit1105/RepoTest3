<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
  public function run()
  {
    $data = [
      ['interview_date' => '2021-06-27 00:00:00', 'candidate_name' => 'test', 'joining_age' => 26, 'spouse' => 1, 'dependents' => 3, 'worked_years' => 8, 'final_education' => 7, 'shortest_service' => 50, 'company_branch_id'=> 1, 'is_accepted'=> 1],
      ['interview_date' => '2021-06-27 23:59:59', 'candidate_name' => 'test abc', 'joining_age' => 26, 'spouse' => 1, 'dependents' => 3, 'worked_years' => 8, 'final_education' => 7, 'shortest_service' => 50, 'company_branch_id'=> 1, 'is_accepted'=> 0],
    ];
    DB::table('enrollments')->insert($data);
  }

}
