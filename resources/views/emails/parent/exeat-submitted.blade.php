<x-mail::message>
# Exeat Request Submitted

Dear **{{ $exeat->parent->first_name }}**,

Your exeat request for **{{ $exeat->student->full_name }}** has been submitted successfully and is pending approval.

## Request Details

| Field | Details |
|-------|---------|
| **Exeat Number** | {{ $exeat->exeat_number }} |
| **Student** | {{ $exeat->student->full_name }} ({{ $exeat->student->admission_number }}) |
| **Class** | {{ $exeat->student->class->name ?? 'N/A' }} |
| **Departure** | {{ $exeat->departure_date->format('l, M d, Y') }} @if($exeat->departure_time) at {{ $exeat->departure_time->format('h:i A') }} @endif |
| **Return** | {{ $exeat->return_date->format('l, M d, Y') }} @if($exeat->return_time) at {{ $exeat->return_time->format('h:i A') }} @endif |
| **Duration** | {{ $exeat->getDaysDifference() }} day(s) |
| **Reason** | {{ $exeat->reason }} |
| **Status** | ⏳ Pending |

## What Happens Next?

1. ✅ Your request has been submitted to the school administration
2. ⏳ They will review and respond within 24-48 hours
3. 📧 You'll receive an email notification once a decision is made
4. 📋 You can track the status anytime in the parent portal

<x-mail::button :url="$detailsUrl" color="primary">
View Request Details
</x-mail::button>

---

Need help? Contact us at [{{ $supportEmail }}](mailto:{{ $supportEmail }})

Thanks,<br>
The {{ $schoolName }} Team
</x-mail::message>