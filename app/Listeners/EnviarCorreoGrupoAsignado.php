<?php

namespace App\Listeners;

use App\Events\GrupoAsignadoEvent;
use App\Mail\GrupoAsignadoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoGrupoAsignado implements ShouldQueue
{
    public function handle(GrupoAsignadoEvent $event): void
    {
        $admision = $event->admision->load('estudiante.persona', 'gestion', 'grupo');
        $correo   = $admision->estudiante->persona->correo ?? null;

        if (!$correo) {
            return;
        }

        Mail::to($correo)->queue(new GrupoAsignadoMail($admision));
    }
}
