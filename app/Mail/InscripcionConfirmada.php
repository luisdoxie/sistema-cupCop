<?php

namespace App\Mail;

use App\Models\Admision;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InscripcionConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Admision $admision
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Inscripción - CUP #' . $this->admision->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inscripcion-confirmada',
            with: [
                'admision' => $this->admision,
                'nombre'   => $this->admision->estudiante->persona->nombre
                              ?? ($this->admision->estudiante->id_persona ?? 'Estudiante'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
