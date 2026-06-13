<?php

namespace App\Listeners;

use App\Events\VeredictoAdmisionEvent;
use App\Mail\VeredictoAdmisionMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoVeredictoAdmision implements ShouldQueue
{
    public function handle(VeredictoAdmisionEvent $event): void
    {
        $admision = $event->admision->load('estudiante.persona', 'gestion', 'carrera1', 'carrera2');
        $correo   = $admision->estudiante->persona->correo ?? null;

        if (!$correo) {
            return;
        }

        Mail::to($correo)->queue(new VeredictoAdmisionMail($admision));
    }
}
