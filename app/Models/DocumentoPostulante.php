<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoPostulante extends Model
{
    protected $table = 'documento_postulante';

    protected $fillable = [
        'id_admision', 'tipo_documento', 'ruta_archivo',
        'estado_verificacion', 'observacion', 'fecha_entrega',
    ];

    public function admision()
    {
        return $this->belongsTo(Admision::class, 'id_admision');
    }
}
