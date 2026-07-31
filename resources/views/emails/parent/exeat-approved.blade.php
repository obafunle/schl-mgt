<x-mail::message>
# ✅ Exeat Request Approved!

Dear **{{ $exeat->parent->first_name }}**,

Great news! Your exeat request for **{{ $exeat->student->full_name }}** has been **approved**.

## Approved Exeat Details

| Field | Details |
|-------|---------|
| **Exeat Number** | {{ $exeat->exeat_number }} |
| **Student** | {{ $exeat->student->full_name }} |
| **Departure** | {{ $exeat->departure_date->format('l, M d, Y') }} @if($exeat->departure_time) at {{ $exeat->departure_time->format('h:i A') }} @endif |
| **Return** | {{ $exeat->return_date->format('l, M d, Y') }} @if($exeat->return_time) at {{ $exeat->return_time->format('h:i A') }} @endif |
| **Duration** | {{ $exeat->getDaysDifference() }} day(s) |
| **Approved By** | {{ $exeat->approvedBy->name ?? 'School Administrator' }} |
| **Approved On** | {{ $exeat->approved_at->format('M d, Y h:i A') }} |

## Important Information

1. 📄 **Print this approval** - Show at the school gate
2. 📅 **Return on time** - {{ $exeat->return_date->format('l, M d, Y') }}
3. 📞 **Contact school** if there are any changes

<x-mail::button :url="$detailsUrl" color="success">
View & Download Approval
</x-mail::button>

---

Need help? Contact us at [{{ $supportEmail }}](mailto:{{ $supportEmail }})

Thanks,<br>
The {{ $schoolName }} Team
</x-mail::message>