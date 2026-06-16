<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulacionDocente extends Model
{
    protected $table = 'postulacion_docente';

    protected $fillable = [
        'ci', 'nombre', 'apellido', 'sexo', 'correo', 'telefono', 'direccion',
        'grado_academico', 'anios_experiencia', 'diplomado_educacion',
        'cv_path', 'estado', 'observacion', 'aprobado_por', 'aprobado_en',
    ];

    protected function casts(): array
    {
        return [
            'diplomado_educacion' => 'boolean',
            'aprobado_en'         => 'datetime',
        ];
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'postulacion_materia', 'id_postulacion', 'id_materia');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(Persona::class, 'aprobado_por');
    }
}
