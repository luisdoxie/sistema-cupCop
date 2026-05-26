<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraGestion extends Model
{
    protected $table = 'carrera_gestion';

    protected $fillable = ['id_carrera', 'id_gestion', 'cupo_maximo', 'cupo_disponible'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }
}
