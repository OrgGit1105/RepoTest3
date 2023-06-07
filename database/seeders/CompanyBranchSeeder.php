<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyBranchSeeder extends Seeder
{
  public function run()
  {
    $data = [
      ['name' => '管理本部', 'address' => '', 'description'=> '','role_id'=>1],
      ['name' => 'YG茨城HC', 'address' => '', 'description' => '','role_id'=>2],
      ['name' => '千葉HC', 'address' => '', 'description' => '','role_id'=>2],
      ['name' => '武蔵野HC', 'address' => '', 'description' => '','role_id'=>2],
      ['name' => '横浜HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => 'AI総合配車センター', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '松戸HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '新座HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => 'SL茨城HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => 'SL八千代HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '群馬HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '福島HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => 'いわきHC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '杉戸第2HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '春日部DC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '春日部TC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '茨城TC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '幸手HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '桶川HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '松伏HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '杉戸第4HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '相模原HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '関西HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '杉戸SHC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '杉戸第3HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '古河SHC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '八千代SHC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '岩槻HC', 'address' => '', 'description'=> '','role_id'=>2],
      ['name' => '沖縄HC', 'address' => '', 'description'=> '','role_id'=>2],
    ];
    DB::table('company_branchs')->insert($data);
  }
}

