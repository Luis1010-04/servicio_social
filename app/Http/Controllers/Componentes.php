<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Componentes extends Controller
{
    public function index()
    {
        $titulo = "Administración de Componentes";
        
        $datos = DB::table('componentes as c')
            ->join('unidades_de_medida as um', 'c.unidad_id', '=', 'um.id')
            ->select(
                'c.*', 
                'um.nombre as unidad_medida',
                // Subconsulta para contar sin romper el GROUP BY
                DB::raw('(SELECT COUNT(*) FROM detalle_esclavo_componentes WHERE componente_id = c.id) as total_esclavos')
            )
            ->get();

        return view('modules.componentes.index', compact('titulo', 'datos'));
    }
    
    public function create()
    {
        $titulo = "Crear Componente";
        $unidades = DB::table('unidades_de_medida')->get();
        $esclavos = DB::table('esclavos_catalogo')->where('activo', 1)->get();
        
        return view('modules.componentes.create', compact('titulo', 'unidades', 'esclavos'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'tipo'   => 'required',
        ];

        if ($request->tipo === 'Sensor') {
            $rules['unidad_id'] = 'required|exists:unidades_de_medida,id';
        } else {
            $rules['unidad_id'] = 'nullable';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Si es actuador, forzamos que sea null independientemente de lo que llegue
            $unidadId = ($request->tipo === 'Actuador') ? 1 : $request->unidad_id;
            $componenteId = DB::table('componentes')->insertGetId([
                'unidad_id'   => $unidadId,
                'nombre'      => $request->nombre,
                'tipo'        => $request->tipo,
                'descripcion' => $request->descripcion,
                'ruta_incono' => $request->ruta_incono ?? 'bi bi-cpu', 
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if ($request->has('esclavos') && is_array($request->esclavos)) {
                $datosDetalle = [];
                foreach ($request->esclavos as $esclavoId) {
                    $datosDetalle[] = [
                        'esclavo_id'    => $esclavoId,
                        'componente_id' => $componenteId,
                    ];
                }
                DB::table('detalle_esclavo_componentes')->insert($datosDetalle);
            }

            DB::commit();
            return redirect()->route('componentes.index')->with('success', 'Componente creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $titulo = "Editar Componente";
        $componente = DB::table('componentes')->where('id', $id)->first();
        $unidades = DB::table('unidades_de_medida')->get();
        $esclavos = DB::table('esclavos_catalogo')->where('activo', 1)->get();
        
        // IDs de los esclavos ya vinculados para las "checkboxes"
        $esclavosVinculados = DB::table('detalle_esclavo_componentes')
            ->where('componente_id', $id)
            ->pluck('esclavo_id')
            ->toArray();

        return view('modules.componentes.edit', compact('titulo', 'componente', 'unidades', 'esclavos', 'esclavosVinculados'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unidad_id'   => 'required',
            'nombre'      => 'required|max:255',
        ]);

        try {
            DB::beginTransaction();

            DB::table('componentes')->where('id', $id)->update([
                'unidad_id'   => $request->unidad_id,
                'nombre'      => $request->nombre,
                'tipo'        => $request->tipo,
                'descripcion' => $request->descripcion,
                'updated_at'  => now(),
            ]);

            // Sincronización manual: Limpiar y re-insertar
            DB::table('detalle_esclavo_componentes')->where('componente_id', $id)->delete();
            
            if ($request->has('esclavos')) {
                foreach ($request->esclavos as $esclavoId) {
                    DB::table('detalle_esclavo_componentes')->insert([
                        'esclavo_id'    => $esclavoId,
                        'componente_id' => $id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('componentes.index')->with('success', 'Componente actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Verificar si tiene lecturas registradas antes de borrar (Auditoría)
            $tieneLecturas = DB::table('lecturas')->where('componente_id', $id)->exists();
            
            if ($tieneLecturas) {
                return back()->with('error', 'No se puede eliminar: el componente tiene historial de lecturas.');
            }

            DB::beginTransaction();
            // Borrar vínculos primero
            DB::table('detalle_esclavo_componentes')->where('componente_id', $id)->delete();
            // Borrar componente
            DB::table('componentes')->where('id', $id)->delete();
            
            DB::commit();
            return redirect()->route('componentes.index')->with('success', 'Componente eliminado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar.');
        }
    }
}