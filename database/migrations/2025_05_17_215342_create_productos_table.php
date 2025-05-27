<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);  // 10 dígitos en total, 2 decimales
            $table->integer('stock')->default(0);
            $table->string('imagen_id')->nullable();   // Para guardar el public_id de Cloudinary
            $table->string('imagen_tag')->nullable();  // Para el tag de seguimiento de Cloudinary
            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index('nombre');
            $table->index('imagen_tag');
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
