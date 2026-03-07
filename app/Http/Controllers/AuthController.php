<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AuthController extends Controller
{
    public function index(){
        $titulo = 'Login de usuarios';
        return view('modules.auth.login', compact('titulo'));
    }
    public function logear(Request $request){
        //Validar Datos de las credenciales
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email',$request->email)->first();
        // Validar usuario y contraseña
        if(!$user || !Hash::check($request->password,$user->password)){
            return back()->withErrors(['email' => 'Credenciales incorrectas'])->withInput();
        }
        //Usuario activo
        if(!$user->activo){
            return back()->withErrors(['email' => 'Usuario inactivo']);
        }
        //Crear la sesion de usuario
        FacadesAuth::login($user);
        $request->session()->regenerate();
        return to_route ('home');
    }
    public function crearAdmin(){
        //Crear admin
        User::create([
            'name' => 'Admin',
            'apellido' => 'Glez',
            'usuario'  => 'admin01',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'activo' => true,
            'rol' => 'admin'
        ]);
        return 'Admin creado exitosamente';
    }
    public function logout(){
        FacadesAuth::logout();
        return to_route('login');
    }
}

