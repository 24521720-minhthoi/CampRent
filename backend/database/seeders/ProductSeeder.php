<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $shops = User::where('role', 'shop')->get();
        $categories = Category::all()->keyBy('slug');

        $products = [
            // === Lều & Tarp (category 1) ===
            ['name' => 'Lều Naturehike Cloud Up 2', 'category' => 'leu-tarp', 'price' => 120000, 'deposit' => 500000, 'stock' => 8, 'description' => 'Lều siêu nhẹ 2 người, chống nước 3000mm, trọng lượng chỉ 1.5kg. Phù hợp trekking và camping nhẹ.', 'image_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600'],
            ['name' => 'Lều Coleman Sundome 4', 'category' => 'leu-tarp', 'price' => 180000, 'deposit' => 800000, 'stock' => 5, 'description' => 'Lều gia đình 4 người, khung nhôm chắc chắn, chống mưa tốt. Lý tưởng cho camping cuối tuần.', 'image_url' => 'https://images.unsplash.com/photo-1478827536114-da961b7f86d2?w=600'],
            ['name' => 'Lều MSR Hubba Hubba NX 2', 'category' => 'leu-tarp', 'price' => 250000, 'deposit' => 1500000, 'stock' => 3, 'description' => 'Lều cao cấp 2 người, siêu nhẹ 1.54kg, 2 cửa 2 vestibule. Dành cho trekking chuyên nghiệp.', 'image_url' => 'https://images.unsplash.com/photo-1537905569824-f89f14cceb68?w=600'],
            ['name' => 'Tarp DD Hammocks 3x3m', 'category' => 'leu-tarp', 'price' => 80000, 'deposit' => 300000, 'stock' => 12, 'description' => 'Tarp đa năng kích thước 3x3m, vải Ripstop chống nước. Dùng che nắng mưa hoặc làm shelter.', 'image_url' => 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?w=600'],
            ['name' => 'Lều Camping 8 Người Decathlon', 'category' => 'leu-tarp', 'price' => 350000, 'deposit' => 2000000, 'stock' => 2, 'description' => 'Lều nhóm lớn 8 người, 3 phòng ngủ riêng biệt, phòng khách rộng. Hoàn hảo cho nhóm bạn hay gia đình.', 'image_url' => 'https://images.unsplash.com/photo-1563299796-17596ed6b017?w=600'],

            // === Ba lô & Túi (category 2) ===
            ['name' => 'Ba lô Osprey Atmos AG 65L', 'category' => 'ba-lo-tui', 'price' => 150000, 'deposit' => 1000000, 'stock' => 6, 'description' => 'Ba lô trekking cao cấp 65L, hệ thống Anti-Gravity thoáng lưng, nhiều ngăn tiện dụng.', 'image_url' => 'https://images.unsplash.com/photo-1622260614153-03223fb72052?w=600'],
            ['name' => 'Ba lô Deuter Aircontact 40+10L', 'category' => 'ba-lo-tui', 'price' => 100000, 'deposit' => 600000, 'stock' => 10, 'description' => 'Ba lô 50L cho chuyến đi 2-3 ngày, hệ thống thoáng khí Aircontact, có áo mưa đi kèm.', 'image_url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600'],
            ['name' => 'Túi ngủ Naturehike CW280', 'category' => 'ba-lo-tui', 'price' => 60000, 'deposit' => 200000, 'stock' => 15, 'description' => 'Túi ngủ lông vũ, chịu lạnh đến 5°C, nặng chỉ 680g. Nhỏ gọn khi cuộn lại.', 'image_url' => 'https://images.unsplash.com/photo-1445308394109-4ec2920981b1?w=600'],
            ['name' => 'Túi chống nước Sea to Summit 20L', 'category' => 'ba-lo-tui', 'price' => 35000, 'deposit' => 150000, 'stock' => 20, 'description' => 'Túi khô chống nước 20L, lý tưởng cho kayak, cắm trại mưa, rafting.', 'image_url' => 'https://images.unsplash.com/photo-1501555088652-021faa106b9b?w=600'],

            // === Nấu nướng & Ăn uống (category 3) ===
            ['name' => 'Bếp gas mini Kovea Spider', 'category' => 'nau-nuong-an-uong', 'price' => 50000, 'deposit' => 300000, 'stock' => 12, 'description' => 'Bếp gas đầu xoay gọn nhẹ, công suất 2800W, phù hợp nấu ăn dã ngoại cho 2-4 người.', 'image_url' => 'https://images.unsplash.com/photo-1510672981848-a1c4f1cb5ccf?w=600'],
            ['name' => 'Bộ nồi camping Fire-Maple 4-5 người', 'category' => 'nau-nuong-an-uong', 'price' => 45000, 'deposit' => 200000, 'stock' => 8, 'description' => 'Bộ nồi nhôm anodized gồm 2 nồi + 1 chảo + bát, phù hợp nhóm 4-5 người.', 'image_url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600'],
            ['name' => 'Thùng giữ lạnh Coleman 28L', 'category' => 'nau-nuong-an-uong', 'price' => 70000, 'deposit' => 400000, 'stock' => 6, 'description' => 'Thùng giữ lạnh 28 lít, giữ đá đến 3 ngày. Có bánh xe và tay kéo tiện di chuyển.', 'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600'],
            ['name' => 'Bình lọc nước Sawyer Squeeze', 'category' => 'nau-nuong-an-uong', 'price' => 40000, 'deposit' => 250000, 'stock' => 10, 'description' => 'Bình lọc nước di động, lọc 99.99% vi khuẩn, dùng suối hoặc sông. Cần thiết cho trekking xa.', 'image_url' => 'https://images.unsplash.com/photo-1525385444278-50f5f04c1dfa?w=600'],

            // === Ánh sáng & Năng lượng (category 4) ===
            ['name' => 'Đèn lều Ledlenser ML6', 'category' => 'anh-sang-nang-luong', 'price' => 40000, 'deposit' => 200000, 'stock' => 15, 'description' => 'Đèn lều 750 lumen, sạc USB-C, có thể treo hoặc đặt bàn. Pin 200 giờ ở chế độ thấp.', 'image_url' => 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?w=600'],
            ['name' => 'Đèn pin Fenix HM65R-DT', 'category' => 'anh-sang-nang-luong', 'price' => 55000, 'deposit' => 350000, 'stock' => 8, 'description' => 'Đèn đội đầu 1500 lumen, 2 bóng LED trắng + đỏ, chống nước IP68. Trail running và leo núi đêm.', 'image_url' => 'https://images.unsplash.com/photo-1541480601022-2308c0f02487?w=600'],
            ['name' => 'Pin sạc EcoFlow River 2', 'category' => 'anh-sang-nang-luong', 'price' => 200000, 'deposit' => 2000000, 'stock' => 4, 'description' => 'Trạm sạc di động 256Wh, sạc nhanh 1 giờ, 6 cổng output. Sạc laptop, quạt, tủ lạnh mini.', 'image_url' => 'https://images.unsplash.com/photo-1609220136736-443140cffec6?w=600'],
            ['name' => 'Tấm pin năng lượng mặt trời 60W', 'category' => 'anh-sang-nang-luong', 'price' => 80000, 'deposit' => 500000, 'stock' => 5, 'description' => 'Tấm pin solar gấp gọn 60W, sạc trực tiếp cho điện thoại hoặc trạm sạc. Camping off-grid.', 'image_url' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=600'],

            // === Nghỉ ngơi & Giấc ngủ (category 5) ===
            ['name' => 'Đệm hơi tự bơm Thermarest NeoAir', 'category' => 'nghi-ngoi-giac-ngu', 'price' => 80000, 'deposit' => 400000, 'stock' => 10, 'description' => 'Đệm hơi tự bơm R-value 4.2, cách nhiệt tốt, nhỏ gọn khi cuộn. Ngủ trên mọi địa hình.', 'image_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600'],
            ['name' => 'Võng Eno DoubleNest', 'category' => 'nghi-ngoi-giac-ngu', 'price' => 50000, 'deposit' => 200000, 'stock' => 14, 'description' => 'Võng đôi chịu tải 180kg, vải Nylon 70D thoáng mát. Kèm dây treo và carabiner.', 'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600'],
            ['name' => 'Túi ngủ mùa đông -10°C', 'category' => 'nghi-ngoi-giac-ngu', 'price' => 100000, 'deposit' => 600000, 'stock' => 5, 'description' => 'Túi ngủ lông vũ 800FP, chịu lạnh -10°C, nặng 1.2kg. Cho trekking mùa đông, leo núi cao.', 'image_url' => 'https://images.unsplash.com/photo-1445308394109-4ec2920981b1?w=600'],

            // === Dụng cụ & Phụ kiện (category 6) ===
            ['name' => 'Dao đa năng Victorinox Huntsman', 'category' => 'dung-cu-phu-kien', 'price' => 30000, 'deposit' => 200000, 'stock' => 12, 'description' => 'Dao đa năng 15 chức năng: dao, kéo, cưa, mở nút chai, tuốc nơ vít. Swiss Made.', 'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=600'],
            ['name' => 'Bộ sơ cứu First Aid Kit Pro', 'category' => 'dung-cu-phu-kien', 'price' => 25000, 'deposit' => 100000, 'stock' => 20, 'description' => 'Bộ sơ cứu 120 món: băng gạc, thuốc sát trùng, kéo y tế, nẹp, chỉ khâu. Bắt buộc cho outdoor.', 'image_url' => 'https://images.unsplash.com/photo-1603398938378-e54eab446dde?w=600'],
            ['name' => 'Gậy trekking Black Diamond Trail', 'category' => 'dung-cu-phu-kien', 'price' => 35000, 'deposit' => 200000, 'stock' => 16, 'description' => 'Gậy trekking nhôm 3 khúc, điều chỉnh 63-140cm, tay cầm EVA foam. Giảm tải đầu gối.', 'image_url' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=600'],
            ['name' => 'Ống nhòm Nikon Aculon 10x50', 'category' => 'dung-cu-phu-kien', 'price' => 45000, 'deposit' => 500000, 'stock' => 6, 'description' => 'Ống nhòm 10x50, field of view 6.5°, lớp phủ đa lớp. Xem chim, ngắm cảnh, thiên văn cơ bản.', 'image_url' => 'https://images.unsplash.com/photo-1502472584811-0a2f2feb8968?w=600'],

            // === Bàn ghế & Nội thất (category 7) ===
            ['name' => 'Ghế xếp Helinox Chair One', 'category' => 'ban-ghe-noi-that', 'price' => 60000, 'deposit' => 300000, 'stock' => 18, 'description' => 'Ghế gấp siêu nhẹ 960g, chịu tải 145kg, khung DAC nhôm. Setup trong 30 giây.', 'image_url' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?w=600'],
            ['name' => 'Bàn camping gấp nhôm 90cm', 'category' => 'ban-ghe-noi-that', 'price' => 50000, 'deposit' => 250000, 'stock' => 10, 'description' => 'Bàn gấp nhôm khung chắc, mặt bàn cuộn gọn, kích thước 90x53cm. Đủ cho 4 người ăn uống.', 'image_url' => 'https://images.unsplash.com/photo-1517824806704-9040b037703b?w=600'],
            ['name' => 'Bạt trải sàn Oxford 3x4m', 'category' => 'ban-ghe-noi-that', 'price' => 30000, 'deposit' => 100000, 'stock' => 25, 'description' => 'Bạt trải sàn vải Oxford 420D chống nước, kích thước 3x4m. Dùng trải dưới lều hoặc làm khu vực sinh hoạt.', 'image_url' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600'],
        ];

        foreach ($products as $product) {
            $catSlug = $product['category'];
            $category = $categories[$catSlug] ?? null;
            if (!$category) continue;

            $shop = $shops->random();

            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'deposit_amount' => $product['deposit'],
                'stock' => $product['stock'],
                'image_url' => $product['image_url'],
                'images' => [$product['image_url']],
                'status' => 'available',
                'category_id' => $category->id,
                'shop_id' => $shop->id,
            ]);
        }
    }
}
