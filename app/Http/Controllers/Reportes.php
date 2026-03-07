<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Reportes extends Controller
{
    public function index()
    {
        $titulo = "Panel de Reportes e Historial";
        // Necesitamos la lista de esclavos para el formulario de reportes
        $esclavos = DB::table('esclavos_catalogo')->get();
        return view('modules.reportes.index', compact('titulo', 'esclavos'));
    }

    public function generarReporte(Request $request)
    {
        $request->validate([
            'esclavo_id' => 'required',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $id = $request->esclavo_id;
        $inicio = $request->fecha_inicio . " 00:00:00";
        $fin = $request->fecha_fin . " 23:59:59";

        // 2. OBTENER INFO DEL ESCLAVO Y SU UBICACIÓN REAL (Relacional)
        $infoEsclavo = DB::table('esclavos_catalogo as e')
            ->join('ubicaciones as u', 'e.ubicacion_id', '=', 'u.id') // JOIN con el nuevo catálogo
            ->where('e.id', $id)
            ->select('e.nombre as esclavo', 'u.nombre as ubicacion', 'e.modelo')
            ->first();

        // 3. HISTORIAL DE LECTURAS (Usando la tabla puente detalle_esclavo_componentes)
        // Ya no buscamos componentes.esclavo_id porque lo eliminaste, usamos el puente
        $lecturas = DB::table('lecturas as l')
            ->join('componentes as c', 'l.componente_id', '=', 'c.id')
            ->join('detalle_esclavo_componentes as dec', 'c.id', '=', 'dec.componente_id')
            ->join('unidades_de_medida as um', 'c.unidad_id', '=', 'um.id')
            ->where('dec.esclavo_id', $id) // Filtramos por el esclavo en la tabla puente
            ->whereBetween('l.created_at', [$inicio, $fin])
            ->select('c.nombre', 'l.valor', 'um.nombre as unidad', 'l.created_at')
            ->orderBy('l.created_at', 'desc')
            ->get();

        // 4. HISTORIAL DE COMANDOS (Actuadores)
        $comandos = DB::table('comando_componentes as cc')
            ->join('componentes as c', 'cc.componente_id', '=', 'c.id')
            ->join('detalle_esclavo_componentes as dec', 'c.id', '=', 'dec.componente_id')
            ->join('users as u', 'cc.user_id', '=', 'u.id')
            ->where('dec.esclavo_id', $id) // Filtramos por el esclavo en la tabla puente
            ->whereBetween('cc.created_at', [$inicio, $fin])
            ->select('c.nombre', 'cc.accion', 'u.usuario as operador', 'cc.created_at')
            ->orderBy('cc.created_at', 'desc')
            ->get();

        if ($lecturas->isEmpty() && $comandos->isEmpty()) {
            return back()->with('error', 'No se encontraron registros en el periodo seleccionado.');
        }

        $titulo = "Reporte Detallado: " . ($infoEsclavo->esclavo ?? 'Dispositivo');

        return view('modules.reportes.resultado', compact('titulo', 'infoEsclavo', 'lecturas', 'comandos', 'inicio', 'fin'));
    }
}