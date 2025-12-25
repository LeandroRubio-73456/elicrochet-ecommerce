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
        // Update cart_items table
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            if (! Schema::hasColumn('cart_items', 'custom_order_id')) {
                $table->foreignId('custom_order_id')->nullable()->after('product_id')->constrained('orders')->onDelete('cascade');
            }
        });

        // Update order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            if (! Schema::hasColumn('order_items', 'custom_order_id')) {
                $table->foreignId('custom_order_id')->nullable()->after('product_id')->constrained('orders')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            if (Schema::hasColumn('cart_items', 'custom_order_id')) {
                $table->dropForeign(['custom_order_id']);
                $table->dropColumn('custom_order_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            if (Schema::hasColumn('order_items', 'custom_order_id')) {
                $table->dropForeign(['custom_order_id']);
                $table->dropColumn('custom_order_id');
            }
        });
    }
};
