<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('componentes', function (Blueprint $table) {
            $table->renameColumn('ruta_incono', 'ruta_icono');
        });
    }

    public function down(): void
    {
        Schema::table('componentes', function (Blueprint $table) {
            $table->renameColumn('ruta_icono', 'ruta_incono');
        });
    }
};
