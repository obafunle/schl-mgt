<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Payment $payment;
    public Invoice $invoice;
    public string $receiptUrl;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->invoice = $payment->invoice;
        $this->receiptUrl = route('parent.fees', $payment->student_id);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Payment Confirmation - ₦' . number_format($this->payment->amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.parent.payment-confirmation',
            with: [
                'payment' => $this->payment,
                'invoice' => $this->invoice,
                'receiptUrl' => $this->receiptUrl,
                'schoolName' => config('app.name'),
                'schoolLogo' => config('app.school_logo'),
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            // Attach payment receipt
            // \Illuminate\Mail\Mailables\Attachment::fromPath(storage_path('app/receipts/receipt-' . $this->payment->reference . '.pdf'))
            //     ->as('receipt-' . $this->payment->reference . '.pdf')
            //     ->withMime('application/pdf'),
        ];
    }
}