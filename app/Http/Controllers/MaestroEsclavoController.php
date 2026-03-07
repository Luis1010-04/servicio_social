<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaestroEsclavoController extends Controller
{
    /**
     * Muestra el panel de control del esclavo y sus componentes (Sensores/Actuadores).
     */
    public function administrarEsclavo($id)
    {
        $esclavo = DB::table('esclavos_catalogo')->where('id', $id)->first();

        if (!$esclavo) {
            return back()->with('error', 'El equipo no existe.');
        }

        $titulo = "Panel de Control: " . $esclavo->nombre;

        // CONSULTA PRO: Traemos componentes a través de la tabla puente detalle_esclavo_componentes
        $componentes = DB::table('componentes as c')
            ->join('detalle_esclavo_componentes as dec', 'c.id', '=', 'dec.componente_id')
            ->join('unidades_de_medida as um', 'c.unidad_id', '=', 'um.id')
            ->leftJoin('lecturas as l', function($join) {
                $join->on('c.id', '=', 'l.componente_id')
                     ->whereRaw('l.id IN (select MAX(id) from lecturas group by componente_id)');
            })
            ->where('dec.esclavo_id', $id) // Filtramos por la tabla puente
            ->select(
                'c.*', 
                'um.nombre as unidad',
                'l.valor as ultimo_valor',
                'l.created_at as fecha_lectura'
            )
            ->get();

        return view('modules.esclavos.administrar', compact('titulo', 'esclavo', 'componentes'));
    }

    /**
     * Prepara la vista para asignar un esclavo a un maestro.
     */
    public function asignarNuevoEsclavo($id)
    {
        $maestro = DB::table('maestros_catalogo')->where('id', $id)->first();
        
        // Esclavos que no están vinculados a NINGÚN maestro todavía
        $disponibles = DB::table('esclavos_catalogo')
            ->whereNotIn('id', function($query) {
                $query->select('esclavo_id')->from('maestros_esclavos');
            })
            ->get();

        $ubicaciones = DB::table('ubicaciones')->get();
        $titulo = "Vincular Red - Maestro: " . $maestro->nombre;

        return view('modules.MaestroEsclavo.index', compact('maestro', 'disponibles', 'ubicaciones', 'titulo'));
    }

    /**
     * Guarda el vínculo entre Maestro y Esclavo.
     */
    public function storeVinculo(Request $request)
    {
        $request->validate([
            'maestro_id' => 'required',
            'esclavo_id' => 'required',
            'nombre_vinculo' => 'required|string|max:255',
            'ubicacion_id' => 'required'
        ]);

        try {
            DB::table('maestros_esclavos')->insert([
                'maestro_id' => $request->maestro_id,
                'esclavo_id' => $request->esclavo_id,
                'nombre'     => $request->nombre_vinculo,
                'ubicacion_id' => $request->ubicacion_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('maestros.administrar', $request->maestro_id)
                            ->with('success', 'Esclavo vinculado correctamente.');
                                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al vincular esclavo: ' . $e->getMessage());
        }
    }

    /**
     * Desvincula un esclavo de un maestro (Lo libera para que pueda ser asignado a otro).
     */
public function desvincularEsclavo($id)
{
    try {
        // En lugar de buscar por la PK 'id', buscamos por 'esclavo_id' 
        // para asegurar que borramos el vínculo de ese hardware específico
        DB::table('maestros_esclavos')->where('esclavo_id', $id)->delete();
        
        return back()->with('success', 'El esclavo ha sido removido de esta red.');
    } catch (\Exception $e) {
        // Esto nos dirá exactamente por qué falla (si es por una llave foránea)
        return back()->with('error', 'Error al desvincular: ' . $e->getMessage());
    }
}
}