@component('mail::message')
# Password Reset Request

Dear {{ $customer->name ?? 'Valued Customer' }},

We received a request to reset the password for your Customer Portal account associated with **{{ $customer->email }}**.

To complete the password reset process, please click the button below:

@component('mail::button', ['url' => $resetUrl, 'color' => 'primary'])
Reset My Password
@endcomponent

**Important Security Information:**
- This password reset link will expire in **60 minutes** for your security
- If you did not request this password reset, please ignore this email - no action is required
- For your security, never share this reset link with anyone

If you're having trouble with the button above, you can also copy and paste the following link into your web browser:

{{ $resetUrl }}

---

**Need assistance?** Our team is here to help with your insurance needs and account questions.

Best regards,<br>
**Parth Rawal**<br>
Insurance Advisor<br>
Professional Insurance Solutions

@slot('subcopy')
This is an automated security email from your Customer Portal. If you have any concerns about your account security, please contact our support team immediately.
@endslot
@endcomponent
