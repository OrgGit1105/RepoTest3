<?php

namespace Database\Seeders;

use App\Models\VIAMUserPolicy;
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
//        $this->call(RoleSeeder::class);
        $this->call(PolicySeeder::class);
        $this->call(VIAMUserSeeder::class);
        $this->call(UserSeeder::class);
//        $this->call(WorkingTimeSeed::class);
    }
}
