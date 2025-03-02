<x-mail::message>
## Hi {{ $user->name }},

<br>
<br>
<p style='text-align: center; font-size:16px;'>Your 6-digit Verification Code is:</p>

<h3 style='text-align: center; font-size:20px; font-weight:blod;'><strong>{{ $otp->code }}</strong><h3>
<br>
<br>
<p>{{ $content }}</p>

<p><strong>Don't</strong> share this code with anyone! Wallet Wise representatives will never reach out to you to verify this code over the phone or SMS.</p>

**This code is valid for 10 minutes**

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
