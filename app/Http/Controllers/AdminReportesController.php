<?php
namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class adminreportescontroller extends controller
{
    public function index()
    {
        $titulo = "Panel de Control Administrativo (God View)";
        return view('modules.reportes.index', compact('titulo'));
    }

    // 1. Datos de Usuarios
    public function getUsuarios()
    {
        $usuarios = DB::table('users')
            ->select('id', 'name', 'email', 'created_at', 'activo')
            ->get();
            
        return response()->json(['data' => $usuarios]);
    }

    // 2. Datos del Catálogo de Maestros + Equipos Operando (MySQL)
    // 2. Datos del Catálogo de Maestros + Equipos Operando
    public function getMaestrosCatalogo()
    {
        try {
            $maestros = DB::table('maestros_catalogo as mc')
                // AQUI ESTÁ EL CAMBIO: Quitamos la validación de mu.activo porque la tabla no la tiene
                ->leftJoin('maestros_usuarios as mu', 'mc.id', '=', 'mu.maestro_id') 
                ->select(
                    'mc.id', 'mc.nombre', 'mc.modelo', 'mc.descripcion', 'mc.activo', 
                    'mc.created_at',
                    DB::raw('COUNT(mu.id) as operando') // Contamos cuántas veces se ha asignado este modelo
                )
                ->groupBy('mc.id', 'mc.nombre', 'mc.modelo', 'mc.descripcion', 'mc.activo', 'mc.created_at')
                ->get();

            return response()->json(['data' => $maestros]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 3. Datos del Catálogo de Esclavos + Equipos Operando
    // 3. Datos del Catálogo de Esclavos + Equipos Operando
    public function getEsclavosCatalogo()
    {
        try {
            $esclavos = DB::table('esclavos_catalogo as ec')
                ->leftJoin('maestros_esclavos as me', 'ec.id', '=', 'me.esclavo_id')
                ->select(
                    'ec.id', 'ec.nombre', 'ec.modelo', 'ec.activo', // <-- Quitamos ec.descripcion
                    'ec.created_at',
                    DB::raw('COUNT(me.id) as operando')
                )
                ->groupBy('ec.id', 'ec.nombre', 'ec.modelo', 'ec.activo', 'ec.created_at') // <-- Quitamos ec.descripcion
                ->get();

            return response()->json(['data' => $esclavos]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 4. Tabla Maestra (Solo relaciones MySQL para carga ultra-rápida)
    public function getTablaMaestra()
    {
        try {
            $relaciones = DB::table('maestros_esclavos as me')
                ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
                ->join('users as u', 'mu.user_id', '=', 'u.id')
                ->join('maestros_catalogo as mc', 'mu.maestro_id', '=', 'mc.id')
                ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
                ->select(
                    'u.name as usuario',
                    'mu.nombre as nombre_maestro',
                    'mc.modelo as modelo_maestro',
                    'me.nombre as nombre_esclavo',
                    // 'ec.modelo as modelo_esclavo', <- Opcional, lo dejé comentado por si no lo usas en JS
                    'me.numero_serie'
                    // AQUI ESTABA EL ERROR: Eliminamos 'me.activo as status_mysql'
                )
                ->get();

            return response()->json(['data' => $relaciones]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 5. Escáner InfluxDB (Se llama asíncronamente por cada fila)
    public function checkInfluxStatus(Request $request)
    {
        $serie = $request->serie;
        $resultado = [
            'online' => false,
            'ultima_actividad' => 'Sin datos'
        ];

        if (!$serie) {
            return response()->json($resultado);
        }

        try {
            $client = new \InfluxDB2\Client([
                "url" => env('INFLUXDB_URL'),
                "token" => env('INFLUXDB_TOKEN'),
                "org" => env('INFLUXDB_ORG'),
            ]);
            $queryApi = $client->createQueryApi();

            // Adaptado a tu estructura Influx
            $fluxQuery = 'from(bucket: "biobit")
                |> range(start: -30d)
                |> filter(fn: (r) => r["dispositivo"] == "' . $serie . '")
                |> last()';

            $result = $queryApi->query($fluxQuery);

            if (!empty($result) && isset($result[0]->records) && count($result[0]->records) > 0) {
                $record = $result[0]->records[0];
                $timeString = $record->getTime(); 
                
                $fechaUltimoDato = Carbon::parse($timeString)->setTimezone('America/Mexico_City');
                $resultado['ultima_actividad'] = $fechaUltimoDato->format('d/m/Y H:i:s');

                // Menos de 5 minutos (300 segundos) = Online
                if ($fechaUltimoDato->diffInSeconds(now('America/Mexico_City')) < 300) {
                    $resultado['online'] = true;
                }
            }
        } catch (\Exception $e) {
            // Guardamos en el log de Laravel por si acaso
            Log::error("Error InfluxDB para $serie: " . $e->getMessage());
            $resultado['ultima_actividad'] = 'Error de conexión';
        }

        return response()->json($resultado);
    }
}
