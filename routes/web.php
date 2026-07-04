<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authcontroller;
use App\Http\Controllers\dashboardcontroller;
use App\Http\Controllers\unidadmedidacontroller;
use App\Http\Controllers\comandocontroller;
use App\Http\Controllers\compartidos\configuracioncontroller;
use App\Http\Controllers\compartidos\notificacionescontroller;
use App\Http\Controllers\compartidos\perfilcontroller;
use App\Http\Controllers\componentecontroller;
use App\Http\Controllers\esclavocatalogocontroller;
use App\Http\Controllers\MaestroEsclavoController;
use App\Http\Controllers\maestrocatalogocontroller;
use App\Http\Controllers\maestrousuariocontroller;
use App\Http\Controllers\AdminReportesController;
use App\Http\Controllers\usuariocontroller;
use App\Http\Controllers\user\UserComponenteController;
use App\Http\Controllers\user\UserMaestroController;
use App\Http\Controllers\user\UserEsclavoController;
use App\Http\Controllers\user\UserUbicacionController;
use App\Http\Controllers\user\ReportesController;
use App\Http\Controllers\user\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Autenticación y Dashboard Base
|--------------------------------------------------------------------------
*/
Route::get('/', [authcontroller::class, 'index'])->name('login');
Route::post('/logear', [authcontroller::class, 'logear'])
    ->middleware('throttle:5,1')
    ->name('logear');

Route::middleware('auth')->group(function () {
    Route::get('/User_home', [UserDashboardController::class, 'index'])->name('user.home');
    Route::get('/logout', [authcontroller::class, 'logout'])->name('logout');
    Route::get('/pendiente', [dashboardcontroller::class, 'pendiente'])->name('pendiente.index');
    Route::get('/comandos', [comandocontroller::class, 'index'])->name('comandos.index');
});

