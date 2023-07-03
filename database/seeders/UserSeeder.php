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
      $password = "12345678";
//      DB::statement('SET FOREIGN_KEY_CHECKS = 0');
      DB::table('users')->truncate();
      DB::table('users')->insert([
        [
          'name' => 'Nawa Hinako',
          'email' => 'hi8nawa@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 1,
          'status' => 1,
        ],
        [
          'name' => 'bach',
          'email' => 'bach@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
      ]);
//      DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
