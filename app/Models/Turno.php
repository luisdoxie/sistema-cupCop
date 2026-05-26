<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turno';

    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public function materiaGrupos()
    {
        return $this->hasMany(MateriaGrupo::class, 'id_turno');
    }
}
