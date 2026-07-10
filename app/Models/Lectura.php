<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lectura extends Model
{
    protected $table = 'lecturas';

    // Esto permite que el controlador guarde los datos
    protected $fillable = ['componente_id', 'valor'];
    
    // Si tu tabla no tiene las columnas created_at/updated_at, añade esto:
    // public $timestamps = false; 
}