<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piso extends Model
{
    protected $table = 'piso';

    protected $fillable = ['numero'];

    public function aulas()
    {
        return $this->hasMany(Aula::class, 'id_piso');
    }
}
