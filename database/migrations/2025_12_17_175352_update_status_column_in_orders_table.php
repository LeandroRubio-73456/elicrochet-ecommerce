<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Modificamos la columna 'status' para que sea de 20 caracteres (suficiente)
            $table->string('status', 20)->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Si quieres revertir, pon la longitud original (ej. 5)
            $table->string('status', 5)->change(); 
        });
    }
};