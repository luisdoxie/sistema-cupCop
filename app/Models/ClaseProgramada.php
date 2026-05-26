<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaseProgramada extends Model
{
    protected $table = 'clase_programada';

    protected $fillable = ['id_asignacion', 'id_aula', 'id_bloque', 'fecha', 'estado'];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionAcademica::class, 'id_asignacion');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula');
    }

    public function bloque()
    {
        return $this->belongsTo(BloqueHorario::class, 'id_bloque');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_clase');
    }
}
