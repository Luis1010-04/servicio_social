<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaestrosCatalogo extends Controller
{
    public function index()
    {
        $titulo = "Catálogo de Equipos Maestros";
        
        // Subconsulta para contar esclavos vinculados
        $datos = DB::table('maestros_catalogo')
            ->select(
                'maestros_catalogo.*',
                DB::raw('(SELECT COUNT(*) FROM maestros_esclavos WHERE maestros_esclavos.maestro_id = maestros_catalogo.id) as total_esclavos')
            )
            ->get();

        return view('modules.maestros.index', compact('titulo', 'datos'));
    }

    public function create()
    {
        $titulo = "Crear Maestro";
        return view('modules.maestros.create', compact('titulo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:255',
            'modelo' => 'required|max:255',
            'descripcion' => 'nullable|max:255'
        ]);

        try {
        DB::table('maestros_catalogo')->insert([
            'nombre'      => $request->nombre,
            'modelo'      => $request->modelo,
            'descripcion' => $request->descripcion,
            'activo'      => $request->has('activo') ? 1 : 0, // Si está marcado 1, si no 0
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

            return to_route('maestros_catalogo.index')->with('success', 'Maestro creado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $titulo = "Editar Maestro";
        $maestro = DB::table('maestros_catalogo')->where('id', $id)->first();
        return view('modules.maestros.edit', compact('titulo', 'maestro'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:255',
            'modelo' => 'required|max:255',
            'descripcion' => 'required|max:255',
            'activo' => 'required' // Validamos que el estado venga en el request
        ]);

        try {
            DB::table('maestros_catalogo')->where('id', $id)->update([
                'nombre'      => $request->nombre,
                'modelo'      => $request->modelo,
                'descripcion' => $request->descripcion,
                'activo'      => $request->activo, // <--- ESTA ES LA CLAVE
                'updated_at'  => now(),
            ]);

            return to_route('maestros_catalogo.index')->with('success', 'Maestro actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {

            DB::table('maestros_catalogo')->where('id', $id)->delete();
            return back()->with('success', 'Maestro eliminado del sistema.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error técnico al intentar eliminar.');
        }
    }

    public function administrar_esclavos($id)
    {
        $titulo = "Administrar Esclavos del Maestro";
        $maestro = DB::table('maestros_catalogo')->where('id', $id)->first();

        // JOIN TRIPLE: maestros_esclavos -> esclavos_catalogo -> ubicaciones
        // Esto permite ver el nombre de la ubicación en la tabla de administración
        $esclavos = DB::table('maestros_esclavos as me')
            ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
            ->join('ubicaciones as u', 'ec.ubicacion_id', '=', 'u.id')
            ->select(
                'ec.*', 
                'u.nombre as nombre_ubicacion', 
                'me.nombre as alias_en_maestro' // El nombre que le dio el maestro al esclavo
            )
            ->where('me.maestro_id', $id)
            ->get();

        return view('modules.maestros.administrar', compact('titulo', 'maestro', 'esclavos'));
    }
}