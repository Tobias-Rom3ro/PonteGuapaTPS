<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['servicio', 'producto']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Cliente
            $table->foreignId('vendedor_id')->constrained('users')->onDelete('cascade'); // Quien registra la venta
            $table->foreignId('cita_id')->nullable()->constrained()->onDelete('set null'); // Para ventas de servicios
            $table->foreignId('producto_id')->nullable()->constrained()->onDelete('set null'); // Para ventas de productos
            $table->integer('cantidad')->default(1); // Cantidad de productos (siempre 1 para servicios)
            $table->decimal('precio_unitario', 10, 2); // Precio al momento de la venta
            $table->decimal('total', 10, 2); // Total de la venta
            $table->text('notas')->nullable();
            $table->timestamp('fecha_venta');
            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index(['tipo', 'fecha_venta']);
            $table->index('user_id');
            $table->index('vendedor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
