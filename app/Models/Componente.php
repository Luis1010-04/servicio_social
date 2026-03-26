<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $table = 'componentes';

    // ESTO ES LO QUE FALTA Y CAUSA EL ERROR
    public function detalleEsclavoComponentes()
    {
        return $this->hasMany(DetalleEsclavoComponente::class, 'componente_id');
    }
    public function lecturas()
{
    return $this->hasMany(Lectura::class, 'componente_id');
}
}