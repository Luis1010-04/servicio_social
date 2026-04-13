<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Reportes extends Controller
{
    public function index()
    {
        $titulo = "Panel de Reportes Globales";
        $usuarios = User::all();

        $kpis = [
            'usuarios_activos' => User::where('activo', 1)->count(),
            'total_usuarios'   => User::count(),
            'total_maestros'   => DB::table('maestros_usuarios')->count(),
            'total_esclavos'   => DB::table('maestros_esclavos')->count(),
        ];

        return view('modules.reportes.index', compact('titulo', 'usuarios', 'kpis'));
    }

    // Listado detallado para el Modal de Maestros
    public function getMaestrosKpi()
    {
        $data = DB::table('maestros_usuarios as mu')
            ->join('maestros_catalogo as mc', 'mu.maestro_id', '=', 'mc.id')
            ->join('users as u', 'mu.user_id', '=', 'u.id')
            ->select(
                'mu.nombre as nombre_asignado', 
                'mc.modelo',                    
                'mu.numero_serie',              
                'u.name as cliente'             
            )
            ->get();

        return response()->json(['data' => $data]);
    }

    // Listado detallado para el Modal de Esclavos
    public function getEsclavosKpi()
    {
        $data = DB::table('maestros_esclavos as me')
            ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
            ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
            ->join('users as u', 'mu.user_id', '=', 'u.id')
            ->select(
                'me.nombre as nombre_vinculo',
                'ec.modelo',
                'me.numero_serie',
                'mu.nombre as maestro_padre',
                'u.name as cliente'
            )
            ->get();

        return response()->json(['data' => $data]);
    }

    // Inventario Global (El botón negro de "Ver Inventario MySQL")
    public function getInventarioGlobal()
        {
                        
            $client = new \InfluxDB2\Client([
                "url" => env('INFLUXDB_URL'),
                "token" => env('INFLUXDB_TOKEN'),
                "bucket" => env('INFLUXDB_BUCKET'),
                "org" => env('INFLUXDB_ORG'),
            ]);
            $queryApi = $client->createQueryApi();

            // 2. Consulta MySQL Principal
            $data = DB::table('maestros_esclavos as me')
                ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
                ->join('users as u', 'mu.user_id', '=', 'u.id')
                ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
                ->select(
                    'u.name as cliente',
                    'u.email',
                    'u.activo',
                    'mu.nombre as maestro',
                    'me.nombre as esclavo',
                    'me.numero_serie as serie',
                    'ec.id as ec_id', 
                    'ec.modelo'
                )
                ->get();

            // 3. Mapeo de datos
            $data = $data->map(function($item) use ($queryApi) {
                
                // Hacemos join con 'componentes' para saber el tipo (Sensor o Actuador)
                // --- PARTE A: SENSORES Y ACTUADORES ---
                        $componentes = DB::table('detalle_esclavo_componentes as dec')
                            ->join('componentes as c', 'dec.componente_id', '=', 'c.id')
                            ->where('dec.esclavo_id', $item->ec_id) 
                            ->select('c.tipo')
                            ->get();

                        // CAMBIO: Usamos stripos o un filtro más permisivo por si hay espacios o minúsculas
                        $item->sensores = $componentes->filter(fn($c) => stripos(trim($c->tipo), 'Sensor') !== false)->count();
                        $item->actuadores = $componentes->filter(fn($c) => stripos(trim($c->tipo), 'Actuador') !== false)->count();

                // Valores por defecto por si el nodo nunca se ha conectado
                // --- PARTE B: TELEMETRÍA (InfluxDB) ---
        $item->ultima_actividad = 'Sin datos';
        $item->online = false;

        try {
            // CAMBIO: Agregamos trim() por si el numero_serie tiene espacios invisibles
            $serieLimpio = trim($item->serie);

            $fluxQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
                |> range(start: -30d)
                |> filter(fn: (r) => r["dispositivo"] == "' . $serieLimpio . '")
                |> last()';

            $result = $queryApi->query($fluxQuery);

            if (!empty($result) && isset($result[0]->records) && count($result[0]->records) > 0) {
                $record = $result[0]->records[0];
                $timeString = $record->getTime(); 
                
                $fechaUltimoDato = Carbon::parse($timeString)->setTimezone('America/Mexico_City');
                $item->ultima_actividad = $fechaUltimoDato->format('d/m/Y H:i:s');

                if ($fechaUltimoDato->diffInSeconds(now('America/Mexico_City')) < 300) {
                    $item->online = true;
                }
            } else {
                // CAMBIO: Log para saber si Influx de verdad está devolviendo algo vacío
                Log::warning("InfluxDB no regresó datos para el dispositivo: {$serieLimpio}");
            }
        } catch (\Exception $e) {
            Log::error("Error InfluxDB para esclavo {$item->serie}: " . $e->getMessage());
        }

                return $item;
            });

            return response()->json(['data' => $data]);
    }

    public function getUsuariosKpi() 
    {
        $data = User::select('id', 'name', 'apellido', 'usuario', 'email', 'rol', 'activo')->get();
        return response()->json(['data' => $data]);
    }
}