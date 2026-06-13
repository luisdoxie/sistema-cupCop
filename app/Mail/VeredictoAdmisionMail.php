<?php

namespace App\Mail;

use App\Models\Admision;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VeredictoAdmisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Admision $admision) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Resultado de Admisión — Sistema CUP FICCT');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.veredicto_admision');
    }
}
