<?php

namespace App\Http\Controllers\Compartidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\User; // Importante para la sugerencia de tipo

class PerfilController extends Controller
{
    /**
     * Mostrar la vista del perfil
     */
    public function index()
    {
        return view('modules.compartidos.perfil', [
            'user' => Auth::user(),
            'titulo' => 'Mi Perfil'
        ]);
    }

    /**
     * Actualizar datos generales e imagen
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user(); // La anotación @var quita el error de "Undefined method"

        $request->validate([
            'name'     => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'usuario'  => 'required|string|max:255|unique:users,usuario,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Procesar la imagen si se sube una nueva
        if ($request->hasFile('imagen_url')) {
            // Borrar imagen vieja si existe en el storage y no es la default
            if ($user->imagen_url && Storage::disk('public')->exists($user->imagen_url)) {
                Storage::disk('public')->delete($user->imagen_url);
            }

            // Guardar nueva imagen en storage/app/public/perfiles
            $path = $request->file('imagen_url')->store('perfiles', 'public');
            $user->imagen_url = $path;
        }

        // Asignar los demás valores
        $user->name = $request->name;
        $user->apellido = $request->apellido;
        $user->usuario = $request->usuario;
        $user->email = $request->email;

        $user->save();

        return back()->with('success', 'Tu información ha sido actualizada correctamente.');
    }

    /**
     * Actualizar la contraseña con validación de seguridad
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'newpassword'      => ['required', 'confirmed', Password::min(8)],
        ], [
            'newpassword.confirmed' => 'La nueva contraseña y su confirmación no coinciden.',
            'newpassword.min'       => 'La contraseña debe tener al menos 8 caracteres.'
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Verificar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual que ingresaste es incorrecta.']);
        }

        // Guardar nueva contraseña
        $user->password = Hash::make($request->newpassword);
        $user->save();

        return back()->with('success', '¡Contraseña cambiada con éxito!');
    }
}