<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'shipping_address')) {
                $table->string('shipping_address')->nullable();
                $table->string('shipping_city')->nullable();
                $table->string('shipping_zip')->nullable();
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'type')) {
                $table->string('type', 20)->default('standard')->after('status'); // standard, catalog, custom
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'custom_description')) {
                $table->text('custom_description')->nullable()->after('price');
            }
            if (!Schema::hasColumn('order_items', 'images')) {
                $table->json('images')->nullable()->after('custom_description');
            }
            if (Schema::hasColumn('order_items', 'product_id')) {
                // Ensure product_id is nullable
                $table->unsignedBigInteger('product_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['shipping_address', 'shipping_city', 'shipping_zip']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['custom_description', 'images']);
            // Reverting product_id to not nullable is risky if nulls exist
        });
    }
};
