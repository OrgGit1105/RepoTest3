<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UpdateNameUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = [
            'Nawa_Hinako',
            'Kohei_Ikeda',
            'Takaba_Moeko',
            'Vu_Anh_Thu',
            'Pham_Thi_Trang',
            'Dinh_Thi_Vung',
            'Do_Xuan_Vung',
            'Mai_Van_Tue',
            'Nguyen_Trong_Huy',
            'Nguyen_Trung_Nguyen',
            'Nguyen_Van_Nguyen',
            'Nguyen_Thi_Ngan',
            'Ngo_Hong_Son',
            'Ly_Van_Phuong',
            'Nguyen_Tung_Bach',
            'Nguyen_Trung_Thanh',
            'Nguyen_Hai_Yen',
            'Vu_Thi_Hoa',
            'Ngo_Tuan_Cuong',
            'Pham_Tuan_Kiet',
            'Ha_Thai_Viet',
            'Ta_Thi_Thuy',
            'Dong_Viet_Long',
            'Hoang_Trung_Nam',
            'Nguyen_Duc_Thu',
            'Nguyen_Duy_Khanh',
            'Nguyen_Thi_Phuong_Hoa'
        ];
        $users = User::query()->where('id', '<=', 27)->get();
        if ($users->count() == 27) {
            foreach ($users as $index => $user) {
                $user->name = $names[$index];
                $user->save();
            }
        }
    }
}
