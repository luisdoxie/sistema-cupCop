<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencia';

    protected $fillable = ['id_clase', 'id_admision', 'estado', 'fecha', 'observacion'];

    public function clase()
    {
        return $this->belongsTo(ClaseProgramada::class, 'id_clase');
    }

    public function admision()
    {
        return $this->belongsTo(Admision::class, 'id_admision');
    }
}
