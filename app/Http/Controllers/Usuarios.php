<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Agregamos la Facade para evitar errores de IDE
use Exception;
use Illuminate\Support\Facades\DB;

class Usuarios extends Controller
{
    public function index()
    {
        $titulo = 'Usuarios';
        $datos = User::all(); 
        return view('modules.usuarios.index', compact('titulo', 'datos'));
    }

    public function create()
    {
        $titulo = 'Crear Usuario';
        return view('modules.usuarios.create', compact('titulo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'usuario'  => 'required|unique:users,usuario', 
            'email'    => 'required|email|unique:users,email', 
            'password' => 'required|min:8',
            'rol'      => 'required|in:Admin,Usuario',
        ], [
            'usuario.unique' => 'El nombre de usuario ya está registrado.',
            'email.unique'   => 'Este correo electrónico ya está en uso.',
        ]);

        try {
            User::create([
                'name'     => $request->name,
                'apellido' => $request->apellido,
                'usuario'  => $request->usuario,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'activo'   => true,
                'rol'      => $request->rol,
            ]);

            return to_route('users.index')->with('success', 'Usuario guardado con éxito');
        } catch (Exception $e) {
            return back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $titulo = "Editar Usuario";
        return view('modules.usuarios.edit', compact('user', 'titulo'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'usuario'  => 'required|unique:users,usuario,' . $id,
            'email'    => 'required|email|unique:users,email,' . $id,
            'rol'      => 'required|in:Admin,Usuario',
        ]);

        try {
            $item = User::findOrFail($id); 
            $item->name = $request->name;
            $item->apellido = $request->apellido;
            $item->usuario = $request->usuario;
            $item->email = $request->email;
            $item->rol = $request->rol;

            if ($request->filled('password')) {
                $item->password = Hash::make($request->password);
            }
            
            $item->save();
            return to_route('users.index')->with('success', 'Usuario actualizado con éxito');
        } catch (Exception $e) {
            return back()->with('error', 'No se pudo actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Corrección para el error "Undefined method id"
            // Usamos Auth::id() que es más estándar y reconocido por los IDEs
            if (Auth::id() == $user->id) {
                return back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
            }

            $user->delete();
            return to_route('users.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (Exception $e) {
            return back()->with('error', 'Error al intentar eliminar el usuario.');
        }
    }
    
    public function estado($id, $estado)
    {
        try {
            $item = User::findOrFail($id);
            // Convertimos a booleano/entero por seguridad
            $item->activo = ($estado == 1) ? true : false;
            $item->save();
            
            return response()->json(['success' => true, 'message' => 'Estado actualizado']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cambiar estado'], 500);
        }
    }

    // No olvides importar DB arriba si no está: use Illuminate\Support\Facades\DB;

public function recursos($id)
{
    $user = User::findOrFail($id);
    $titulo = "Gestionar Recursos: " . $user->usuario;

    // 1. Obtener Ubicaciones del usuario
    $ubicaciones = DB::table('ubicaciones')
        ->where('user_id', $id)
        ->get();

    // 2. Obtener Maestros vinculados al usuario (con datos del catálogo)
    $maestros = DB::table('maestros_usuarios as mu')
        ->join('maestros_catalogo as mc', 'mu.maestro_id', '=', 'mc.id')
        ->join('ubicaciones as u', 'mu.ubicacion_id', '=', 'u.id')
        ->where('mu.user_id', $id)
        ->select('mu.*', 'mc.nombre as modelo_base', 'u.nombre as nombre_ubicacion')
        ->get();

    // 3. Obtener Esclavos vinculados a los maestros de este usuario
    // Buscamos en maestros_esclavos, pero filtrando por los maestros que pertenecen al user_id
    $esclavos = DB::table('maestros_esclavos as me')
        ->join('esclavos_catalogo as ec', 'me.esclavo_id', '=', 'ec.id')
        ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
        ->join('ubicaciones as u', 'me.ubicacion_id', '=', 'u.id')
        ->where('mu.user_id', $id)
        ->select('me.*', 'ec.modelo as modelo_esclavo', 'u.nombre as nombre_ubicacion', 'mu.nombre as nombre_maestro')
        ->get();

    return view('modules.usuarios.recursos', compact('user', 'titulo', 'ubicaciones', 'maestros', 'esclavos'));
}
}