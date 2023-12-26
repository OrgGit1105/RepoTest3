<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
                User::VIAM_USER_ID => 1,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 10.5,
                'status' => 1,
            ],
            [
                'name' => 'Kohei Ikeda',
                'email' => 'i.kohei2323@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 1,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 28,
                'status' => 1,
            ],
            [
                'name' => 'Takaba Moeko',
                'email' => 'takaba.veho@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 1,
                User::ENTRY_DATE => Carbon::parse('2022-01-05')->format('Y-m-d'),
                User::PAID_OFF => 5.5,
                'status' => 1,
            ],
            [
                'name' => 'Vũ Anh Thư',
                'email' => 'thu.vu@veho-works.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 1,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 7.5,
                'status' => 1,
            ],
            [
                'name' => 'Phạm Thị Trang',
                'email' => 'phamthitrang290@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 9,
                'status' => 1,
            ],
            [
                'name' => 'Đinh Thị Vững',
                'email' => 'dbacninh99@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-03-15')->format('Y-m-d'),
                User::PAID_OFF => 5,
                'status' => 1,
            ],
            [
                'name' => 'Đỗ Xuân Vững',
                'email' => 'vungdoxuankthd@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 24.5,
                'status' => 1,
            ],
            [
                'name' => 'Mai Văn Tuế',
                'email' => 'maivantue29@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-02-20')->format('Y-m-d'),
                User::PAID_OFF => 5.5,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Trọng Huy',
                'email' => 'nguyentronghuy22061998@gmail.com',
                'password' => Hash::make($password),
                User::ENTRY_DATE => Carbon::parse('2022-09-19')->format('Y-m-d'),
                User::PAID_OFF => 9.5,
                User::VIAM_USER_ID => 2,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Trung Nguyên',
                'email' => 'nguyentrungnguyenth14@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-02-01')->format('Y-m-d'),
                User::PAID_OFF => 2.5,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Văn Nguyên',
                'email' => 'nguyenvn099@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-09-01')->format('Y-m-d'),
                User::PAID_OFF => 10.5,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Thị Ngân',
                'email' => 'ngannguyendt2haui@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 3,
                'status' => 1,
            ],
            [
                'name' => 'Ngô Hồng Sơn',
                'email' => 'ngoson919597@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-04-01')->format('Y-m-d'),
                User::PAID_OFF => 1.5,
                'status' => 1,
            ],
            [
                'name' => 'Lý Văn Phương',
                'email' => 'phuong.codeunited@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 17,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Tùng Bách',
                'email' => 'nguyentungbachholo@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-08-01')->format('Y-m-d'),
                User::PAID_OFF => 5,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Trung Thành',
                'email' => 'trungthanh2388@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-12-01')->format('Y-m-d'),
                User::PAID_OFF => 7.5,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Hải Yến',
                'email' => 'haiyentp.1204@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-02-20')->format('Y-m-d'),
                User::PAID_OFF => 0,
                'status' => 1,
            ],
            [
                'name' => 'Vũ Thị Hoa',
                'email' => 'vuhoa11052000@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 1,
                User::ENTRY_DATE => Carbon::parse('2023-05-01')->format('Y-m-d'),
                User::PAID_OFF => 4,
                'status' => 1,
            ],
            [
                'name' => 'Ngô Tuấn Cường',
                'email' => 'tuancuongth88@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 14.5,
                'status' => 1,
            ],
            [
                'name' => 'Phạm Tuấn Kiệt',
                'email' => 'ptkit1.0@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-04-12')->format('Y-m-d'),
                User::PAID_OFF => 16,
                'status' => 1,
            ],
            [
                'name' => 'Hà Thái Việt',
                'email' => 'hathaiviet411@gmai.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 0,
                'status' => 1,
            ],
            [
                'name' => 'Tạ Thị Thủy',
                'email' => 'tathithuyvn89@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 17,
                'status' => 1,
            ],
            [
                'name' => 'Đồng Việt Long',
                'email' => 'dongvietlong123@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-05-01')->format('Y-m-d'),
                User::PAID_OFF => 4,
                'status' => 1,
            ],
            [
                'name' => 'Hoàng Trung Nam',
                'email' => 'hoangtrungnam0000@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-07-05')->format('Y-m-d'),
                User::PAID_OFF => 3.5,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Đức Thủ',
                'email' => 'thubkit.hut@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2022-01-01')->format('Y-m-d'),
                User::PAID_OFF => 2,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Duy Khánh',
                'email' => 'Duykhanhnw@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-11-01')->format('Y-m-d'),
                User::PAID_OFF => 0,
                'status' => 1,
            ],
            [
                'name' => 'Nguyễn Thị Phương Hoa',
                'email' => 'ntphoa0209@gmail.com',
                'password' => Hash::make($password),
                User::VIAM_USER_ID => 2,
                User::ENTRY_DATE => Carbon::parse('2023-11-01')->format('Y-m-d'),
                User::PAID_OFF => 0,
                'status' => 1,
            ],
        ]);
//      DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
