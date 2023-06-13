<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//      DB::statement('SET FOREIGN_KEY_CHECKS = 0');
      DB::table('users')->truncate();
      DB::table('users')->insert([
        [
          'name' => 'manager',
          'email' => 'manager@gmail.com',
          'password' => Hash::make(123),
          'role_id' => 1,
          'status' => 1,
        ],
        [
          'name' => 'manager2',
          'email' => 'manager2@gmail.com',
          'password' => Hash::make(123),
          'role_id' => 1,
          'status' => 1,
        ],
        [
          'name' => 'staff',
          'email' => 'staff@gmail.com',
          'password' => Hash::make(123),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'ngan',
          'email' => 'ngan@gmail.com',
          'password' => Hash::make(123),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'bach',
          'email' => 'bach@gmail.com',
          'password' => Hash::make(123),
          'role_id' => 2,
          'status' => 1,
        ],
      ]);
//      DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
