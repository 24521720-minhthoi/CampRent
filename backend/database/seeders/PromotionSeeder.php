<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $tentCategory = Category::where('slug', 'leu-tarp')->first();
        $cookingCategory = Category::where('slug', 'nau-nuong-an-uong')->first();
        $tent = Product::where('slug', 'leu-naturehike-cloud-up-2')->first();

        $weekendSale = Promotion::create([
            'name' => 'Giam 15% leu cuoi tuan',
            'type' => Promotion::TYPE_PERCENT,
            'value' => 15,
            'scope' => Promotion::SCOPE_CATEGORY,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'is_active' => true,
        ]);

        if ($tentCategory) {
            $weekendSale->targets()->create([
                'target_type' => 'category',
                'target_id' => $tentCategory->id,
            ]);
        }

        Promotion::create([
            'name' => 'Voucher CAMP50K',
            'code' => 'CAMP50K',
            'type' => Promotion::TYPE_FIXED,
            'value' => 50000,
            'scope' => Promotion::SCOPE_ALL,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(45),
            'usage_limit' => 100,
            'per_user_limit' => 2,
            'min_order_value' => 500000,
            'status' => 'active',
            'is_active' => true,
        ]);

        $bogo = Promotion::create([
            'name' => 'Mua 1 tang 1 bo nau an',
            'type' => Promotion::TYPE_BOGO,
            'value' => 1,
            'scope' => Promotion::SCOPE_CATEGORY,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(14),
            'status' => 'active',
            'is_active' => true,
        ]);

        if ($cookingCategory) {
            $bogo->targets()->create([
                'target_type' => 'category',
                'target_id' => $cookingCategory->id,
            ]);
        }

        if ($tent) {
            $flashSale = Promotion::create([
                'name' => 'Flash sale leu Naturehike',
                'type' => Promotion::TYPE_FIXED,
                'value' => 30000,
                'scope' => Promotion::SCOPE_PRODUCT,
                'start_date' => now()->subHours(2),
                'end_date' => now()->addHours(8),
                'usage_limit' => 20,
                'status' => 'active',
                'is_active' => true,
            ]);

            $flashSale->targets()->create([
                'target_type' => 'product',
                'target_id' => $tent->id,
            ]);
        }
    }
}
