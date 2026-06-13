<?php

namespace App\Mail;

use App\Models\Admision;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PagoConfirmadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Admision $admision) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pago Confirmado — Sistema CUP FICCT');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.pago_confirmado');
    }
}
