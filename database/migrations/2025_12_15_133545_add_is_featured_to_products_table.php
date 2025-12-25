<?php

// database/migrations/XXXX_XX_XX_XXXXXX_add_is_featured_to_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Añade la columna 'is_featured' como booleano, por defecto falso (0)
            $table->boolean('is_featured')->default(false)->after('description'); // Colócala después de 'description' o donde prefieras
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
