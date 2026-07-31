<x-mail::message>
# {{ $subject }}

Dear **{{ $parent->first_name }}**,

{!! nl2br(e($message)) !!}

<x-mail::button :url="route('parent.dashboard')" color="primary">
Go to Dashboard
</x-mail::button>

---

Need help? Contact us at [{{ config('mail.from.address') }}](mailto:{{ config('mail.from.address') }})

Thanks,<br>
The {{ $schoolName }} Team
</x-mail::message>