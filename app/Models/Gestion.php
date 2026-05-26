<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table = 'gestion';

    protected $fillable = [
        'nombre', 'anio', 'semestre', 'fecha_inicio', 'fecha_fin', 'estado',
    ];

    public function carreraGestiones()
    {
        return $this->hasMany(CarreraGestion::class, 'id_gestion');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_gestion');
    }

    public function admisiones()
    {
        return $this->hasMany(Admision::class, 'id_gestion');
    }

    public function importaciones()
    {
        return $this->hasMany(ImportacionLote::class, 'id_gestion');
    }
}
