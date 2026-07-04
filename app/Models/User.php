<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellido',
        'usuario',
        'email',
        'password',
        'rol',
        'imagen_url',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Ubicaciones que pertenecen al usuario.
     */
    public function ubicaciones(): HasMany
    {
        return $this->hasMany(Ubicaciones::class, 'user_id');
    }

    /**
     * Instancias físicas de gateways/maestros del usuario.
     */
    public function maestrosUsuarios(): HasMany
    {
        return $this->hasMany(MaestroUsuario::class, 'user_id');
    }
}
