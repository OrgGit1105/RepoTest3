<?php

namespace Database\Seeders;

use App\Models\ConfigRange;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(ConfigRangeSeeder::class);
//        $this->call(DistrictSeeder::class);
//        $this->call(ProvinceSeeder::class);
//        $this->call(WardSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(CompanyBranchSeeder::class);
        $this->call(EnrollmentSeeder::class);
//        $this->call(EmployeeSeeder::class);
//        $this->call(RiskScoreSeeder::class);
        // $this->call(DigitacoPointSeed::class);
    }
}
