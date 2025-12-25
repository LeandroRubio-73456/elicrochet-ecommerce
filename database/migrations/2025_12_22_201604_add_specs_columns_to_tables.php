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
        Schema::table('categories', function (Blueprint $table) {
            $table->json('required_specs')->nullable()->after('description');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->json('specs')->nullable()->after('description');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('custom_specs')->nullable()->after('custom_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('required_specs');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('specs');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('custom_specs');
        });
    }
};
