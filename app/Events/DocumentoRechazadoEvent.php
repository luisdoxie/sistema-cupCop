<?php

namespace App\Events;

use App\Models\DocumentoPostulante;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentoRechazadoEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public DocumentoPostulante $documento,
        public string $observacion,
    ) {}
}
