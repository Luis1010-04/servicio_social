<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EsclavosCatalogos extends Controller
{
    public function index()
    {
        $titulo = "Catálogo de Equipos Esclavos";

        // Solo traemos la información técnica del catálogo y el conteo de componentes
        $datos = DB::table('esclavos_catalogo as ec')
            ->select(
                'ec.*',
                DB::raw('(SELECT COUNT(*) FROM detalle_esclavo_componentes WHERE esclavo_id = ec.id) as total_componentes')
            )
            ->get();

        return view('modules.esclavos.index', compact('titulo', 'datos'));
    }

    public function create()
    {
        $titulo = "Registro de Nuevo Esclavo";
        $ubicaciones = DB::table('ubicaciones')->get();
        
        // Componentes disponibles (puedes ajustar el whereNotIn si prefieres que se repitan)
        $componentes_disponibles = DB::table('componentes')->get();

        return view('modules.esclavos.create', compact('titulo', 'ubicaciones', 'componentes_disponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'modelo'       => 'required|unique:esclavos_catalogo,modelo',
            'componentes'  => 'nullable|array' 
        ]);

        DB::beginTransaction();
        try {
            $esclavoId = DB::table('esclavos_catalogo')->insertGetId([
                'nombre'       => $request->nombre,
                'modelo'       => $request->modelo,
                'activo'       => $request->has('activo') ? 1 : 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            if ($request->has('componentes')) {
                $detalle = [];
                foreach ($request->componentes as $compId) {
                    $detalle[] = [
                        'esclavo_id'    => $esclavoId,
                        'componente_id' => $compId
                    ];
                }
                DB::table('detalle_esclavo_componentes')->insert($detalle);
            }

            DB::commit();
            return to_route('esclavos.catalogo.index')->with('success', 'Equipo creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $titulo = "Editar Esclavo";
        $esclavo = DB::table('esclavos_catalogo')->where('id', $id)->first();
        
        if (!$esclavo) return abort(404);

        $ubicaciones = DB::table('ubicaciones')->get();
        $componentes_disponibles = DB::table('componentes')->get();
        
        // Obtenemos los IDs de los componentes que ya tiene este esclavo
        $componentes_actuales = DB::table('detalle_esclavo_componentes')
            ->where('esclavo_id', $id)
            ->pluck('componente_id')
            ->toArray();

        return view('modules.esclavos.edit', compact('titulo', 'esclavo', 'ubicaciones', 'componentes_disponibles', 'componentes_actuales'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'modelo' => 'required|unique:esclavos_catalogo,modelo,'.$id,
            // quitamos la validación de ubicacion_id
            'componentes' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            DB::table('esclavos_catalogo')->where('id', $id)->update([
                'nombre'     => $request->nombre,
                'modelo'     => $request->modelo,
                'activo'     => $request->has('activo') ? 1 : 0,
                'updated_at' => now(),
                // quitamos 'ubicacion_id' => $request->ubicacion_id
            ]);

            // Sincronización de componentes se queda igual...
            DB::table('detalle_esclavo_componentes')->where('esclavo_id', $id)->delete();
            if ($request->has('componentes')) {
                $detalle = [];
                foreach ($request->componentes as $compId) {
                    $detalle[] = ['esclavo_id' => $id, 'componente_id' => $compId];
                }
                DB::table('detalle_esclavo_componentes')->insert($detalle);
            }

            DB::commit();
            return to_route('esclavos.catalogo.index')->with('success', 'Modelo actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function administrar($id)
    {
        // Solo traemos el equipo, sin joins de ubicación
        $esclavo = DB::table('esclavos_catalogo')
            ->where('id', $id)
            ->first();

        if (!$esclavo) return abort(404);

        // Los componentes técnicos del modelo
        $componentes = DB::table('detalle_esclavo_componentes as d')
            ->join('componentes as c', 'd.componente_id', '=', 'c.id')
            ->join('unidades_de_medida as u', 'c.unidad_id', '=', 'u.id') 
            ->select('c.*', 'u.nombre as nombre_unidad')
            ->where('d.esclavo_id', $id)
            ->get();

        $titulo = "Detalle Técnico: " . $esclavo->nombre;
        return view('modules.esclavos.monitor', compact('esclavo', 'componentes', 'titulo'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Limpiamos todas las tablas relacionadas antes de borrar el hardware
            DB::table('detalle_esclavo_componentes')->where('esclavo_id', $id)->delete();
            DB::table('maestros_esclavos')->where('esclavo_id', $id)->delete();
            DB::table('esclavos_catalogo')->where('id', $id)->delete();

            DB::commit();
            return to_route('esclavos.catalogo.index')->with('success', 'Equipo eliminado del catálogo.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}