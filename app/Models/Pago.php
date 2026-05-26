<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';

    protected $fillable = [
        'id_admision', 'monto', 'tipo_pasarela',
        'referencia_transaccion', 'estado_pago', 'fecha_pago',
    ];

    public function admision()
    {
        return $this->belongsTo(Admision::class, 'id_admision');
    }
}
