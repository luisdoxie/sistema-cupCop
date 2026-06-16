<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocenteMateriaHabilitada extends Model
{
    protected $table = 'docente_materia_habilitada';

    protected $fillable = ['id_docente', 'id_materia', 'aprobado_por'];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(Persona::class, 'aprobado_por');
    }
}
