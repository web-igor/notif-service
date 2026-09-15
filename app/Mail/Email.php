<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class Email extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        private readonly string $senderName,
        private readonly string $text
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->senderName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.email',
            with: [
                'senderName' => $this->senderName,
                'text'       => $this->text,
            ]
        );
    }
}
