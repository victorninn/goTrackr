<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $demoName,
        public string $demoBusiness,
        public string $demoPhone,
        public string $demoEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[goTrackr] New Demo Request from ' . $this->demoBusiness,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo-request',
        );
    }
}
