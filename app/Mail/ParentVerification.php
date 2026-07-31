<?php

namespace App\Mail;

use App\Models\Parent as ParentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParentVerification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ParentModel $parent;
    public string $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(ParentModel $parent)
    {
        $this->parent = $parent;
        $this->verificationUrl = route('parent.verify', $parent->verification_code);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Parent Account - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.parent.verification',
            with: [
                'parent' => $this->parent,
                'verificationUrl' => $this->verificationUrl,
                'schoolName' => config('app.name'),
                'schoolLogo' => config('app.school_logo'),
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}