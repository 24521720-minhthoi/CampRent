<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lều & Tarp',
                'slug' => 'leu-tarp',
                'description' => 'Lều cắm trại từ 1-8 người, tarpaulin, lều treo hammock, và phụ kiện che chắn cho mọi loại địa hình.',
                'image_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=400',
            ],
            [
                'name' => 'Ba lô & Túi',
                'slug' => 'ba-lo-tui',
                'description' => 'Ba lô trekking 30L-80L, túi đựng đồ chống nước, túi ngủ và phụ kiện mang vác cho chuyến phượt.',
                'image_url' => 'https://images.unsplash.com/photo-1622260614153-03223fb72052?w=400',
            ],
            [
                'name' => 'Nấu nướng & Ăn uống',
                'slug' => 'nau-nuong-an-uong',
                'description' => 'Bếp gas mini, bộ nồi dã ngoại, bình giữ nhiệt, bộ dao kéo camping, thùng giữ lạnh.',
                'image_url' => 'https://images.unsplash.com/photo-1510672981848-a1c4f1cb5ccf?w=400',
            ],
            [
                'name' => 'Ánh sáng & Năng lượng',
                'slug' => 'anh-sang-nang-luong',
                'description' => 'Đèn pin, đèn lều, đèn treo, sạc dự phòng năng lượng mặt trời, pin sạc outdoor.',
                'image_url' => 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?w=400',
            ],
            [
                'name' => 'Nghỉ ngơi & Giấc ngủ',
                'slug' => 'nghi-ngoi-giac-ngu',
                'description' => 'Túi ngủ mọi mùa, đệm hơi tự bơm, võng treo, gối hơi, thảm cách nhiệt.',
                'image_url' => 'https://images.unsplash.com/photo-1445308394109-4ec2920981b1?w=400',
            ],
            [
                'name' => 'Dụng cụ & Phụ kiện',
                'slug' => 'dung-cu-phu-kien',
                'description' => 'Dao đa năng, rìu camping, dây thừng, la bàn, kính viễn vọng, bộ sơ cứu, bộ dụng cụ sửa chữa.',
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
            ],
            [
                'name' => 'Bàn ghế & Nội thất',
                'slug' => 'ban-ghe-noi-that',
                'description' => 'Ghế xếp gọn, bàn camping, kệ đa năng, bạt trải sàn, phụ kiện setup camp.',
                'image_url' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?w=400',
            ],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
