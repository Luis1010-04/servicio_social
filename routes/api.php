<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LecturaController;
use App\Http\Controllers\User\UserEsclavoController;

// Esta es la ruta que usará el ESP32
Route::post('/lecturas/registrar', [LecturaController::class, 'store']);
Route::get('/configurar-dispositivo/{serie}', [UserEsclavoController::class, 'getConfiguracion']);