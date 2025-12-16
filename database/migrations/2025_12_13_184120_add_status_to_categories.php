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
            // Definimos los posibles estados de una categoría
            $table->enum('status', [
                'active',   // Activa (Visible en la tienda, por defecto)
                'inactive', // Inactiva / Borrador (Oculta al público)
                'archived', // Archivado (Mantiene el historial)
            ])->default('active')->after('name'); // Se inserta después de la columna 'name'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Es crucial definir la lógica inversa para poder hacer rollback
            $table->dropColumn('status');
        });
    }
};
