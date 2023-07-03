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
          'name' => 'Kohei Ikeda',
          'email' => 'i.kohei2323@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 1,
          'status' => 1,
        ],
        [
          'name' => 'Takaba Moeko',
          'email' => 'takaba.veho@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 1,
          'status' => 1,
        ],
        [
          'name' => 'Vũ Anh Thư',
          'email' => 'thu.vu@veho-works.com',
          'password' => Hash::make($password),
          'role_id' => 1,
          'status' => 1,
        ],
        [
          'name' => 'Phạm Thị Trang',
          'email' => 'phamthitrang290@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Đinh Thị Vững',
          'email' => 'dbacninh99@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Đỗ Xuân Vững',
          'email' => 'vungdoxuankthd@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Mai Văn Tuế',
          'email' => 'maivantue29@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Nguyễn Trọng Huy',
          'email' => 'nguyentronghuy22061998@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Nguyễn Trung Nguyên',
          'email' => 'nguyentrungnguyenth14@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Nguyễn Văn Nguyên',
          'email' => 'nguyenvn099@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Nguyễn Thị Ngân',
          'email' => 'ngannguyendt2haui@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Ngô Hồng Sơn',
          'email' => 'ngoson919597@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Lý Văn Phương',
          'email' => 'phuong.codeunited@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Nguyễn Tùng Bách',
          'email' => 'nguyentungbachholo@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Nguyễn Trung Thành',
          'email' => 'trungthanh2388@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Nguyễn Hải Yến',
          'email' => 'haiyentp.1204@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Vũ Thị Hoa',
          'email' => 'vuhoa11052000@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Ngô Tuấn Cường',
          'email' => 'tuancuongth88@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Phạm Tuấn Kiệt',
          'email' => 'ptkit1.0@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Hà Thái Việt',
          'email' => 'hathaiviet411@gmai.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
        [
          'name' => 'Tạ Thị Thủy',
          'email' => 'tathithuyvn89@gmail.com',
          'password' => Hash::make($password),
          'role_id' => 2,
          'status' => 1,
        ],
      ]);
//      DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
