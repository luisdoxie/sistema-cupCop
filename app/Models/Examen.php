<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    protected $table = 'examen';

    protected $fillable = ['id_materia_grupo', 'tipo', 'puntaje_maximo', 'fecha', 'estado'];

    public function materiaGrupo()
    {
        return $this->belongsTo(MateriaGrupo::class, 'id_materia_grupo');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_examen');
    }
}
