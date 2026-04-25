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
                'name' => 'Outdoor Sai Gon',
                'email' => 'outdoor.saigon@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '45 Nguyen Trai, Q1, TP.HCM',
            ],
            [
                'name' => 'Camp Da Lat Store',
                'email' => 'camp.dalat@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '12 Phan Dinh Phung, Da Lat, Lam Dong',
            ],
            [
                'name' => 'Phuot Gear Ha Noi',
                'email' => 'phuot.hanoi@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '88 Bach Mai, Hai Ba Trung, Ha Noi',
            ],
            [
                'name' => 'Adventure Hub Nha Trang',
                'email' => 'adventure.nhatrang@camprent.vn',
                'password' => Hash::make('shop123'),
                'role' => 'shop',
                'address' => '23 Tran Phu, Nha Trang, Khanh Hoa',
            ],
        ];

        foreach ($shops as $shop) {
            User::create($shop);
        }
    }
}
