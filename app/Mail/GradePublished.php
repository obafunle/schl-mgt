<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Term;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GradePublished extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Student $student;
    public Term $term;
    public string $gradesUrl;

    public function __construct(Student $student, Term $term)
    {
        $this->student = $student;
        $this->term = $term;
        $this->gradesUrl = route('parent.child.grades', $student->id);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Results Published - ' . $this->student->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.parent.grades-published',
            with: [
                'student' => $this->student,
                'term' => $this->term,
                'gradesUrl' => $this->gradesUrl,
                'schoolName' => config('app.name'),
                'schoolLogo' => config('app.school_logo'),
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            // Attach report card
            // \Illuminate\Mail\Mailables\Attachment::fromPath(storage_path('app/report-cards/report-' . $this->student->id . '-' . $this->term->id . '.pdf'))
            //     ->as('report-card-' . $this->student->admission_number . '.pdf')
            //     ->withMime('application/pdf'),
        ];
    }
}