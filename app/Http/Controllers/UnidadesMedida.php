<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UnidadesMedida extends Controller
{
    public function index()
    {
        $titulo = "Unidades de Medida";
        $datos = DB::table('unidades_de_medida')->get();
        // Nota: Asegúrate que el nombre de la carpeta sea exacto (unidadMedida vs UnidadMedida)
        return view('modules.unidadMedida.index', compact('titulo', 'datos'));
    }

    public function create()
    {
        $titulo = "Crear Unidad de Medida";
        return view('modules.unidadMedida.create', compact('titulo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:unidades_de_medida,nombre',
        ], [
            'nombre.unique' => 'Esta unidad ya está registrada.',
        ]);

        try {
            DB::table('unidades_de_medida')->insert([
                'nombre' => $request->nombre,
                // Quitamos los timestamps si te dan error, 
                // o asegúrate de que existan en la tabla.
            ]);

            return redirect()->route('unidades.medida.index')->with('success', 'Unidad creada correctamente.');
        } catch (\Exception $e) {
            // En desarrollo, es mejor ver el error real: return back()->with('error', $e->getMessage());
            return back()->with('error', 'Error técnico: ' . $e->getMessage())->withInput();
        }
    }
    
    public function edit(string $id)
    {
        $titulo = "Editar Unidad de Medida";    
        $item = DB::table('unidades_de_medida')->where('id', $id)->first();
        
        if (!$item) {
            return redirect()->route('unidades.medida.index')->with('error', 'Registro no encontrado.');
        }

        return view('modules.unidadMedida.edit', compact('titulo', 'item'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            // Esto permite actualizar el nombre pero evita que pongas uno que ya tenga otra unidad
            'nombre' => 'required|string|max:50|unique:unidades_de_medida,nombre,' . $id,
        ]);

        try {
            DB::table('unidades_de_medida')->where('id', $id)->update([
                'nombre'     => $request->nombre,
                'updated_at' => now(),
            ]);
            return redirect()->route('unidades.medida.index')->with('success', 'Actualizado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Fallo al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            // Protección de Integridad Referencial
            $enUso = DB::table('componentes')->where('unidad_id', $id)->exists();
            
            if ($enUso) {
                return back()->with('error', 'No se puede eliminar: existen componentes configurados con esta unidad.');
            }

            DB::table('unidades_de_medida')->where('id', $id)->delete();
            return redirect()->route('unidades.medida.index')->with('success', 'Unidad eliminada con éxito');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}