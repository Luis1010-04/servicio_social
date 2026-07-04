<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InfluxDB2\Client;

class dashboardcontroller extends Controller
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

        
        $ultimosMaestrosRaw = DB::table('maestros_catalogo')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Limpiamos las fechas con Carbon antes de mandarlas a la vista
        $ultimosMaestros = $ultimosMaestrosRaw->map(function ($maestro) {
            
            $maestro->fecha_formateada = Carbon::parse($maestro->created_at)->format('Y-m-d H:i:s');
            return $maestro;
        });

        // 3. Verificación de Influx
        $influxStatus = $this->checkInfluxStatus();

        return view('modules.dashboard.home', compact('titulo', 'stats', 'ultimosMaestros', 'influxStatus'));
    }

    public function pendiente()
    {
        $titulo = "404 - Página No Encontrada";

        return view('modules.pendiente.index', compact('titulo'));
    }


    private function checkInfluxStatus()
    {
        try {
            $client = new Client([
                "url" => config('services.influxdb.url'),
                "token" => config('services.influxdb.token'),
                "bucket" => config('services.influxdb.bucket'),
                "org" => config('services.influxdb.org'),
                "timeout" => 3 
            ]);
            
            return $client->ping() ? 'Online' : 'Offline';
        } catch (\Exception $e) {
            return 'Offline';
        }
    }
}   
