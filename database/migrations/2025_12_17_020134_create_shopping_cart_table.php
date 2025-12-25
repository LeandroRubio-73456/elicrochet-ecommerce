<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_cart', function (Blueprint $table) {
            $table->string('id'); // ID único del carrito (user_id para autenticados o session_id para invitados)
            $table->string('instance')->default('default'); // Instancia del carrito (para múltiples carritos)
            $table->longText('content'); // Contenido serializado del carrito en JSON
            $table->nullableTimestamps(); // created_at y updated_at

            $table->primary(['id', 'instance']); // Clave primaria compuesta
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_cart');
    }
};
