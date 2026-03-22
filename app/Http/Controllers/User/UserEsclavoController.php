<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use InfluxDB2\Client;

class UserEsclavoController extends Controller
{
    public function index()
{
    $userId = Auth::id();
    $titulo = "Inventario Global de Dispositivos";

    // Traemos todos los esclavos, uniendo con su maestro y catálogo
    $esclavos = DB::table('maestros_esclavos as me')
        ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
        ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
        ->leftJoin('ubicaciones as u', 'me.ubicacion_id', '=', 'u.id')
        ->select(
            'me.*', 
            'ec.nombre as tipo_dispositivo', 
            'mu.nombre as nombre_maestro',
            'u.nombre as nombre_ubicacion'
        )
        ->where('mu.user_id', $userId) // El candado de seguridad hereda del maestro
        ->get();

    return view('modules.vistasUsuario.esclavos.index', compact('titulo', 'esclavos'));
}
    /**
     * Store: Registra un nuevo esclavo vinculado a un maestro físico.
     */
    public function store(Request $request)
    {
        $request->validate([
            'maestro_id'   => 'required|exists:maestros_usuarios,id',
            'esclavo_id'   => 'required|exists:esclavos_catalogo,id',
            'numero_serie' => 'required|string|max:100|unique:maestros_esclavos,numero_serie',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'nombre'       => 'required|string|max:100',
        ]);

        // SEGURIDAD: Validar que el maestro_id pertenece al usuario logueado
        $maestroId = $request->maestro_id;
        $esSuMaestro = DB::table('maestros_usuarios')
            ->where('id', $maestroId)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$esSuMaestro) {
            return back()->with('error', 'No tienes permiso para agregar esclavos a este equipo.');
        }

