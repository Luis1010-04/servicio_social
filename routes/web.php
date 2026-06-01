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
use App\Http\Controllers\maestroesclavocontroller;
use App\Http\Controllers\maestrocatalogocontroller;
use App\Http\Controllers\maestrousuariocontroller;
use App\Http\Controllers\adminreportescontroller;
use App\Http\Controllers\usuariocontroller;
use App\Http\Controllers\user\usercomponentecontroller;

// Controladores de la carpeta User
use App\Http\Controllers\user\usermaestrocontroller;
use App\Http\Controllers\user\useresclavocontroller;
use App\Http\Controllers\user\userubicacioncontroller;
use App\Http\Controllers\user\reportescontroller;
use App\Http\Controllers\user\userdashboardcontroller;

/*
|--------------------------------------------------------------------------
| Autenticación y Dashboard Base
|--------------------------------------------------------------------------
*/
Route::get('/crear-admin', [authcontroller::class, 'crearAdmin']);
Route::get('/', [authcontroller::class, 'index'])->name('login');
Route::post('/logear', [authcontroller::class, 'logear'])->name('logear');

Route::middleware("auth")->group(function () {
 
    //Dashboard del usuario
    Route::get('/User_home', [userdashboardcontroller::class, 'index'])->name('user.home');
    //Rutas extra
    Route::get('/logout', [authcontroller::class, 'logout'])->name('logout');
    Route::get('/pendiente', [dashboardcontroller::class, 'pendiente'])->name('pendiente.index');

    // Ruta para el AJAX de esclavos
 
    Route::get('/comandos', [comandocontroller::class, 'index'])->name('comandos.index');
});

/*
|--------------------------------------------------------------------------
| Rutas Administrativas (Catálogos y Gestión Global)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'checkrol:Admin'])->group(function () {
       //Dashboard del administrador
    Route::get('/home', [dashboardcontroller::class, 'index'])->name('home');   
    // Usuarios
    Route::resource('users', usuariocontroller::class)->names('users');
    Route::get('users/cambiar-estado/{id}/{estado}', [usuariocontroller::class, 'estado'])->name('users.estado');
    Route::get('users/{id}/recursos', [usuariocontroller::class, 'recursos'])->name('users.recursos');

    // Unidades y Componentes
    Route::resource('unidades-medida', unidadmedidacontroller::class)->names('unidades.medida');
    Route::resource('componentes', componentecontroller::class)->names('componentes');
    //Reprtes del administrador
    //Route::get('/reportes_Admin', [Reportes::class, 'index'])->name('admin.reportes.index');
    // Route::get('/get-maestros/{userId}', [Reportes::class, 'getMaestrosByUser']);
    // Route::get('/get-esclavos/{maestroId}', [Reportes::class, 'getEsclavosByMaestro']);
    // Route::get('/get-componentes/{esclavoId}', [Reportes::class, 'getComponentesByEsclavo']);
    // Route::get('/generar', [Reportes::class, 'generarReporteAdmin']);

    //

    Route::prefix('admin/reportes')->name('admin.reportes.')->middleware(['auth'])->group(function () {
    // Vista principal
    Route::get('/', [adminreportescontroller::class, 'index'])->name('index');

    // Endpoints para las DataTables (Devuelven JSON)
    Route::get('/api/usuarios', [adminreportescontroller::class, 'getUsuarios'])->name('api.usuarios');
    Route::get('/api/maestros-catalogo', [adminreportescontroller::class, 'getMaestrosCatalogo'])->name('api.maestros');
    Route::get('/api/esclavos-catalogo', [adminreportescontroller::class, 'getEsclavosCatalogo'])->name('api.esclavos');
    Route::get('/api/tabla-maestra', [adminreportescontroller::class, 'getTablaMaestra'])->name('api.tabla_maestra');
    
    // Endpoint individual para InfluxDB (El "escáner en vivo")
    Route::post('/api/influx-status', [adminreportescontroller::class, 'checkInfluxStatus'])->name('api.influx');
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
    // Maestros Usuarios
    Route::prefix('maestros_usuarios')->group(function () {
        Route::get('/administrar/{id}', [maestrousuariocontroller::class, 'administrar'])->name('maestros.usuarios.administrar');
        Route::post('/vincular_maestro', [maestrousuariocontroller::class, 'vincular_maestro'])->name('maestros.usuarios.vincular_maestro');
        Route::post('/desvincular_maestro', [maestrousuariocontroller::class, 'desvincular_maestro'])->name('maestros.usuarios.desvincular_maestro');
    });

    // Maestro Esclavo (Relación técnica)
    Route::prefix('maestro_esclavo')->group(function () {
        Route::get('/maestros/{id}/administrar', [maestrocatalogocontroller::class, 'administrar_esclavos'])->name('maestros.administrar');
        Route::get('/maestros/{id}/vincular-esclavo', [maestroesclavocontroller::class, 'asignarNuevoEsclavo'])->name('maestros.esclavos.crear');
        Route::post('/maestros/vincular-esclavo', [maestroesclavocontroller::class, 'storeVinculo'])->name('maestros.esclavos.store');
        Route::delete('/maestros/desvincular/{id}', [maestroesclavocontroller::class, 'desvincularEsclavo'])->name('maestros.esclavos.destroy');
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
    Route::get('/dashboard-data', [userdashboardcontroller::class, 'getRealTimeData'])->name('dashboard.data');
    Route::resource('maestros', usermaestrocontroller::class);
    Route::get('maestros/{id}/administrar', [usermaestrocontroller::class, 'administrar'])->name('maestros.administrar');

    // Esclavos del Usuario
    Route::resource('esclavos', useresclavocontroller::class)->except(['create']); 
    Route::get('esclavos/{id}/monitor', [useresclavocontroller::class, 'monitor'])->name('esclavos.monitor');
    Route::get('/esclavo/{id}/ultima-lectura', [useresclavocontroller::class, 'getUltimaLectura']);
    Route::get('/configurar-dispositivo/{serie}', [useresclavocontroller::class, 'getConfiguracion']);
    //Rutas de reportes
    // Rutas de reportes
    Route::get('/reportes', [reportescontroller::class, 'index'])->name('reportes.index');

    Route::get('/obtener-esclavos/{id}', [reportescontroller::class, 'getEsclavosByMaestro'])->name('reportes.getEsclavos');

    Route::get('/obtener-componentes/{id}', [reportescontroller::class, 'getComponentesByEsclavo'])->name('reportes.getComponentes');
    Route::get('/generar', [reportescontroller::class, 'generarReporte'])->name('reportes.generar');
    Route::resource('ubicaciones', userubicacioncontroller::class);
    
    Route::post('/componente/{esclavoId}/controlar', [usercomponentecontroller::class, 'controlar'])->name('componente.controlar');
}); 

Route::middleware(['auth'])->group(function () {
    
    
    Route::get('/mi-perfil', [perfilcontroller::class, 'index'])->name('perfil.index');
    Route::put('/perfil/actualizar', [perfilcontroller::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [perfilcontroller::class, 'updatePassword'])->name('perfil.password');
    // Configuración (Preferencias del sistema)
    Route::get('/configuracion', [configuracioncontroller::class, 'index'])->name('configuracion.index');

    // Notificaciones (Historial de alertas)
    Route::get('/notificaciones', [notificacionescontroller::class, 'index'])->name('notificaciones.index');
    
});