<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admision extends Model
{
    protected $table = 'admision';

    protected $fillable = [
        'id_estudiante', 'id_gestion', 'id_grupo', 'id_carrera1',
        'id_carrera2', 'fecha', 'estado', 'promedio_final',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function carrera1()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera1');
    }

    public function carrera2()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera2');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoPostulante::class, 'id_admision');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_admision');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_admision');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_admision');
    }
}
