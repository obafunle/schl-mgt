<?php

namespace App\Mail;

use App\Models\Parent as ParentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $subject;
    public string $message;
    public ParentModel $parent;

    public function __construct(string $subject, string $message, ParentModel $parent)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->parent = $parent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.parent.generic',
            with: [
                'parent' => $this->parent,
                'message' => $this->message,
                'schoolName' => config('app.name'),
                'schoolLogo' => config('app.school_logo'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}