<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfluxDB2\Client;
use Carbon\Carbon;

class UserDashboard extends Controller
{
    public function index()
    {
        $titulo = 'Dashboard';
        $user = Auth::user();

        // 1. Obtener esclavos según la jerarquía de tu diagrama:
        // users -> maestros_usuarios (mu) -> maestros_esclavos (me) -> esclavos_catalogo (ec)
        $esclavos = DB::table('maestros_usuarios as mu')
            ->join('maestros_esclavos as me', 'mu.id', '=', 'me.maestro_id')
            ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
            ->where('mu.user_id', $user->id)
            ->select(
                'me.numero_serie',       // Este es el tag 'dispositivo' en Influx
                'me.nombre as nombre_esclavo', 
                'ec.modelo',
                'mu.nombre as nombre_maestro' // Opcional, por si quieres agrupar por maestro
            )
            ->get();

        return view('modules.vistasUsuario.dashboard.home', compact('esclavos','titulo'));
    }

    public function getRealTimeData()
    {
        try {
            $user = Auth::id();

            // 1. Obtenemos solo los números de serie de los esclavos que pertenecen al usuario
            $numeroSeries = DB::table('maestros_usuarios as mu')
                ->join('maestros_esclavos as me', 'mu.id', '=', 'me.maestro_id')
                ->where('mu.user_id', $user)
                ->pluck('me.numero_serie') 
                ->toArray();

            if (empty($numeroSeries)) {
                return response()->json([]);
            }

            // 2. Cliente InfluxDB
            $client = new Client([
                "url"    => env('INFLUXDB_URL'),
                "token"  => env('INFLUXDB_TOKEN'),
                "bucket" => env('INFLUXDB_BUCKET'),
                "org"    => env('INFLUXDB_ORG'),
                "timeout" => 5
            ]);

            $queryApi = $client->createQueryApi();

            // 3. Filtro dinámico para la query
            $filtros = collect($numeroSeries)
                ->map(fn($s) => 'r["dispositivo"] == "' . trim($s) . '"')
                ->implode(' or ');

            $fluxQuery = "from(bucket: \"" . env('INFLUXDB_BUCKET') . "\")
                |> range(start: -15m)
                |> filter(fn: (r) => {$filtros})
                |> keep(columns: [\"_time\", \"_field\", \"_value\", \"dispositivo\"])
                |> sort(columns: [\"_time\"], desc: false)"; // Ordenado para la gráfica

            $records = $queryApi->query($fluxQuery);
            
            $data = [];
            foreach ($records as $table) {
                foreach ($table->records as $record) {
                    $serie = $record["dispositivo"];
                    $field = $record["_field"];
                    $value = $record["_value"];
                    // Formateamos la hora para que el eje X de Chart.js sea legible
                    $time  = Carbon::parse($record->getTime())->format('H:i:s');

                    // Estructura optimizada para Chart.js
                    // Evitamos duplicar labels usando el tiempo como llave temporal
                    if (!isset($data[$serie]['labels']) || !in_array($time, $data[$serie]['labels'])) {
                        $data[$serie]['labels'][] = $time;
                    }
                    
                    $data[$serie]['datasets'][$field][] = $value;
                    $data[$serie]['ultimo_estado'][$field] = $value;
                }
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}