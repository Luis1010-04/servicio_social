<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Ubicaciones extends Controller
{
    public function index()
    {
        $titulo = 'Ubicaciones';
        $datos = DB::table('ubicaciones')->get();
        return view('modules.ubicaciones.index', compact('titulo', 'datos'));
    }

    public function create()
    {
        $titulo = 'Crear Ubicación';
        return view('modules.ubicaciones.create', compact('titulo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:ubicaciones,nombre',
        ], [
            'nombre.unique' => 'Ya existe una ubicación con ese nombre.',
        ]);

        try {
            DB::table('ubicaciones')->insert([
                'nombre'     => $request->nombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return redirect()->route('ubicaciones.index')->with('success', 'Ubicación guardada con éxito.');
        } catch (\Exception $e) {
            // Usamos back() con input para no borrar lo que el usuario escribió
            return back()->with('error', 'Error al guardar la ubicación.')->withInput();
        }
    }

    public function edit(string $id)
    {
        $titulo = 'Editar Ubicación';
        $item = DB::table('ubicaciones')->where('id', $id)->first();
        
        if (!$item) {
            return redirect()->route('ubicaciones.index')->with('error', 'La ubicación no existe.');
        }
        
        return view('modules.ubicaciones.edit', compact('titulo', 'item'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:ubicaciones,nombre,' . $id,
        ]);

        try {
            DB::table('ubicaciones')->where('id', $id)->update([
                'nombre'     => $request->nombre,
                'updated_at' => now(),
            ]);
            return redirect()->route('ubicaciones.index')->with('success', 'Ubicación actualizada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            // Comprobamos si la ubicación está en uso por esclavos o maestros
            $enUsoEsclavos = DB::table('esclavos_catalogo')->where('ubicacion_id', $id)->exists();
            $enUsoMaestros = DB::table('maestros_catalogo')->where('ubicacion_id', $id)->exists();

            if ($enUsoEsclavos || $enUsoMaestros) {
                return back()->with('error', 'No se puede eliminar: hay equipos (Maestros o Esclavos) asociados a esta ubicación.');
            }

            DB::table('ubicaciones')->where('id', $id)->delete();
            return redirect()->route('ubicaciones.index')->with('success', 'Ubicación eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error técnico al intentar eliminar.');
        }
    }
    public function show($id)
{
    // 1. Obtener la ubicación
    $ubicacion = DB::table('ubicaciones')->where('id', $id)->first();
    if (!$ubicacion) return abort(404);

    // 2. Obtener los esclavos que están en esta ubicación
    $esclavos = DB::table('esclavos_catalogo as e')
        ->select('e.*', 
            DB::raw('(SELECT COUNT(*) FROM detalle_esclavo_componentes WHERE esclavo_id = e.id) as total_comp')
        )
        ->where('e.ubicacion_id', $id)
        ->get();

    $titulo = "Detalle de Ubicación: " . $ubicacion->nombre;

    return view('modules.ubicaciones.show', compact('ubicacion', 'esclavos', 'titulo'));
}
}