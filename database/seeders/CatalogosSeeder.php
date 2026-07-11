<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Usuario por Defecto
        DB::table('users')->insert([
            'name'       => 'Admin',
            'apellido'   => 'Sistema',
            'usuario'    => 'admin', 
            'email'      => 'admin@admin.com',
            'password'   => Hash::make('admin'), // Encriptación obligatoria
            'rol'        => 'Admin', // <-- Asignamos explícitamente el rol de administrador
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Crear Unidades de Medida 
        // (¡Corregido! Quitamos los timestamps porque tu tabla no los tiene)
        DB::table('unidades_de_medida')->insert([
            ['id' => 1, 'nombre' => 'Grados Celsius (°C)'],
            ['id' => 2, 'nombre' => 'Porcentaje (%)'],
            ['id' => 3, 'nombre' => 'Voltios (V)'],
            ['id' => 4, 'nombre' => 'Amperios (A)'],
            ['id' => 5, 'nombre' => 'Milímetros (mm)'],
        ]);

        // 3. Crear Catálogo de Equipos Maestros
        DB::table('maestros_catalogo')->insert([
            'nombre'      => 'Maestro biobit',
            'modelo'      => 'M01',
            'descripcion' => 'Equipo maestro principal', // <-- Añadido porque tu BD lo exige
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 4. Crear Catálogo de Equipos Esclavos
        DB::table('esclavos_catalogo')->insert([
            'modelo'     => 'Esclavo biobit, E01',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Crear Componentes Típicos de un Proyecto IoT
        DB::table('componentes')->insert([
            // Sensores (requieren unidad_id)
            [
                'unidad_id'   => 1, // °C
                'nombre'      => 'Sensor de Temperatura (DHT22)',
                'tipo'        => 'Sensor',
                'descripcion' => 'Sensor digital para medir la temperatura ambiental.',
                'ruta_icono' => 'bi-thermometer-half',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'unidad_id'   => 2, // %
                'nombre'      => 'Sensor de Humedad Relativa (DHT22)',
                'tipo'        => 'Sensor',
                'descripcion' => 'Sensor digital para medir el porcentaje de humedad en el aire.',
                'ruta_icono' => 'bi-moisture',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'unidad_id'   => 5, // mm
                'nombre'      => 'Sensor Ultrasónico de Distancia (HC-SR04)',
                'tipo'        => 'Sensor',
                'descripcion' => 'Sensor para medir niveles u objetos por proximidad.',
                'ruta_icono' => 'bi-arrow-left-right',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'unidad_id'   => 2, // %
                'nombre'      => 'Sensor de Humedad del Suelo (YL-69)',
                'tipo'        => 'Sensor',
                'descripcion' => 'Sensor analógico para monitoreo de riego.',
                'ruta_icono' => 'bi-droplet-half',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            
            // Actuadores (con unidad_id en NULL)
            [
                'unidad_id'   => null,
                'nombre'      => 'Módulo Relevador (Relay 5V)',
                'tipo'        => 'Actuador',
                'descripcion' => 'Interruptor controlado digitalmente para encender/apagar cargas eléctricas.',
                'ruta_icono' => 'bi-toggle-on',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'unidad_id'   => null,
                'nombre'      => 'Servomotor (SG90)',
                'tipo'        => 'Actuador',
                'descripcion' => 'Actuador para control de posición angular o compuertas.',
                'ruta_icono' => 'bi-gear-wide-connected',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'unidad_id'   => null,
                'nombre'      => 'Bomba de Agua DC 5V/12V',
                'tipo'        => 'Actuador',
                'descripcion' => 'Mini bomba sumergible para sistemas de automatización de riego.',
                'ruta_icono' => 'bi-water',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}