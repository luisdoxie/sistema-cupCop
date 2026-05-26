<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docente';

    protected $fillable = [
        'id_persona', 'especialidad', 'grado_academico',
        'diplomado_educacion', 'anios_experiencia', 'max_grupos', 'estado',
    ];

    protected function casts(): array
    {
        return ['diplomado_educacion' => 'boolean'];
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionAcademica::class, 'id_docente');
    }
}
