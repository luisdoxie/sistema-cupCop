<?php

namespace App\Providers;

use App\Events\DocumentoRechazadoEvent;
use App\Events\GrupoAsignadoEvent;
use App\Events\PagoConfirmadoEvent;
use App\Events\VeredictoAdmisionEvent;
use App\Listeners\EnviarCorreoDocumentoRechazado;
use App\Listeners\EnviarCorreoGrupoAsignado;
use App\Listeners\EnviarCorreoPagoConfirmado;
use App\Listeners\EnviarCorreoVeredictoAdmision;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(DocumentoRechazadoEvent::class, EnviarCorreoDocumentoRechazado::class);
        Event::listen(PagoConfirmadoEvent::class,     EnviarCorreoPagoConfirmado::class);
        Event::listen(GrupoAsignadoEvent::class,      EnviarCorreoGrupoAsignado::class);
        Event::listen(VeredictoAdmisionEvent::class,  EnviarCorreoVeredictoAdmision::class);
    }
}
