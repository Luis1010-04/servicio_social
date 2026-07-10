<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaestroEsclavo extends Model
{
    protected $table = 'maestros_esclavos';

    public function maestroUsuario()
    {
        return $this->belongsTo(MaestroUsuario::class, 'maestro_id');
    }

    public function esclavoCatalogo()
    {
        return $this->belongsTo(EsclavosCatalogo::class, 'esclavo_id');
    }
}