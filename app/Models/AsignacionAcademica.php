<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionAcademica extends Model
{
    protected $table = 'asignacion_academica';

    protected $fillable = [
        'id_docente', 'id_materia_grupo', 'carga_horaria',
        'fecha_asignacion', 'estado', 'observacion',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente');
    }

    public function materiaGrupo()
    {
        return $this->belongsTo(MateriaGrupo::class, 'id_materia_grupo');
    }

    public function bloquesHorario()
    {
        return $this->hasMany(BloqueHorario::class, 'id_asignacion');
    }

    public function clasesProgramadas()
    {
        return $this->hasMany(ClaseProgramada::class, 'id_asignacion');
    }
}
