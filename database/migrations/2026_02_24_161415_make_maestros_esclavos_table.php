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
        Schema::create('maestros_esclavos', function (Blueprint $table) {
            $table->id();
            // IMPORTANTE: Apunta a maestros_usuarios, la instancia física
            $table->foreignId('maestro_id')->constrained('maestros_usuarios')->cascadeOnDelete();
            $table->foreignId('esclavo_id')->constrained('esclavos_catalogo')->cascadeOnDelete();
            $table->foreignId('ubicacion_id')->constrained('ubicaciones')->cascadeOnDelete();
            $table->string('numero_serie', 100)->unique();
            $table->string('nombre');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
