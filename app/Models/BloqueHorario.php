<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueHorario extends Model
{
    protected $table = 'bloque_horario';

    protected $fillable = ['id_asignacion', 'dia', 'hora_inicio', 'hora_fin'];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionAcademica::class, 'id_asignacion');
    }

    public function clasesProgramadas()
    {
        return $this->hasMany(ClaseProgramada::class, 'id_bloque');
    }
}
