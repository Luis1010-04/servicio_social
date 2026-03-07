<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaestrosUsuarios extends Controller
{
    public function administrar($id)
    {
        $titulo = 'Equipos Maestros Asignados';

        // 1. Obtenemos los maestros asignados al usuario con su ubicación "heredada" del catálogo
        $datos = DB::table('maestros_usuarios as mu')
            ->join('maestros_catalogo as mc', 'mu.maestro_id', '=', 'mc.id')
            ->leftJoin('ubicaciones as u', 'mu.ubicacion_id', '=', 'u.id') // Usamos el ID de la tabla ubicaciones
            ->select(
                'mu.*',           
                'mc.modelo',      
                'mc.activo',
                'u.nombre as nombre_ubicacion' 
            )
            ->where('mu.user_id', $id)
            ->orderBy('mu.created_at', 'desc')
            ->get();

        $usuario = DB::table('users')->where('id', $id)->first();
        
        // Solo maestros que no han sido asignados o que están activos en catálogo
        $disponibles = DB::table('maestros_catalogo')->where('activo', 1)->get();
        
        // Cargamos el catálogo de ubicaciones para los selects de la vista
        $ubicaciones = DB::table('ubicaciones')->get();

        return view('modules.Maestros_Usuario.administrar', compact('titulo', 'datos', 'usuario', 'disponibles', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'maestro_id'   => 'required|exists:maestros_catalogo,id',
            'user_id'      => 'required', // O usar Auth::id() por seguridad
            'numero_serie' => 'required|string|unique:maestros_usuarios,numero_serie',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'nombre'       => 'required|string|max:255',
            'topico'       => 'required|string|max:255',
            'Broker'       => 'required|string|max:255',
        ], [
            'numero_serie.unique' => 'Este número de serie ya está vinculado a otro usuario.'
        ]);

        try {
            DB::table('maestros_usuarios')->insert([
                'maestro_id'   => $request->maestro_id, 
                // Sugerencia: use Auth::id() si el usuario se auto-asigna
                'user_id'      => $request->user_id,
                'numero_serie' => strtoupper($request->numero_serie), // Normalizamos a mayúsculas
                'nombre'       => $request->nombre,     
                'imagen_ruta'  => $request->imagen_ruta ?? 'default.png',
                'ubicacion_id' => $request->ubicacion_id, 
                'topico'       => $request->topico,     
                'Broker'       => $request->Broker,     
                'descripcion'  => $request->descripcion,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return back()->with('success', 'Maestro asignado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        // Al actualizar, el número de serie suele ser de SOLO LECTURA.
        // Solo validamos lo que realmente puede cambiar el usuario.
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'topico'       => 'required|string|max:255',
            'Broker'       => 'required|string|max:255',
        ]);

        try {
            DB::table('maestros_usuarios')->where('id', $id)->update([
                'nombre'       => $request->nombre,
                'ubicacion_id' => $request->ubicacion_id,
                'topico'       => $request->topico,
                'Broker'       => $request->Broker,
                'descripcion'  => $request->descripcion,
                'updated_at'   => now(),
            ]);

            return back()->with('success', 'Configuración actualizada con éxito.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            // Antes de borrar, podrías verificar si hay esclavos críticos dependiendo de este vínculo
            DB::table('maestros_usuarios')->where('id', $id)->delete();
            return back()->with('success', 'Asignación eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la asignación.');
        }
    }
}