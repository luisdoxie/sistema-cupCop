<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Persona extends Authenticatable
{
    use Notifiable;

    protected $table = 'persona';

    protected $fillable = [
        'ci', 'nombre', 'apellido', 'sexo', 'telefono',
        'direccion', 'correo', 'password', 'rol', 'activo',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo'   => 'boolean',
        ];
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'id_persona');
    }

    public function docente()
    {
        return $this->hasOne(Docente::class, 'id_persona');
    }

    public function coordinador()
    {
        return $this->hasOne(Coordinador::class, 'id_persona');
    }

    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'id_persona');
    }

    public function importaciones()
    {
        return $this->hasMany(ImportacionLote::class, 'id_admin');
    }
}
