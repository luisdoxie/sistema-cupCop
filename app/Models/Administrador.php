<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administrador';

    protected $fillable = ['id_persona', 'cargo', 'estado'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
