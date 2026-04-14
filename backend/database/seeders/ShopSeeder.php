<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $shops = [
            [
                'name' => 'Outdoor Sài Gòn',
                'email' => 'outdoor.saigon@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '45 Nguyễn Trãi, Q1, TP.HCM',
            ],
            [
                'name' => 'Camp Đà Lạt Store',
                'email' => 'camp.dalat@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '12 Phan Đình Phùng, Đà Lạt, Lâm Đồng',
            ],
            [
                'name' => 'Phượt Gear Hà Nội',
                'email' => 'phuot.hanoi@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '88 Bạch Mai, Hai Bà Trưng, Hà Nội',
            ],
            [
                'name' => 'Adventure Hub Nha Trang',
                'email' => 'adventure.nhatrang@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '23 Trần Phú, Nha Trang, Khánh Hòa',
            ],
        ];

        foreach ($shops as $shop) {
            User::create($shop);
        }
    }
}
