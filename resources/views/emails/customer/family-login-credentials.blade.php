@component('mail::message')
# {{ $isHead ? 'Welcome to Your Family Group Portal!' : "You've Been Added to a Family Group!" }}

Dear {{ $customer->name }},

@if($isHead)
**Congratulations!** You have been designated as the **Family Head** for the family group "**{{ $familyGroup->name }}**" in the Parth Rawal Insurance Advisory Customer Portal.

As the family head, you have special privileges to:
- Manage all family members' accounts
- View all family insurance policies
- Change family members' passwords without their current password
- Access comprehensive family reports and documents
@else
**Welcome to the family!** You have been added to the family group "**{{ $familyGroup->name }}**" in the Parth Rawal Insurance Advisory Customer Portal.

Your family head is **{{ $familyGroup->familyHead->name }}**, who can help you with any questions about your account or insurance policies.
@endif

## Your Login Credentials

**Login URL:** [{{ $loginUrl }}]({{ $loginUrl }})

**Email Address:** {{ $customer->email }}  
**Temporary Password:** `{{ $password }}`

@component('mail::button', ['url' => $loginUrl, 'color' => 'primary'])
Login to Customer Portal
@endcomponent

## Important Next Steps

**1. Verify Your Email Address**
Your email address needs to be verified for security. Click the link below to verify:

@component('mail::button', ['url' => $verificationUrl, 'color' => 'success'])
Verify Email Address
@endcomponent

**2. Change Your Password**
For security, please change your temporary password immediately after logging in. Go to your profile page and select "Change Password."

**3. Complete Your Profile**
Make sure your profile information is complete and up-to-date for the best insurance service experience.

## Family Group Information

- **Family Name:** {{ $familyGroup->name }}
- **Family Head:** {{ $familyGroup->familyHead->name }}
@if($isHead)
- **Your Role:** Family Head
- **Members Count:** {{ $familyGroup->familyMembers->count() }} (including you)
@else
- **Your Role:** Family Member
- **Total Members:** {{ $familyGroup->familyMembers->count() }}
@endif

## What You Can Access

✅ **View Your Insurance Policies** - See all your current policies and coverage details  
✅ **Track Policy Renewals** - Stay updated on renewal dates and payment schedules  
✅ **Download Documents** - Access your insurance certificates and policy documents  
✅ **Contact Support** - Direct access to our customer support team  
✅ **Family Coordination** - {{ $isHead ? "Manage your family members' accounts" : "Coordinate with your family head for account matters" }}

---

## Security Notice

🔒 **Keep Your Credentials Safe**
- Never share your login credentials with anyone
- Always log out when using shared computers
- Contact us immediately if you suspect unauthorized access

🔔 **Account Activity**
All login attempts and account activities are logged for your security. You can view your account activity in the portal.

**Need Help?**
If you have any questions about your account or need assistance with logging in, please contact our support team.

Best regards,<br>
**Parth Rawal**<br>
Insurance Advisor<br>
Professional Insurance Solutions

@slot('subcopy')
**Security Reminder:** This email contains sensitive login information. Please ensure you're viewing this email in a secure environment and delete it after you've successfully logged in and changed your password.

If you did not expect to receive this email or believe it was sent in error, please contact our support team immediately.
@endslot
@endcomponent
