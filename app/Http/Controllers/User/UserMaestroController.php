<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserMaestroController extends Controller
{
    /**
     * Muestra la lista de equipos maestros del usuario logueado.
     */
    public function index()
    {
        $userId = Auth::id();
        $titulo = "Mis Equipos Maestros";

        $maestros = DB::table('maestros_usuarios as mu')
            ->join('maestros_catalogo as mc', 'mu.maestro_id', '=', 'mc.id')
            // Eliminamos el join con ubicaciones porque no hay ID de relación en mu
            ->select('mu.*', 'mc.nombre as modelo_nombre', 'mc.modelo', 'mu.localizacion as nombre_ubicacion')
            ->where('mu.user_id', $userId)
            ->get();

        return view('modules.vistasUsuario.maestros.index', compact('titulo', 'maestros'));
    }

    /**
     * Muestra el formulario para vincular un nuevo equipo físico.
     */
    public function create()
    {
        $titulo = "Vincular Nuevo Maestro";
        
        // Catálogo general de modelos disponibles
        $modelosDisponibles = DB::table('maestros_catalogo')->where('id', '>', 0)->get();
        
        // Solo las ubicaciones que este usuario ha creado
        $ubicaciones = DB::table('ubicaciones')->where('user_id', Auth::id())->get();

        return view('modules.vistasUsuario.maestros.create', compact('titulo', 'modelosDisponibles', 'ubicaciones'));
    }

    /**
     * Guarda la vinculación del hardware con el usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
        'maestro_id'   => 'required|exists:maestros_catalogo,id',
        'numero_serie' => 'required|string|max:100|unique:maestros_usuarios,numero_serie',
        'ubicacion_id' => 'required|exists:ubicaciones,id',
        'nombre'       => 'required|string|max:100',
        'topico'       => 'required|string',
        'Broker'       => 'required|ip|unique:maestros_usuarios,Broker', // <--- Crucial para evitar el error 500
    ], [
        'Broker.unique' => 'Esta dirección IP de Broker ya está asignada a otro equipo.',
        'numero_serie.unique' => 'Este número de serie ya existe en el sistema.',
        'Broker.ip' => 'El formato de la dirección IP no es válido.'
    ]);

        $nombreUbi = DB::table('ubicaciones')->where('id', $request->ubicacion_id)->value('nombre');

        DB::table('maestros_usuarios')->insert([
            'user_id'      => Auth::id(),
            'maestro_id'   => $request->maestro_id,
            'ubicacion_id' => $request->ubicacion_id, // Incluimos el ID
            'numero_serie' => strtoupper($request->numero_serie),
            'nombre'       => $request->nombre,
            'localizacion' => $nombreUbi, 
            'topico'       => $request->topico,
            'imagen_ruta'  => 'default.png', 
            'Broker'       => $request->Broker,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('user.maestros.index')->with('success', 'Equipo vinculado correctamente.');
    }

    /**
     * Muestra el formulario de edición (solo nombre, ubicación y red).
     */
    public function edit($id)
    {
        // Validamos propiedad antes de mostrar la vista
        $maestro = DB::table('maestros_usuarios')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $titulo = "Editar Equipo: " . $maestro->nombre;
        $ubicaciones = DB::table('ubicaciones')->where('user_id', Auth::id())->get();

        return view('modules.vistasUsuario.maestros.edit', compact('titulo', 'maestro', 'ubicaciones'));
    }

    /**
     * Actualiza la configuración del maestro.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'nombre'       => 'required|string|max:100',
            'topico'       => 'required|string',
        ]);

        // Update con cláusula where de seguridad
        DB::table('maestros_usuarios')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'ubicacion_id' => $request->ubicacion_id,
                'nombre'       => $request->nombre,
                'topico'       => $request->topico,
                'Broker'       => $request->Broker,
                'updated_at'   => now(),
            ]);

        return redirect()->route('user.maestros.index')->with('success', 'Equipo actualizado.');
    }

    /**
     * Panel central para administrar los esclavos de un maestro específico.
     */
    public function administrar($id)
    {
        // 1. Verificamos que el maestro sea del usuario
        $maestro = DB::table('maestros_usuarios as mu')
            ->join('maestros_catalogo as mc', 'mu.maestro_id', '=', 'mc.id')
            ->select('mu.*', 'mc.nombre as modelo_nombre')
            ->where('mu.id', $id)
            ->where('mu.user_id', Auth::id()) // Seguridad total
            ->firstOrFail();

        // 2. Obtenemos solo los esclavos vinculados a ESTE maestro físico
        $esclavos = DB::table('maestros_esclavos as me')
            ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
            ->leftJoin('ubicaciones as u', 'me.ubicacion_id', '=', 'u.id')
            ->select('me.*', 'ec.modelo as modelo_tecnico', 'u.nombre as nombre_ubicacion')
            ->where('me.maestro_id', $id)
            ->get();

        $catalogoEsclavos = DB::table('esclavos_catalogo')->get();
        $ubicaciones = DB::table('ubicaciones')->where('user_id', Auth::id())->get();
        $titulo = "Administrar: " . $maestro->nombre;

        return view('modules.vistasUsuario.maestros.administrar', 
            compact('titulo', 'maestro', 'esclavos', 'catalogoEsclavos', 'ubicaciones'));
    }

    /**
     * Elimina la vinculación del maestro (y por cascada sus esclavos).
     */
    public function destroy($id)
    {
        $borrado = DB::table('maestros_usuarios')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        if ($borrado) {
            return redirect()->route('user.maestros.index')->with('success', 'Equipo desvinculado.');
        }

        return back()->with('error', 'No tienes permisos para eliminar este equipo.');
    }
}