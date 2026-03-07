<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserUbicacionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $titulo = "Mis Ubicaciones";
        
        // Solo traemos las del usuario actual
        $ubicaciones = DB::table('ubicaciones')
            ->where('user_id', $userId)
            ->get();

        return view('modules.vistasUsuario.ubicaciones.index', compact('titulo', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        DB::table('ubicaciones')->insert([
            'nombre'     => $request->nombre,
            'user_id'    => Auth::id(), // Forzamos que sea del usuario logueado
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Ubicación creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        // Seguridad: solo actualizar si le pertenece
        DB::table('ubicaciones')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'nombre'     => $request->nombre,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Ubicación actualizada.');
    }

    public function destroy($id)
    {
        // Seguridad: solo eliminar si le pertenece
        // OJO: Si tienes llaves foráneas en maestros_usuarios, esto podría fallar si hay equipos ahí.
        $existeRelacion = DB::table('maestros_usuarios')->where('ubicacion_id', $id)->exists();

        if ($existeRelacion) {
            return back()->with('error', 'No puedes eliminar esta ubicación porque tiene equipos asignados.');
        }

        DB::table('ubicaciones')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Ubicación eliminada.');
    }

    public function edit($id)
{
    $titulo = "Editar Ubicación";
    
    // Filtro de seguridad: Solo mis ubicaciones
    $ubicacion = DB::table('ubicaciones')
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    return view('modules.vistasUsuario.ubicaciones.edit', compact('titulo', 'ubicacion'));
}
}