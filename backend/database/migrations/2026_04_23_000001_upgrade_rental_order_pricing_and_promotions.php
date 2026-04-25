<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->string('type');
            $table->decimal('value', 12, 2);
            $table->string('scope')->default('all');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_user_limit')->nullable();
            $table->decimal('min_order_value', 12, 2)->default(0);
            $table->boolean('new_user_only')->default(false);
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['status', 'is_active', 'start_date', 'end_date'], 'promotions_active_window_index');
            $table->index(['scope', 'status'], 'promotions_scope_status_index');
        });

        $this->normalizeOrderStatusColumn();

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('rental_subtotal', 12, 2)->default(0)->after('end_date');
            $table->decimal('deposit_total', 12, 2)->default(0)->after('rental_subtotal');
            $table->decimal('insurance_fee', 12, 2)->default(0)->after('deposit_total');
            $table->decimal('shipping_fee', 12, 2)->default(0)->after('insurance_fee');
            $table->decimal('discount_total', 12, 2)->default(0)->after('shipping_fee');
            $table->json('pricing_snapshot')->nullable()->after('total_amount');
            $table->timestamp('paid_at')->nullable()->after('pricing_snapshot');
            $table->timestamp('completed_at')->nullable()->after('paid_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            $table->timestamp('returned_at')->nullable()->after('cancelled_at');
            $table->timestamp('refunded_at')->nullable()->after('returned_at');

            $table->index(['status', 'created_at'], 'orders_status_created_at_index');
            $table->index('paid_at', 'orders_paid_at_index');
            $table->index('completed_at', 'orders_completed_at_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('quantity');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('unit_deposit', 12, 2)->default(0)->after('price');
            $table->decimal('rental_subtotal', 12, 2)->default(0)->after('days');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('rental_subtotal');
            $table->decimal('deposit_total', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('total_amount', 12, 2)->default(0)->after('subtotal');
            $table->foreignId('promotion_id')->nullable()->after('total_amount')->constrained('promotions')->nullOnDelete();
            $table->json('pricing_snapshot')->nullable()->after('promotion_id');

            $table->index(['product_id', 'start_date', 'end_date'], 'order_items_product_dates_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
            $table->index(['status', 'created_at'], 'payments_status_created_at_index');
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_role')->nullable();
            $table->string('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at'], 'order_status_histories_order_created_index');
            $table->index('new_status', 'order_status_histories_new_status_index');
        });

        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->integer('quantity');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['product_id', 'start_date', 'end_date', 'status'], 'reservations_product_dates_status_index');
            $table->index(['order_id', 'status'], 'reservations_order_status_index');
        });

        Schema::create('promotion_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->string('target_type');
            $table->unsignedBigInteger('target_id');
            $table->timestamps();

            $table->unique(['promotion_id', 'target_type', 'target_id'], 'promotion_targets_unique');
            $table->index(['target_type', 'target_id'], 'promotion_targets_lookup_index');
        });

        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['promotion_id', 'order_id'], 'promotion_usages_promotion_order_unique');
            $table->index(['promotion_id', 'user_id'], 'promotion_usages_promotion_user_index');
        });

        Schema::create('order_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('type');
            $table->decimal('value', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->json('snapshot')->nullable();
            $table->timestamps();

            $table->index('order_id', 'order_discounts_order_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_discounts');
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('promotion_targets');
        Schema::dropIfExists('inventory_reservations');
        Schema::dropIfExists('order_status_histories');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_created_at_index');
            $table->dropColumn(['paid_at', 'refunded_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
            $table->dropIndex('order_items_product_dates_index');
            $table->dropColumn([
                'start_date',
                'end_date',
                'unit_deposit',
                'rental_subtotal',
                'discount_amount',
                'deposit_total',
                'total_amount',
                'promotion_id',
                'pricing_snapshot',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_at_index');
            $table->dropIndex('orders_paid_at_index');
            $table->dropIndex('orders_completed_at_index');
            $table->dropColumn([
                'rental_subtotal',
                'deposit_total',
                'insurance_fee',
                'shipping_fee',
                'discount_total',
                'pricing_snapshot',
                'paid_at',
                'completed_at',
                'cancelled_at',
                'returned_at',
                'refunded_at',
            ]);
        });

        Schema::dropIfExists('promotions');
    }

    private function normalizeOrderStatusColumn(): void
    {
        DB::table('orders')->where('status', 'processing')->update(['status' => 'packing']);
        DB::table('orders')->where('status', 'shipped')->update(['status' => 'shipping']);

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });

            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'packing', 'shipping', 'delivered', 'completed', 'cancelled', 'returned', 'refunded') NOT NULL DEFAULT 'pending'");
    }
};
