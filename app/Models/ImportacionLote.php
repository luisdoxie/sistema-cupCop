<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportacionLote extends Model
{
    protected $table = 'importacion_lote';

    protected $fillable = [
        'id_admin', 'id_gestion', 'tipo_usuario', 'nombre_archivo',
        'ruta_archivo', 'total_registros', 'exitosos', 'fallidos',
        'errores', 'estado', 'fecha_subida', 'fecha_proceso',
    ];

    public function admin()
    {
        return $this->belongsTo(Persona::class, 'id_admin');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }
}
