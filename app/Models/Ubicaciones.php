<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicaciones extends Model
{
    // Especificamos el nombre exacto de la tabla en tu base de datos
    protected $table = 'ubicaciones';

    // Si la tabla no tiene los campos created_at y updated_at, pon esto en false
    public $timestamps = true; 

    public function maestros()
    {
        return $this->hasMany(MaestroUsuario::class, 'ubicacion_id');
    }
}
