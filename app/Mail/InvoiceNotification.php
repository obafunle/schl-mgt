<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $payUrl;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->payUrl = route('parent.fees', $invoice->student_id);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '💰 New Invoice - ' . $this->invoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.parent.invoice',
            with: [
                'invoice' => $this->invoice,
                'payUrl' => $this->payUrl,
                'schoolName' => config('app.name'),
                'schoolLogo' => config('app.school_logo'),
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            // Attach PDF invoice
            // \Illuminate\Mail\Mailables\Attachment::fromPath(storage_path('app/invoices/invoice-' . $this->invoice->invoice_number . '.pdf'))
            //     ->as('invoice-' . $this->invoice->invoice_number . '.pdf')
            //     ->withMime('application/pdf'),
        ];
    }
}