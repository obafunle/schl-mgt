<x-mail::message>
# ✅ Payment Confirmation

Dear **{{ $invoice->student->parent_name }}**,

We have received your payment of **₦{{ number_format($payment->amount, 2) }}** for **{{ $invoice->student->full_name }}**.

## Payment Details

| Field | Details |
|-------|---------|
| **Transaction Reference** | {{ $payment->reference }} |
| **Payment Method** | {{ ucfirst($payment->payment_method) }} |
| **Amount Paid** | ₦{{ number_format($payment->amount, 2) }} |
| **Payment Date** | {{ $payment->payment_date->format('M d, Y h:i A') }} |
| **Invoice Number** | {{ $invoice->invoice_number }} |

## Updated Invoice Status

| | |
|---|---|
| **Total Invoice:** | ₦{{ number_format($invoice->total, 2) }} |
| **Amount Paid:** | ₦{{ number_format($invoice->amount_paid, 2) }} |
| **Balance:** | ₦{{ number_format($invoice->balance, 2) }} |

@if($invoice->balance > 0)
⚠️ **Reminder:** There is still a balance of ₦{{ number_format($invoice->balance, 2) }} remaining.
@else
✅ **Invoice fully paid!** Thank you for your prompt payment.
@endif

<x-mail::button :url="$receiptUrl" color="success">
View Receipt
</x-mail::button>

---

Need help? Contact us at [{{ $supportEmail }}](mailto:{{ $supportEmail }})

Thanks,<br>
The {{ $schoolName }} Bursary
</x-mail::message>