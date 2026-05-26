<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiante';

    protected $fillable = [
        'id_persona', 'fecha_nacimiento', 'colegio_procedencia',
        'ciudad', 'titulo_bachiller', 'otros_documentos', 'estado',
    ];

    protected function casts(): array
    {
        return ['titulo_bachiller' => 'boolean'];
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function admisiones()
    {
        return $this->hasMany(Admision::class, 'id_estudiante');
    }
}
