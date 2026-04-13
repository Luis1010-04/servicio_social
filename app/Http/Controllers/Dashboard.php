<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Import Carbon para limpiar la fecha SQL
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InfluxDB2\Client;

class Dashboard extends Controller
{
    public function index()
    {
        $titulo = "Dashboard Administrador";

        // 1. Estadísticas Globales usando nombres exactos de tablas (diagrama)
        $stats = [
            'usuarios' => DB::table('users')->count(),
            'maestros' => DB::table('maestros_catalogo')->count(),
            'esclavos' => DB::table('esclavos_catalogo')->count(),
            'unidades' => DB::table('unidades_de_medida')->count(),
        ];

        // 2. Últimos Equipos Registrados y CORRECCIÓN DE FECHA
        // Eloquent/QueryBuilder por defecto mandan 'Y-m-d H:i:s.u' (con microsegundos).
        // Si tu base de datos SQL no soporta microsegundos en 'created_at', dará error.
        
        $ultimosMaestrosRaw = DB::table('maestros_catalogo')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Limpiamos las fechas con Carbon antes de mandarlas a la vista
        $ultimosMaestros = $ultimosMaestrosRaw->map(function ($maestro) {
            // Aseguramos formato SQL limpio: YYYY-MM-DD HH:MM:SS
            $maestro->fecha_formateada = Carbon::parse($maestro->created_at)->format('Y-m-d H:i:s');
            return $maestro;
        });

        // 3. Verificación de Influx
        $influxStatus = $this->checkInfluxStatus();

        return view('modules.dashboard.home', compact('titulo', 'stats', 'ultimosMaestros', 'influxStatus'));
    }

    private function checkInfluxStatus()
    {
        try {
            $client = new Client([
                "url" => env('INFLUXDB_URL'),
                "token" => env('INFLUXDB_TOKEN'),
                "bucket" => env('INFLUXDB_BUCKET'),
                "org" => env('INFLUXDB_ORG'),
                "timeout" => 3 
            ]);
            
            return $client->ping() ? 'Online' : 'Offline';
        } catch (\Exception $e) {
            return 'Offline';
        }
    }
}   