        DB::table('maestros_esclavos')->insert([
            'maestro_id'   => $maestroId,
            'esclavo_id'   => $request->esclavo_id,
            'ubicacion_id' => $request->ubicacion_id,
            'numero_serie' => strtoupper($request->numero_serie),
            'nombre'       => $request->nombre,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('user.maestros.administrar', $request->maestro_id)
                     ->with('success', 'Esclavo actualizado.');
    }

    /**
     * Monitor: Vista en tiempo real del esclavo (Sensores/Actuadores).
     */
    public function monitor($id)
    {
        // 1. Validamos propiedad y obtenemos datos del equipo
        $esclavo = DB::table('maestros_esclavos as me')
            ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
            ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
            ->select('me.*', 'ec.modelo', 'mu.topico as topico_maestro')
            ->where('me.id', $id)
            ->where('mu.user_id', Auth::id())
            ->firstOrFail();

        // 2. Buscamos la última lectura y su unidad de medida
        // Usamos el esclavo_id del catálogo para buscar sus componentes vinculados
        $ultimaLectura = DB::table('detalle_esclavo_componentes as dec')
            ->join('componentes as c', 'dec.componente_id', '=', 'c.id')
            ->join('unidades_de_medida as um', 'c.unidad_id', '=', 'um.id')
            ->leftJoin('lecturas as l', 'c.id', '=', 'l.componente_id')
            ->select('l.valor', 'um.nombre as unidad')
            ->where('dec.esclavo_id', $esclavo->esclavo_id) 
            ->orderBy('l.created_at', 'desc')
            ->first();

        $titulo = "Monitoreo: " . $esclavo->nombre;

        return view('modules.vistasUsuario.esclavos.monitor', compact('titulo', 'esclavo', 'ultimaLectura'));
    }

    /**
     * Edit: Formulario para cambiar nombre o ubicación del esclavo.
     */
    public function edit($id)
    {
        $esclavo = DB::table('maestros_esclavos as me')
            ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
            ->select('me.*')
            ->where('me.id', $id)
            ->where('mu.user_id', Auth::id())
            ->firstOrFail();

        $ubicaciones = DB::table('ubicaciones')->where('user_id', Auth::id())->get();
        $titulo = "Editar Esclavo: " . $esclavo->nombre;

        return view('modules.vistasUsuario.esclavos.edit', compact('titulo', 'esclavo', 'ubicaciones'));
    }

    /**
     * Update: Actualiza los datos.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'       => 'required|string|max:100',
            'ubicacion_id' => 'required|exists:ubicaciones,id'
        ]);

        // Verificamos propiedad antes de actualizar
        $esclavoValido = DB::table('maestros_esclavos as me')
            ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
            ->where('me.id', $id)
            ->where('mu.user_id', Auth::id())
            ->exists();

        if (!$esclavoValido) return abort(403);

        DB::table('maestros_esclavos')->where('id', $id)->update([
            'nombre'       => $request->nombre,
            'ubicacion_id' => $request->ubicacion_id,
            'updated_at'   => now()
        ]);

        return redirect()->route('user.maestros.administrar', $request->maestro_id)
                         ->with('success', 'Esclavo actualizado.');
    }

    /**
     * Destroy: Elimina el esclavo.
     */
    public function destroy($id)
    {
        $esclavo = DB::table('maestros_esclavos as me')
            ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
            ->where('me.id', $id)
            ->where('mu.user_id', Auth::id())
            ->select('me.id')
            ->first();

        if ($esclavo) {
            DB::table('maestros_esclavos')->where('id', $id)->delete();
            return back()->with('success', 'Dispositivo eliminado.');
        }

        return back()->with('error', 'No autorizado.');
    }
    public function create(Request $request)
    {
        $titulo = "Vincular Nuevo Esclavo";
        $userId = Auth::id();

        // Si venimos desde un Maestro específico, lo pre-seleccionamos
        $maestro_id = $request->query('maestro_id');

        // Solo los maestros que le pertenecen al usuario
        $maestros = DB::table('maestros_usuarios')
            ->where('user_id', $userId)
            ->get();

        // Catálogo de tipos de sensores/actuadores
        $catalogoEsclavos = DB::table('esclavos_catalogo')->get();
        
        // Ubicaciones del usuario
        $ubicaciones = DB::table('ubicaciones')->where('user_id', $userId)->get();

        return view('modules.vistasUsuario.esclavos.create', 
            compact('titulo', 'maestros', 'catalogoEsclavos', 'ubicaciones', 'maestro_id'));
    }
public function getConfiguracion($serie)
    {
        $dispositivo = DB::table('maestros_esclavos as me')
            ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
            ->where('me.numero_serie', $serie)
            ->select('mu.user_id', 'me.numero_serie')
            ->first();

        if (!$dispositivo) {
            return response()->json(['error' => 'Dispositivo no vinculado'], 404);
        }

        return response()->json([
            'mqtt_host'  => env('MQTT_HOST', '192.168.100.18'),
            'mqtt_port'  => (int)env('MQTT_PORT', 1883),
            'user_id'    => $dispositivo->user_id,
            'base_topic' => "v1/usuarios/{$dispositivo->user_id}/nodos/{$dispositivo->numero_serie}/",
            'client_id'  => "ESP32_{$dispositivo->numero_serie}",
            'status'     => 'authorized'
        ]);
    }

    /**
     * Obtener datos de InfluxDB filtrados por el número de serie dinámico
     */
    public function getUltimaLectura($id)
    {
        $esclavo = DB::table('maestros_esclavos')->where('id', $id)->first();
        if (!$esclavo) return response()->json(['error' => 'No encontrado'], 404);

        $componentesMySQL = DB::table('detalle_esclavo_componentes as dec')
            ->join('componentes as c', 'dec.componente_id', '=', 'c.id')
            ->join('unidades_de_medida as um', 'c.unidad_id', '=', 'um.id')
            ->select('c.nombre', 'um.nombre as unidad')
            ->where('dec.esclavo_id', $esclavo->esclavo_id)
            ->get();

        $client = new Client([
            "url" => env('INFLUXDB_URL'),
            "token" => env('INFLUXDB_TOKEN'),
            "bucket" => env('INFLUXDB_BUCKET'),
            "org" => env('INFLUXDB_ORG')
        ]);

        $queryApi = $client->createQueryApi();
        $fluxQuery = 'from(bucket: "' . env('INFLUXDB_BUCKET') . '")
            |> range(start: -1h)
            |> filter(fn: (r) => r["dispositivo"] == "' . $esclavo->numero_serie . '")
            |> last()';

        $tables = $queryApi->query($fluxQuery);

        $resultado = $componentesMySQL->map(function ($comp) use ($tables) {
            $valorInflux = null;
            $fecha = now();
            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    if ($record->getField() == $comp->nombre) {
                        $valorInflux = $record->getValue();
                        $fecha = $record->getTime();
                    }
                }
            }
            return [
                'nombre' => $comp->nombre,
                'valor' => $valorInflux ?? '--',
                'unidad' => $comp->unidad,
                'created_at' => $fecha
            ];
        });

        return response()->json($resultado);
    }
}