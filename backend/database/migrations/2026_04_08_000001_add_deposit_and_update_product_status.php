<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add deposit_amount column and update product status enum to English values.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('deposit_amount', 10, 2)->default(0)->after('price');
        });

        DB::table('products')->whereIn('status', ['Con hang', 'Còn hàng'])->update(['status' => 'available']);
        DB::table('products')->whereIn('status', ['Dang cho thue', 'Đang cho thuê'])->update(['status' => 'rented']);
        DB::table('products')->whereIn('status', ['Bao tri', 'Bảo trì'])->update(['status' => 'maintenance']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('available', 'rented', 'maintenance', 'suspended', 'discontinued', 'out_of_stock') NOT NULL DEFAULT 'available'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('Con hang', 'Dang cho thue', 'Bao tri') NOT NULL DEFAULT 'Con hang'");
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('deposit_amount');
        });
    }
};
