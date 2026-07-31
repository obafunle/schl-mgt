<x-mail::message>
# New Invoice Generated

Dear **{{ $invoice->student->parent_name }}**,

A new invoice has been generated for **{{ $invoice->student->full_name }}**.

## Invoice Summary

| Field | Details |
|-------|---------|
| **Invoice Number** | {{ $invoice->invoice_number }} |
| **Student** | {{ $invoice->student->full_name }} |
| **Class** | {{ $invoice->class->name }} @if($invoice->classArm)({{ $invoice->classArm->name }})@endif |
| **Term** | {{ $invoice->term->name }} |
| **Academic Year** | {{ $invoice->academicYear->name }} |
| **Issue Date** | {{ $invoice->issue_date->format('M d, Y') }} |
| **Due Date** | {{ $invoice->due_date->format('M d, Y') }} |

## Fee Breakdown

@foreach($invoice->items as $item)
- **{{ $item['name'] }}:** ₦{{ number_format($item['amount'], 2) }}
@endforeach

| | |
|---|---|
| **Subtotal:** | ₦{{ number_format($invoice->subtotal, 2) }} |
| @if($invoice->discount > 0)**Discount:** | -₦{{ number_format($invoice->discount, 2) }} |
@endif| **Total:** | ₦{{ number_format($invoice->total, 2) }} |
| **Amount Paid:** | ₦{{ number_format($invoice->amount_paid, 2) }} |
| **Balance Due:** | ₦{{ number_format($invoice->balance, 2) }} |

<x-mail::button :url="$payUrl" color="primary">
Pay Now
</x-mail::button>

---

*Please ensure payment is made by the due date to avoid penalties.*

Need help? Contact us at [{{ $supportEmail }}](mailto:{{ $supportEmail }})

Thanks,<br>
The {{ $schoolName }} Bursary
</x-mail::message>