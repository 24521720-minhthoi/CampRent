<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // === Admin User ===
        User::create([
            'name' => 'Admin CampRent',
            'email' => 'admin@camprent.vn',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'address' => 'Khu phố 6, P.Linh Trung, TP.Thủ Đức, TP.HCM',
        ]);

        // === Shop Users ===
        $this->call(ShopSeeder::class);

        // === Categories ===
        $this->call(CategorySeeder::class);

        // === Products ===
        $this->call(ProductSeeder::class);

        // === Customer User ===
        User::create([
            'name' => 'Khách Hàng Demo',
            'email' => 'customer@camprent.vn',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'address' => '227 Nguyễn Văn Cừ, Q5, TP.HCM',
        ]);
    }
}