/*
|--------------------------------------------------------------------------
| Rutas Administrativas (Catálogos y Gestión Global)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'checkrol:Admin'])->group(function () {
    Route::get('/home', [dashboardcontroller::class, 'index'])->name('home');

    // Usuarios
    Route::resource('users', usuariocontroller::class)->names('users');
    Route::get('users/cambiar-estado/{id}/{estado}', [usuariocontroller::class, 'estado'])->name('users.estado');
    Route::get('users/{id}/recursos', [usuariocontroller::class, 'recursos'])->name('users.recursos');

    // Unidades y Componentes
    Route::resource('unidades-medida', unidadmedidacontroller::class)->names('unidades.medida');
    Route::resource('componentes', componentecontroller::class)->names('componentes');

    // Reportes del administrador
    Route::prefix('admin/reportes')->name('admin.reportes.')->middleware('auth')->group(function () {
        Route::get('/', [AdminReportesController::class, 'index'])->name('index');
        Route::get('/api/usuarios', [AdminReportesController::class, 'getUsuarios'])->name('api.usuarios');
        Route::get('/api/maestros-catalogo', [AdminReportesController::class, 'getMaestrosCatalogo'])->name('api.maestros');
        Route::get('/api/esclavos-catalogo', [AdminReportesController::class, 'getEsclavosCatalogo'])->name('api.esclavos');
        Route::get('/api/tabla-maestra', [AdminReportesController::class, 'getTablaMaestra'])->name('api.tabla_maestra');
        Route::post('/api/influx-status', [AdminReportesController::class, 'checkInfluxStatus'])->name('api.influx');
    });

    // Catálogo de Maestros
    Route::prefix('maestros-catalogo')->group(function () {
        Route::get('/', [maestrocatalogocontroller::class, 'index'])->name('maestros_catalogo.index');
        Route::get('/create', [maestrocatalogocontroller::class, 'create'])->name('maestros.catalogo.create');
        Route::post('/store', [maestrocatalogocontroller::class, 'store'])->name('maestros.catalogo.store');
        Route::get('/edit/{id}', [maestrocatalogocontroller::class, 'edit'])->name('maestros.catalogo.edit');
        Route::put('/update/{id}', [maestrocatalogocontroller::class, 'update'])->name('maestros.catalogo.update');
        Route::get('/show-esclavos/{id}', [maestrocatalogocontroller::class, 'administrar_esclavos'])->name('esclavos.catalogo.show');
        Route::post('/vincular-esclavo', [maestrocatalogocontroller::class, 'vincular_esclavo'])->name('maestros.vincular_esclavo');
        Route::delete('/{id}', [maestrocatalogocontroller::class, 'destroy'])->name('maestros.catalogo.destroy');
    });

    // Catálogo de Esclavos
    Route::resource('esclavos-catalogo', esclavocatalogocontroller::class)->names('esclavos.catalogo');
    Route::get('esclavos-catalogo/administrar/{id}', [esclavocatalogocontroller::class, 'administrar'])->name('esclavos.catalogo.administrar');
});

/*
|--------------------------------------------------------------------------
| Lógica de Vinculación (Maestros / Esclavos / Usuarios)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::prefix('maestros_usuarios')->group(function () {
        Route::get('/administrar/{id}', [maestrousuariocontroller::class, 'administrar'])->name('maestros.usuarios.administrar');
        Route::post('/vincular_maestro', [maestrousuariocontroller::class, 'vincular_maestro'])->name('maestros.usuarios.vincular_maestro');
        Route::post('/desvincular_maestro', [maestrousuariocontroller::class, 'desvincular_maestro'])->name('maestros.usuarios.desvincular_maestro');
    });

    Route::prefix('maestro_esclavo')->group(function () {
        Route::get('/maestros/{id}/administrar', [maestrocatalogocontroller::class, 'administrar_esclavos'])->name('maestros.administrar');
        Route::get('/maestros/{id}/vincular-esclavo', [MaestroEsclavoController::class, 'asignarNuevoEsclavo'])->name('maestros.esclavos.crear');
        Route::post('/maestros/vincular-esclavo', [MaestroEsclavoController::class, 'storeVinculo'])->name('maestros.esclavos.store');
        Route::delete('/maestros/desvincular/{id}', [MaestroEsclavoController::class, 'desvincularEsclavo'])->name('maestros.esclavos.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| PANEL DE USUARIO (Mis Equipos)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'web'])->prefix('mis-equipos')->as('user.')->group(function () {
    Route::get('/dashboard-data', [UserDashboardController::class, 'getRealTimeData'])->name('dashboard.data');
    Route::resource('maestros', UserMaestroController::class);
    Route::get('maestros/{id}/administrar', [UserMaestroController::class, 'administrar'])->name('maestros.administrar');

    Route::resource('esclavos', UserEsclavoController::class)->except(['create']);
    Route::get('esclavos/{id}/monitor', [UserEsclavoController::class, 'monitor'])->name('esclavos.monitor');
    Route::get('/esclavo/{id}/ultima-lectura', [UserEsclavoController::class, 'getUltimaLectura']);
    Route::get('/configurar-dispositivo/{serie}', [UserEsclavoController::class, 'getConfiguracion']);

    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
    Route::get('/obtener-esclavos/{id}', [ReportesController::class, 'getEsclavosByMaestro'])->name('reportes.getEsclavos');
    Route::get('/obtener-componentes/{id}', [ReportesController::class, 'getComponentesByEsclavo'])->name('reportes.getComponentes');
    Route::get('/generar', [ReportesController::class, 'generarReporte'])->name('reportes.generar');

    Route::resource('ubicaciones', UserUbicacionController::class);
    Route::post('/componente/{esclavoId}/controlar', [UserComponenteController::class, 'controlar'])->name('componente.controlar');
});

/*
|--------------------------------------------------------------------------
| Perfil y Configuración (Ambos roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/mi-perfil', [perfilcontroller::class, 'index'])->name('perfil.index');
    Route::put('/perfil/actualizar', [perfilcontroller::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [perfilcontroller::class, 'updatePassword'])->name('perfil.password');
    Route::get('/configuracion', [configuracioncontroller::class, 'index'])->name('configuracion.index');
    Route::get('/notificaciones', [notificacionescontroller::class, 'index'])->name('notificaciones.index');
});
