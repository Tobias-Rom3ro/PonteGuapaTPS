<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->boolean('es_combo')->default(false)->after('precio');
            $table->json('servicios_incluidos')->nullable()->after('es_combo');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn(['es_combo', 'servicios_incluidos']);
        });
    }
};
