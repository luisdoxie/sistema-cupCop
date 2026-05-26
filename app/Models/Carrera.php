<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';

    protected $fillable = ['nombre', 'sigla', 'plan'];

    public function carreraGestiones()
    {
        return $this->hasMany(CarreraGestion::class, 'id_carrera');
    }

    public function admisionesCarrera1()
    {
        return $this->hasMany(Admision::class, 'id_carrera1');
    }

    public function admisionesCarrera2()
    {
        return $this->hasMany(Admision::class, 'id_carrera2');
    }
}
