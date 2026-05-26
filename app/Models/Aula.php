<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aula';

    protected $fillable = [
        'id_piso', 'numero', 'capacidad', 'tipo', 'modalidad', 'estado',
    ];

    public function piso()
    {
        return $this->belongsTo(Piso::class, 'id_piso');
    }

    public function clasesProgramadas()
    {
        return $this->hasMany(ClaseProgramada::class, 'id_aula');
    }
}
