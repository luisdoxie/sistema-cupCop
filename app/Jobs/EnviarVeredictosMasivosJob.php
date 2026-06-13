<?php

namespace App\Jobs;

use App\Events\VeredictoAdmisionEvent;
use App\Models\Admision;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EnviarVeredictosMasivosJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public int $idGestion) {}

    public function handle(): void
    {
        Admision::where('id_gestion', $this->idGestion)
            ->whereIn('estado', ['admitido_carrera1', 'admitido_carrera2', 'reprobado', 'no_admitido'])
            ->with('estudiante.persona')
            ->each(function (Admision $admision) {
                event(new VeredictoAdmisionEvent($admision));
            });
    }
}
