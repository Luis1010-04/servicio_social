<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsclavosCatalogo extends Model
{
    protected $table = 'esclavos_catalogo';

    public function maestroEsclavos()
    {
        // Un registro del catálogo puede estar en muchos maestros_esclavos
        return $this->hasMany(MaestroEsclavo::class, 'esclavo_id');
    }
    public function componentes()
    {
        return $this->belongsToMany(Componente::class, 'detalle_esclavo_componentes', 'esclavo_id', 'componente_id');
    }
}