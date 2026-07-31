<x-mail::message>
# Welcome to {{ $schoolName }} Parent Portal!

Dear **{{ $parent->first_name }}**,

We're excited to welcome you to the {{ $schoolName }} Parent Portal. This platform gives you real-time access to your children's academic progress, fees, attendance, and more.

## Your Children

@foreach($children as $child)
- **{{ $child->full_name }}** ({{ $child->admission_number }})
  - Class: {{ $child->class->name ?? 'Not Assigned' }}
  @if($child->classArm)
    - Arm: {{ $child->classArm->name }}
  @endif
@endforeach

## Quick Actions

<x-mail::button :url="$loginUrl" color="primary">
Login to Parent Portal
</x-mail::button>

## What You Can Do

- 📊 **View Results** - Check grades and performance
- 💰 **Manage Fees** - View invoices and make payments
- 📋 **Request Exeats** - Submit permission requests
- 📈 **Track Attendance** - Monitor attendance records
- 🔔 **Receive Notifications** - Get real-time updates

---

Need help? Contact us at [{{ $supportEmail }}](mailto:{{ $supportEmail }})

Thanks,<br>
The {{ $schoolName }} Team
</x-mail::message>