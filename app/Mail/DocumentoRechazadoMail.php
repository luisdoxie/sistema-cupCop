<?php

namespace App\Mail;

use App\Models\DocumentoPostulante;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentoRechazadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DocumentoPostulante $documento,
        public string $observacion,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Documento Rechazado — Sistema CUP FICCT');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.documento_rechazado');
    }
}
