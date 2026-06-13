<?php

namespace App\Listeners;

use App\Events\PagoConfirmadoEvent;
use App\Mail\PagoConfirmadoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoPagoConfirmado implements ShouldQueue
{
    public function handle(PagoConfirmadoEvent $event): void
    {
        $admision = $event->admision->load('estudiante.persona', 'gestion', 'carrera1', 'carrera2');
        $correo   = $admision->estudiante->persona->correo ?? null;

        if (!$correo) {
            return;
        }

        Mail::to($correo)->queue(new PagoConfirmadoMail($admision));
    }
}
