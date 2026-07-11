<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Componente extends Model
{
    protected $table = 'componentes';

    /**
     * Unidad de medida asociada (solo para sensores).
     */
    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_id');
    }

    /**
     * Relación pivot con esclavos del catálogo.
     */
    public function detalleEsclavoComponentes(): HasMany
    {
        return $this->hasMany(DetalleEsclavoComponente::class, 'componente_id');
    }

    /**
     * Esclavos del catálogo que tienen este componente vinculado.
     */
    public function esclavos(): BelongsToMany
    {
        return $this->belongsToMany(
            EsclavosCatalogo::class,
            'detalle_esclavo_componentes',
            'componente_id',
            'esclavo_id'
        );
    }

    /**
     * Lecturas históricas de telemetría (MySQL - buffer/auditoría).
     */
    public function lecturas(): HasMany
    {
        return $this->hasMany(Lectura::class, 'componente_id');
    }
}
