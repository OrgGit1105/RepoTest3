<?php


namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
  public function run()
  {
    $data = [
      ['name' => 'Headquater_Role', 'display_name' => '', 'description' => ''],
      ['name' => 'Department_Role', 'display_name' => '', 'description'=> ''],

    ];
    DB::table('roles')->insert($data);
  }
}
