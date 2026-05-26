<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriaGrupo extends Model
{
    protected $table = 'materia_grupo';

    protected $fillable = ['id_grupo', 'id_materia', 'id_turno', 'estado'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionAcademica::class, 'id_materia_grupo');
    }

    public function examenes()
    {
        return $this->hasMany(Examen::class, 'id_materia_grupo');
    }
}
