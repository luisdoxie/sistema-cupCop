<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';

    protected $fillable = ['nombre', 'sigla'];

    public function materiaGrupos()
    {
        return $this->hasMany(MateriaGrupo::class, 'id_materia');
    }
}
