<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';

    protected $fillable = [
        'id_gestion', 'nombre', 'paralelo', 'modalidad', 'cupo_maximo', 'estado',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function materiaGrupos()
    {
        return $this->hasMany(MateriaGrupo::class, 'id_grupo');
    }

    public function admisiones()
    {
        return $this->hasMany(Admision::class, 'id_grupo');
    }
}
