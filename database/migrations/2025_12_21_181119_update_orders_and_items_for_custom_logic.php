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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('type', 20)->default('standard')->after('status'); // standard, custom
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->text('custom_description')->nullable()->after('price');
            $table->json('images')->nullable()->after('custom_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Revert product_id to not null (caution: data loss if customs exist)
            // We won't strict revert to not null to avoid errors if data exists, just dropping cols
            $table->dropColumn(['custom_description', 'images']);
        });
    }
};
