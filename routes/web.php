<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\UnidadesMedida;
use App\Http\Controllers\comandos;
use App\Http\Controllers\Compartidos\ConfiguracionController;
use App\Http\Controllers\Compartidos\NotificacionesController;
use App\Http\Controllers\Compartidos\PerfilController;
use App\Http\Controllers\componentes;
use App\Http\Controllers\EsclavosCatalogos;
use App\Http\Controllers\MaestroEsclavoController;
use App\Http\Controllers\MaestrosCatalogo;
use App\Http\Controllers\MaestrosUsuarios;
use App\Http\Controllers\Reportes;
use App\Http\Controllers\Usuarios;
use App\Http\Controllers\User\UserComponenteController;

// Controladores de la carpeta User
use App\Http\Controllers\User\UserMaestroController;
use App\Http\Controllers\User\UserEsclavoController;
use App\Http\Controllers\User\UserUbicacionController;
use App\Http\Controllers\User\ReportesController;
use App\Http\Controllers\user\UserDashboard;

/*
|--------------------------------------------------------------------------
| Autenticación y Dashboard Base
|--------------------------------------------------------------------------
*/
Route::get('/crear-admin', [AuthController::class, 'crearAdmin']);
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/logear', [AuthController::class, 'logear'])->name('logear');

Route::middleware("auth")->group(function () {
 
    //Dashboard del usuario
    Route::get('/User_home', [UserDashboard::class, 'index'])->name('user.home');
    //Rutas extra
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/pendiente', [Dashboard::class, 'pendiente'])->name('pendiente.index');

    // Ruta para el AJAX de esclavos
 
    Route::get('/comandos', [comandos::class, 'index'])->name('comandos.index');
});

/*
|--------------------------------------------------------------------------
| Rutas Administrativas (Catálogos y Gestión Global)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'checkrol:Admin'])->group(function () {
       //Dashboard del administrador
    Route::get('/home', [Dashboard::class, 'index'])->name('home');   
    // Usuarios
    Route::resource('users', Usuarios::class)->names('users');
    Route::get('users/cambiar-estado/{id}/{estado}', [Usuarios::class, 'estado'])->name('users.estado');
    Route::get('users/{id}/recursos', [Usuarios::class, 'recursos'])->name('users.recursos');

    // Unidades y Componentes
    Route::resource('unidades-medida', UnidadesMedida::class)->names('unidades.medida');
    Route::resource('componentes', componentes::class)->names('componentes');
    //Reprtes del administrador
    Route::get('/reportes_Admin', [Reportes::class, 'index'])->name('admin.reportes.index');
    // Route::get('/get-maestros/{userId}', [Reportes::class, 'getMaestrosByUser']);
    // Route::get('/get-esclavos/{maestroId}', [Reportes::class, 'getEsclavosByMaestro']);
    // Route::get('/get-componentes/{esclavoId}', [Reportes::class, 'getComponentesByEsclavo']);
    // Route::get('/generar', [Reportes::class, 'generarReporteAdmin']);

    //
     Route::prefix('admin/reportes')->group(function () {
    // Route::get('/', [Reportes::class, 'index'])->name('admin.reportes.index');
    Route::get('/inventario-global', [Reportes::class, 'getInventarioGlobal']);
    Route::get('/get-maestros/{userId}', [Reportes::class, 'getMaestrosByUser']);
    Route::get('/get-esclavos/{maestroId}', [Reportes::class, 'getEsclavosByMaestro']);
    Route::get('/get-componentes/{esclavoId}', [Reportes::class, 'getComponentesByEsclavo']);
    Route::get('/generar', [Reportes::class, 'generarReporteAdmin']);
    Route::get('/kpi-usuarios', [Reportes::class, 'getUsuariosKpi']);
    Route::get('/kpi-maestros', [Reportes::class, 'getMaestrosKpi']);
    Route::get('/kpi-esclavos', [Reportes::class, 'getEsclavosKpi']);
});


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
    //Ruta para el dashboard
    Route::get('/dashboard-data', [App\Http\Controllers\User\UserDashboard::class, 'getRealTimeData'])->name('dashboard.data');
    Route::resource('maestros', UserMaestroController::class);
    Route::get('maestros/{id}/administrar', [UserMaestroController::class, 'administrar'])->name('maestros.administrar');

    // Esclavos del Usuario
    Route::resource('esclavos', UserEsclavoController::class)->except(['create']); 
    Route::get('esclavos/{id}/monitor', [UserEsclavoController::class, 'monitor'])->name('esclavos.monitor');
    Route::get('/esclavo/{id}/ultima-lectura', [App\Http\Controllers\User\UserEsclavoController::class, 'getUltimaLectura']);
    Route::get('/configurar-dispositivo/{serie}', [UserEsclavoController::class, 'getConfiguracion']);
    //Rutas de reportes
    // Rutas de reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');

    Route::get('/obtener-esclavos/{id}', [ReportesController::class, 'getEsclavosByMaestro'])->name('reportes.getEsclavos');

    Route::get('/obtener-componentes/{id}', [ReportesController::class, 'getComponentesByEsclavo'])->name('reportes.getComponentes');
    Route::get('/generar', [ReportesController::class, 'generarReporte'])->name('reportes.generar');
    Route::resource('ubicaciones', UserUbicacionController::class);
    
    Route::post('/componente/{esclavoId}/controlar', [UserComponenteController::class, 'controlar'])->name('componente.controlar');
}); 

Route::middleware(['auth'])->group(function () {
    
    
    Route::get('/mi-perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
    // Configuración (Preferencias del sistema)
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

    // Notificaciones (Historial de alertas)
    Route::get('/notificaciones', [NotificacionesController::class, 'index'])->name('notificaciones.index');
    
});