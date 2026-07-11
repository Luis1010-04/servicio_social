<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaestroUsuario extends Model
{
    protected $table = 'maestros_usuarios';

    public function ubicacion()
    {
        // Debe apuntar al Modelo Ubicaciones, no al controlador
        return $this->belongsTo(Ubicaciones::class, 'ubicacion_id');
    }

    public function maestroEsclavos()
    {
        return $this->hasMany(MaestroEsclavo::class, 'maestro_id');
    }
}