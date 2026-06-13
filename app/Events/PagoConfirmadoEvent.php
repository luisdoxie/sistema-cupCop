<?php

namespace App\Events;

use App\Models\Admision;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PagoConfirmadoEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Admision $admision) {}
}
