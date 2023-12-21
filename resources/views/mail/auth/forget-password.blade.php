@component('mail::message')
# Reset Your Password

Hello,

You have requested to reset your password. Please use the following code:

**Reset Code: {{ $code }}**

If you did not request a password reset, please ignore this email.

Thank you,
{{ config('app.name') }}
@endcomponent