<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Policy::first()) {
            Policy::query()->insert([
                [
                    Policy::NAME => 'Admin',
                    Policy::TYPE => POLICY_TYPE['V_FACE']
                ],
                [
                    Policy::NAME => 'Normal',
                    Policy::TYPE => POLICY_TYPE['V_FACE']
                ],
            ]);
        }
    }
}
