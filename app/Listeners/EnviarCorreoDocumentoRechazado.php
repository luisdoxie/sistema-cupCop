<?php

namespace App\Listeners;

use App\Events\DocumentoRechazadoEvent;
use App\Mail\DocumentoRechazadoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoDocumentoRechazado implements ShouldQueue
{
    public function handle(DocumentoRechazadoEvent $event): void
    {
        $correo = $event->documento->admision->estudiante->persona->correo ?? null;

        if (!$correo) {
            return;
        }

        Mail::to($correo)->queue(new DocumentoRechazadoMail($event->documento, $event->observacion));
    }
}
