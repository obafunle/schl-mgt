<x-mail::message>
# ❌ Exeat Request Rejected

Dear **{{ $exeat->parent->first_name }}**,

We regret to inform you that your exeat request for **{{ $exeat->student->full_name }}** has been **rejected**.

## Request Details

| Field | Details |
|-------|---------|
| **Exeat Number** | {{ $exeat->exeat_number }} |
| **Student** | {{ $exeat->student->full_name }} |
| **Departure** | {{ $exeat->departure_date->format('M d, Y') }} |
| **Return** | {{ $exeat->return_date->format('M d, Y') }} |
| **Duration** | {{ $exeat->getDaysDifference() }} day(s) |
| **Reason** | {{ $exeat->reason }} |

## Rejection Reason

{{ $exeat->rejection_reason ?? 'No specific reason provided.' }}

## What You Can Do

1. 📞 **Contact the school** to discuss the decision
2. ✏️ **Submit a new request** with additional information
3. 📋 **Check the guidelines** for exeat requests

<x-mail::button :url="$detailsUrl" color="danger">
View Request Details
</x-mail::button>

---

Need help? Contact us at [{{ $supportEmail }}](mailto:{{ $supportEmail }})

Thanks,<br>
The {{ $schoolName }} Team
</x-mail::message>