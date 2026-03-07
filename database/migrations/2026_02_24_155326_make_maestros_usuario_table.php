<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maestros_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maestro_id')->constrained('maestros_catalogo')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ubicacion_id')->constrained('ubicaciones')->cascadeOnDelete();
            $table->string('numero_serie', 100)->unique();
            $table->string('nombre');
            $table->string('imagen_ruta')->default('default.png');
            $table->string('localizacion'); // Nuevo campo para guardar el nombre de la ubicación
            $table->string('topico');
            $table->string('Broker')->unique();
            $table->text('descripcion')->nullable();
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
