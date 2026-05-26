<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinador extends Model
{
    protected $table = 'coordinador';

    protected $fillable = ['id_persona', 'area', 'estado'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
