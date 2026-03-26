<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportesController extends Controller
{
public function index()
{
    $titulo = "Panel de Reportes e Historial";
    $user = Auth::user();

    $maestros = DB::table('maestros_usuarios')
        ->join('maestros_catalogo', 'maestros_usuarios.maestro_id', '=', 'maestros_catalogo.id')
        ->where('maestros_usuarios.user_id', $user->id)
        ->where('maestros_catalogo.activo', 1)
        ->select(
            'maestros_usuarios.id', 
            'maestros_catalogo.modelo', 
            'maestros_usuarios.nombre as nombre_asignado'
        )
        ->get();

    return view('modules.vistasUsuario.reportes.index', compact('titulo', 'maestros'));
}

// public function getEsclavosByMaestro($id)
// {
//     try {
//         $userId = Auth::id();

//         // 1. Verificamos que el usuario sea dueño de esa asignación de maestro
//         // Aquí $id es el ID de 'maestros_usuarios' que viene del select
//         $acceso = DB::table('maestros_usuarios')
//             ->where('id', $id) 
//             ->where('user_id', $userId)
//             ->exists();

//         if (!$acceso) {
//             return response()->json(['error' => 'No autorizado'], 403);
//         }

//         // 2. Traemos los esclavos basándonos en la relación de tu Inventario
//         // $esclavos = DB::table('maestros_esclavos as me')
//         //     ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
//         //     ->where('me.maestro_id', $id) // Buscamos por el ID de la tabla maestros_usuarios
//         //     ->select('ec.id', 'me.nombre', 'ec.modelo') // Traemos el nombre personalizado de la relación
//         //     ->get();
//         $esclavos = DB::table('maestros_esclavos as me')
//             ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
//             ->where('me.maestro_id', $id) // Buscamos por el ID de la tabla maestros_usuarios
//             ->select('ec.id', 'me.nombre', 'ec.modelo') // Traemos el nombre personalizado de la relación
//             ->get();


//         return response()->json($esclavos);

//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }
public function getEsclavosByMaestro($id)
{
    try {
        $userId = Auth::id();

        $acceso = DB::table('maestros_usuarios')
            ->where('id', $id) 
            ->where('user_id', $userId)
            ->exists();

        if (!$acceso) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $esclavos = DB::table('maestros_esclavos as me')
            ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
            ->where('me.maestro_id', $id)
            ->select(
                'me.id',           
                'me.nombre', 
                'ec.modelo',
                'me.numero_serie'
            )
            ->get();

        return response()->json($esclavos);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


// public function getComponentesByEsclavo($id)
// {
//     try {
//         // Buscamos los componentes vinculados al ID del esclavo_catalogo
//         $componentes = DB::table('componentes as c')
//             ->join('detalle_esclavo_componentes as dec', 'c.id', '=', 'dec.componente_id')
//             ->where('dec.esclavo_id', $id)
//             ->select('c.id', 'c.nombre', 'c.tipo')
//             ->get();

//         return response()->json($componentes);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }

public function getComponentesByEsclavo($idRelacion)
{

    $asignacion = DB::table('maestros_esclavos')->where('id', $idRelacion)->first();
    
    if (!$asignacion) return response()->json([]);

    $componentes = DB::table('detalle_esclavo_componentes')
        ->join('componentes', 'detalle_esclavo_componentes.componente_id', '=', 'componentes.id')
        ->where('detalle_esclavo_componentes.esclavo_id', $asignacion->esclavo_id)
        ->select('componentes.id', 'componentes.nombre', 'componentes.tipo')
        ->get();

    return response()->json($componentes);
}

public function generarReporte(Request $request)
{
    try {
        $client = new \InfluxDB2\Client([
            "url" => env('INFLUXDB_URL'),
            "token" => env('INFLUXDB_TOKEN'),
            "bucket" => env('INFLUXDB_BUCKET'),
            "org" => env('INFLUXDB_ORG'),
        ]);

        $queryApi = $client->createQueryApi();

        $start = \Carbon\Carbon::parse($request->fecha_inicio)->startOfDay()->format('Y-m-d\TH:i:s\Z');
        $end = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay()->format('Y-m-d\TH:i:s\Z');

        $asignacion = DB::table('maestros_esclavos')
            ->where('id', $request->esclavo_id) 
            ->first();
        

        if (!$asignacion) {
            throw new \Exception("No se encontró la asignación del dispositivo.");
        }

        $tagDispositivo = $asignacion->numero_serie; 

        $nombres = DB::table('componentes')
            ->whereIn('id', $request->componentes)
            ->pluck('nombre')
            ->toArray();
            
        if (empty($nombres)) {
            throw new \Exception("No se seleccionaron sensores válidos.");
        }

        $filtrosField = collect($nombres)->map(fn($n) => 'r["_field"] == "' . trim($n) . '"')->implode(' or ');

        $fluxQuery = "from(bucket: \"biobit\")
        |> range(start: {$start}, stop: {$end})
        |> filter(fn: (r) => r[\"dispositivo\"] == \"{$tagDispositivo}\")
        |> filter(fn: (r) => {$filtrosField})
        |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
        |> yield(name: \"mean\")";

        Log::info("Consulta enviada:\n" . $fluxQuery);

        $records = $queryApi->query($fluxQuery);
        
        $data = [];
        foreach ($records as $table) {
            foreach ($table->records as $record) {
                $data[] = [
                    '_time' => $record->getTime(),
                    'componente' => $record->getField(),
                    'valor' => $record->getValue(),

                    $record->getField() => $record->getValue() 
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'debug_query' => $fluxQuery
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
}