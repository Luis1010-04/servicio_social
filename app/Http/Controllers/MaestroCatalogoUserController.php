<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MaestroCatalogoUserController extends Controller
{
    public function index()
    {
        $misMaestros = DB::table('maestros_usuarios as mu')
            ->join('maestros_catalogo as mc', 'mu.maestro_id', '=', 'mc.id')
            ->select('mu.*', 'mc.modelo as modelo_hardware')
            ->where('mu.user_id', Auth::id())
            ->get();

        return view('maestros.usuarios.index', compact('misMaestros'));
    }

    public function create()
    {
        // Obtenemos solo los maestros que el administrador tiene activos
        $catalogo = DB::table('maestros_catalogo')
            ->where('activo', 1)
            ->get();

        return view('maestros.usuarios.create', compact('catalogo'));
    }

    public function store(Request $request)
    {
        // Validamos lo que llega del formulario
        $request->validate([
            'maestro_id' => 'required',
            'nombre' => 'required|max:255',
            'localizacion' => 'required'
        ]);

        // Insertamos en la tabla según tu script de BD
        DB::table('maestros_usuarios')->insert([
            'maestro_id'   => $request->maestro_id,
            'user_id'      => Auth::id(),
            'nombre'       => $request->nombre,
            'localizacion' => $request->localizacion,
            'descripcion'  => $request->descripcion,
            'imagen_ruta'  => 'default.png', // Obligatorio por tu NOT NULL
            'topico'       => 'vacio',       // Obligatorio por tu NOT NULL
            'Broker'       => 'vacio',       // Obligatorio por tu NOT NULL
            'created_at'   => now(),
            'updated_at'   => now()
        ]);

        return redirect()->route('maestros.user.index')->with('status', 'Maestro asignado con éxito');
    }

    public function destroy($id)
    {
        // Borramos asegurándonos que el registro sea del usuario actual
        DB::table('maestros_usuarios')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('maestros.user.index');
    }
}