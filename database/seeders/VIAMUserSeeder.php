<?php

namespace Database\Seeders;

use App\Models\Policy;
use App\Models\VIAMUser;
use App\Models\VIAMUserPolicy;
use Illuminate\Database\Seeder;

class VIAMUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = [1 => 'V-Face Admin', 2 => 'V-Face Normal'];
        if (!VIAMUserPolicy::first() && !VIAMUser::first()) {
            foreach ($names as $key => $value) {
                $viamUser = VIAMUser::query()->create([VIAMUser::NAME => $value]);
                VIAMUserPolicy::create([
                    VIAMUserPolicy::VIAM_USER_ID => $viamUser->id,
                    VIAMUserPolicy::POLICY_ID => $key
                ]);
            }

        }
    }
}
