@component('mail::message')
# Reset Your Password

Hello "{{ $user->full_name }}",

You have requested to reset your password. Please use the following code:

**Reset Code: {{ $code }}**

User Information:
- **Name:** "{{ $user->full_name }}"
- **Email:** {{ $user->email }}
- **Operating System:** {{ $operating_system }}
- **Browser Info:** {{ $browser }}
- **IP Info:** {{ $ip }}
- **Time:** {{ $time }}

If you did not request a password reset, please ignore this email.

Thank you,
{{ config('app.name') }}
@endcomponent