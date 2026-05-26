<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigSistema extends Model
{
    protected $table = 'config_sistema';
    protected $primaryKey = 'clave';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['clave', 'valor', 'descripcion'];
}
