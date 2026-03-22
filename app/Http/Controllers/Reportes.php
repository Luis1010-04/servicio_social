<?php

namespace App\Http\Controllers;

use App\Models\MaestroUsuario;
use App\Models\EsclavosCatalogo;
use App\Models\Lectura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Reportes extends Controller
{
    public function index()
    {
        $titulo = "Panel de Reportes e Historial";
        $user = Auth::user();

        if ($user->rol === 'Admin') {
            // El Admin ve todos los maestros de todos los usuarios
            $maestros = MaestroUsuario::with('ubicacion')->get();
            // Para el admin, cargamos todos los esclavos inicialmente o por AJAX
            $esclavos = EsclavosCatalogo::all();
        } else {
            // El Usuario solo ve sus maestros
            $maestros = MaestroUsuario::where('user_id', $user->id)
                        ->with('ubicacion')
                        ->get();
            // Solo esclavos vinculados a sus maestros
            $esclavos = EsclavosCatalogo::whereHas('maestroEsclavos', function($q) use ($user) {
                $q->whereHas('maestroUsuario', function($sq) use ($user) {
                    $sq->where('user_id', $user->id);
                });
            })->get();
        }

        return view('modules.reportes.index', compact('titulo', 'maestros', 'esclavos'));
    }

    // API para cargar esclavos dinámicamente vía AJAX (Para una mejor UX)
    public function getEsclavosByMaestro($maestro_id)
    {
        // Esta tabla puente une Maestros con Esclavos
        $esclavos = DB::table('maestros_esclavos as me')
            ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
            ->where('me.maestro_id', $maestro_id)
            ->select('ec.id', 'ec.nombre', 'ec.modelo')
            ->get();

        return response()->json($esclavos);
    }
    public function generarReporte(Request $request)
{
    // 1. Validar la entrada
    $request->validate([
        'maestro_id' => 'required',
        'esclavo_id' => 'required',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
    ]);

    $inicio = $request->fecha_inicio . " 00:00:00";
    $fin = $request->fecha_fin . " 23:59:59";

    // 2. Consulta de Lecturas con Filtro de Seguridad
    // Usamos Query Builder para mayor velocidad en reportes grandes
    $lecturas = DB::table('lecturas as l')
        ->join('componentes as c', 'l.componente_id', '=', 'c.id')
        ->join('detalle_esclavo_componentes as dec', 'c.id', '=', 'dec.componente_id')
        ->join('unidades_de_medida as um', 'c.unidad_id', '=', 'um.id')
        ->where('dec.esclavo_id', $request->esclavo_id)
        ->whereBetween('l.created_at', [$inicio, $fin])
        ->select(
            'c.nombre as componente', 
            'l.valor', 
            'um.nombre as unidad', 
            'l.created_at'
        )
        ->orderBy('l.created_at', 'asc')
        ->get();

    // 3. Obtener info del esclavo para el encabezado del reporte
    $infoEsclavo = DB::table('esclavos_catalogo')->where('id', $request->esclavo_id)->first();

    if ($lecturas->isEmpty()) {
        return back()->with('error', 'No hay datos para este dispositivo en las fechas seleccionadas.');
    }

    // 4. Retornar a la vista de resultados
    $titulo = "Resultado del Reporte";
    return view('modules.reportes.resultado', compact('lecturas', 'infoEsclavo', 'titulo', 'inicio', 'fin'));
}
}