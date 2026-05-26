<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'nota';

    protected $fillable = ['id_examen', 'id_admision', 'calificacion', 'estado'];

    public function examen()
    {
        return $this->belongsTo(Examen::class, 'id_examen');
    }

    public function admision()
    {
        return $this->belongsTo(Admision::class, 'id_admision');
    }
}
