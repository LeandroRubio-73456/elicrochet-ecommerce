<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('status', [
                'draft',      // Borrador (no visible)
                'active',     // Activo (visible en tienda)
                'out_of_stock', // Agotado
                'discontinued', // Descontinuado
                'archived'    // Archivado
            ])->default('draft')->after('stock');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};