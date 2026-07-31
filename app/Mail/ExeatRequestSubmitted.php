<?php

namespace App\Mail;

use App\Models\ExeatRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExeatRequestSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ExeatRequest $exeat;
    public string $detailsUrl;

    public function __construct(ExeatRequest $exeat)
    {
        $this->exeat = $exeat;
        $this->detailsUrl = route('parent.exeats.details', $exeat);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exeat Request Submitted - ' . $this->exeat->student->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.parent.exeat-submitted',
            with: [
                'exeat' => $this->exeat,
                'detailsUrl' => $this->detailsUrl,
                'schoolName' => config('app.name'),
                'schoolLogo' => config('app.school_logo'),
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}