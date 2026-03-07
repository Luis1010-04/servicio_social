<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\UnidadesMedida;
use App\Http\Controllers\comandos;
use App\Http\Controllers\componentes;
use App\Http\Controllers\EsclavosCatalogos;
use App\Http\Controllers\MaestroEsclavoController;
use App\Http\Controllers\MaestrosCatalogo;
use App\Http\Controllers\MaestrosUsuarios;
use App\Http\Controllers\Usuarios;
use App\Http\Controllers\reportes;
use App\Http\Controllers\Ubicaciones;

// Controladores de la carpeta User
use App\Http\Controllers\User\UserMaestroController;
use App\Http\Controllers\User\UserEsclavoController;
use App\Http\Controllers\User\UserUbicacionController;

/*
|--------------------------------------------------------------------------
| Autenticación y Dashboard Base
|--------------------------------------------------------------------------
*/
Route::get('/crear-admin', [AuthController::class, 'crearAdmin']);
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/logear', [AuthController::class, 'logear'])->name('logear');

Route::middleware("auth")->group(function () {
    Route::get('/home', [Dashboard::class, 'index'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/pendiente', [Dashboard::class, 'pendiente'])->name('pendiente.index');
    Route::get('/reportes', [reportes::class, 'index'])->name('reportes.index');
    Route::get('/comandos', [comandos::class, 'index'])->name('comandos.index');
});

/*
|--------------------------------------------------------------------------
| Rutas Administrativas (Catálogos y Gestión Global)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'checkrol:Admin'])->group(function () {
    
    // Usuarios
    Route::resource('users', Usuarios::class)->names('users');
    Route::get('users/cambiar-estado/{id}/{estado}', [Usuarios::class, 'estado'])->name('users.estado');
    Route::get('users/{id}/recursos', [Usuarios::class, 'recursos'])->name('users.recursos');

    // Unidades y Componentes
    Route::resource('unidades-medida', UnidadesMedida::class)->names('unidades.medida');
    Route::resource('componentes', componentes::class)->names('componentes');


    // Catálogo de Maestros
    Route::prefix('maestros-catalogo')->group(function () {
        Route::get('/', [MaestrosCatalogo::class, 'index'])->name('maestros_catalogo.index');
        Route::get('/create', [MaestrosCatalogo::class, 'create'])->name('maestros.catalogo.create');
        Route::post('/store', [MaestrosCatalogo::class, 'store'])->name('maestros.catalogo.store');
        Route::get('/edit/{id}', [MaestrosCatalogo::class, 'edit'])->name('maestros.catalogo.edit');
        Route::put('/update/{id}', [MaestrosCatalogo::class, 'update'])->name('maestros.catalogo.update');
        Route::get('/show-esclavos/{id}', [MaestrosCatalogo::class, 'administrar_esclavos'])->name('esclavos.catalogo.show');
        Route::post('/vincular-esclavo', [MaestrosCatalogo::class, 'vincular_esclavo'])->name('maestros.vincular_esclavo');
        Route::delete('/{id}', [MaestrosCatalogo::class, 'destroy'])->name('maestros.catalogo.destroy');
    });

    // Catálogo de Esclavos
    Route::resource('esclavos-catalogo', EsclavosCatalogos::class)->names('esclavos.catalogo');
    Route::get('esclavos-catalogo/administrar/{id}', [EsclavosCatalogos::class, 'administrar'])->name('esclavos.catalogo.administrar');
});

/*
|--------------------------------------------------------------------------
| Lógica de Vinculación (Maestros / Esclavos / Usuarios)
|--------------------------------------------------------------------------
| Estas rutas mantienen tus controladores originales funcionando.
*/
Route::middleware('auth')->group(function () {
    // Maestros Usuarios
    Route::prefix('maestros_usuarios')->group(function () {
        Route::get('/administrar/{id}', [MaestrosUsuarios::class, 'administrar'])->name('maestros.usuarios.administrar');
        Route::post('/vincular_maestro', [MaestrosUsuarios::class, 'vincular_maestro'])->name('maestros.usuarios.vincular_maestro');
        Route::post('/desvincular_maestro', [MaestrosUsuarios::class, 'desvincular_maestro'])->name('maestros.usuarios.desvincular_maestro');
    });

    // Maestro Esclavo (Relación técnica)
    Route::prefix('maestro_esclavo')->group(function () {
        Route::get('/maestros/{id}/administrar', [MaestrosCatalogo::class, 'administrar_esclavos'])->name('maestros.administrar');
        Route::get('/maestros/{id}/vincular-esclavo', [MaestroEsclavoController::class, 'asignarNuevoEsclavo'])->name('maestros.esclavos.crear');
        Route::post('/maestros/vincular-esclavo', [MaestroEsclavoController::class, 'storeVinculo'])->name('maestros.esclavos.store');
        Route::delete('/maestros/desvincular/{id}', [MaestroEsclavoController::class, 'desvincularEsclavo'])->name('maestros.esclavos.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| PANEL DE USUARIO (Mis Equipos)
|--------------------------------------------------------------------------
| Usamos el nombre 'user.' para no chocar con las rutas de Admin.
*/
Route::middleware(['auth', 'web'])->prefix('mis-equipos')->as('user.')->group(function () {
    Route::resource('maestros', UserMaestroController::class);
    // Cambiamos el nombre aquí para que sea user.maestros.administrar
    Route::get('maestros/{id}/administrar', [UserMaestroController::class, 'administrar'])->name('maestros.administrar');

    // Esclavos del Usuario
    Route::resource('esclavos', UserEsclavoController::class)->except(['create']); 
    Route::get('esclavos/{id}/monitor', [UserEsclavoController::class, 'monitor'])->name('esclavos.monitor');

    // Ubicaciones Privadas
    Route::resource('ubicaciones', UserUbicacionController::class);
}); 