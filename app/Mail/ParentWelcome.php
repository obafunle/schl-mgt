<?php

namespace App\Mail;

use App\Models\Parent as ParentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParentWelcome extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ParentModel $parent;
    public string $loginUrl;

    public function __construct(ParentModel $parent)
    {
        $this->parent = $parent;
        $this->loginUrl = route('parent.login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name') . ' Parent Portal',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.parent.welcome',
            with: [
                'parent' => $this->parent,
                'loginUrl' => $this->loginUrl,
                'schoolName' => config('app.name'),
                'schoolLogo' => config('app.school_logo'),
                'children' => $this->parent->children,
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}