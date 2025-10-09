# Email Integration - Workflow Diagrams

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     NOTIFICATION SYSTEM                          │
│                   (WhatsApp + Email Channels)                   │
└─────────────────────────────────────────────────────────────────┘
                              │
         ┌────────────────────┼────────────────────┐
         │                    │                    │
    ┌────▼─────┐        ┌────▼─────┐        ┌────▼─────┐
    │  Event   │        │ Service  │        │ Command  │
    │ Triggers │        │  Calls   │        │  Cron    │
    └────┬─────┘        └────┬─────┘        └────┬─────┘
         │                   │                    │
         └────────────────┬──┴────────────────────┘
                          │
                    ┌─────▼─────┐
                    │  Listener │
                    │  (Queue)  │
                    └─────┬─────┘
                          │
         ┌────────────────┼────────────────┐
         │                │                │
    ┌────▼─────┐    ┌────▼─────┐    ┌────▼─────┐
    │ WhatsApp │    │  Email   │    │   SMS    │
    │ Service  │    │ Service  │    │ Service  │
    └────┬─────┘    └────┬─────┘    └────┬─────┘
         │               │                │
    ┌────▼─────┐    ┌────▼─────┐    ┌────▼─────┐
    │BotMaster │    │  SMTP    │    │  SMS     │
    │   API    │    │ Server   │    │ Gateway  │
    └──────────┘    └──────────┘    └──────────┘
```

---

## Customer Welcome Email Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    CUSTOMER REGISTRATION                         │
└─────────────────────────────────────────────────────────────────┘
                              │
                     CustomerController
                              │
                    ┌─────────▼─────────┐
                    │  CustomerService  │
                    │   ->create()      │
                    └─────────┬─────────┘
                              │
                    Fire: CustomerRegistered
                              │
              ┌───────────────┼───────────────┐
              │               │               │
         Database        Event Queue      Return to
          Updated         Added           Controller
              │               │               │
              │     ┌─────────▼─────────┐     │
              │     │ SendOnboarding    │     │
              │     │    Listener       │     │
              │     │   (ShouldQueue)   │     │
              │     └─────────┬─────────┘     │
              │               │               │
              │      shouldQueue() check      │
              │     ┌─────────▼─────────┐     │
              │     │ Has mobile/email? │     │
              │     │ Notifications on? │     │
              │     └─────────┬─────────┘     │
              │               │               │
              │         Queue Job             │
              │               │               │
              │     ┌─────────▼─────────┐     │
              │     │   Queue Worker    │     │
              │     │   Processes Job   │     │
              │     └─────────┬─────────┘     │
              │               │               │
              │       handle() executes       │
              │               │               │
              │     ┌─────────┴─────────┐     │
              │     │                   │     │
      ┌───────▼─────▼──┐      ┌────────▼────────────┐
      │sendWhatsApp    │      │sendEmailNotification│
      │Notification()  │      │        ()           │
      └───────┬────────┘      └────────┬────────────┘
              │                        │
      ┌───────▼────────┐      ┌────────▼────────────┐
      │ Check mobile   │      │  Check email        │
      │ Check enabled  │      │  Check enabled      │
      └───────┬────────┘      └────────┬────────────┘
              │                        │
      ┌───────▼────────┐      ┌────────▼────────────┐
      │CustomerService │      │ CustomerService     │
      │->sendOnboarding│      │->sendOnboarding     │
      │   Message()    │      │    Email()          │
      └───────┬────────┘      └────────┬────────────┘
              │                        │
      ┌───────▼────────┐      ┌────────▼────────────┐
      │Template        │      │  EmailService       │
      │Service         │      │->sendFromCustomer() │
      └───────┬────────┘      └────────┬────────────┘
              │                        │
      ┌───────▼────────┐      ┌────────▼────────────┐
      │Render template │      │ Render template     │
      │'customer_      │      │ 'customer_welcome'  │
      │  welcome'      │      │ (email channel)     │
      │(whatsapp)      │      └────────┬────────────┘
      └───────┬────────┘               │
              │                ┌───────▼────────┐
              │                │Format HTML     │
              │                │content         │
              │                └───────┬────────┘
      ┌───────▼────────┐               │
      │Send via        │      ┌────────▼────────────┐
      │BotMasterSender │      │TemplatedNotification│
      │   API          │      │    (Mailable)       │
      └───────┬────────┘      └────────┬────────────┘
              │                        │
      ┌───────▼────────┐      ┌────────▼────────────┐
      │  Log success   │      │  Laravel Mail       │
      │  or failure    │      │  Queue Job          │
      └────────────────┘      └────────┬────────────┘
                                       │
                              ┌────────▼────────────┐
                              │  SMTP Server        │
                              │  Sends Email        │
                              └────────┬────────────┘
                                       │
                              ┌────────▼────────────┐
                              │  Customer Inbox     │
                              │  Email Received     │
                              └─────────────────────┘
```

---

## Policy Document Email Flow

```
┌─────────────────────────────────────────────────────────────────┐
│              POLICY DOCUMENT UPLOAD & SEND                       │
└─────────────────────────────────────────────────────────────────┘
                              │
              CustomerInsuranceController
                              │
                    ┌─────────▼─────────┐
                    │CustomerInsurance  │
                    │   Service         │
                    │->sendDocument...()│
                    └─────────┬─────────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
    ┌─────────▼──────┐  ┌────▼─────┐  ┌──────▼─────┐
    │sendPolicyDoc   │  │sendPolicy│  │Send manual │
    │WhatsApp()      │  │DocEmail()│  │via UI      │
    └─────────┬──────┘  └────┬─────┘  └──────┬─────┘
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │  EmailService     │     │
              │    │->sendFromInsurance│     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │TemplateService    │     │
              │    │->renderFrom       │     │
              │    │  Insurance()      │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │NotificationContext│     │
              │    │->fromInsuranceId()│     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Load relationships:│     │
              │    │- customer         │     │
              │    │- insuranceCompany │     │
              │    │- policyType       │     │
              │    │- premiumType      │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │VariableResolver   │     │
              │    │->resolveTemplate()│     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Extract & replace  │     │
              │    │70+ variables      │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Format for email:  │     │
              │    │- Bold to <strong> │     │
              │    │- Links to <a>     │     │
              │    │- Newlines to <br> │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Get subject line   │     │
              │    │(policy_created)   │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Prepare attachments│     │
              │    │[policy_pdf_path]  │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Validate file      │     │
              │    │exists & readable  │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │TemplatedNotif     │     │
              │    │Mailable           │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Build envelope:    │     │
              │    │- from address     │     │
              │    │- from name        │     │
              │    │- reply-to         │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Render Blade view: │     │
              │    │templated-         │     │
              │    │notification.blade │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Attach PDF file    │     │
              │    │fromPath()         │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Mail::to()->send() │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Laravel Queue      │     │
              │    │(mail queue)       │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Queue Worker       │     │
              │    │processes email    │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │SMTP Connection    │     │
              │    │(Mailtrap/Gmail)   │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Email Delivered    │     │
              │    │with PDF attached  │     │
              │    └─────────┬─────────┘     │
              │              │               │
              │    ┌─────────▼─────────┐     │
              │    │Comprehensive      │     │
              │    │logging at each    │     │
              │    │step               │     │
              │    └───────────────────┘     │
              │                              │
              └──────────────────────────────┘
```

---

## Quotation Email Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                 QUOTATION GENERATION & SEND                      │
└─────────────────────────────────────────────────────────────────┘
                              │
                  QuotationController
                              │
              ┌───────────────▼───────────────┐
              │    QuotationService           │
              │    ->storeQuotation()         │
              └───────────────┬───────────────┘
                              │
                     Save to database
                              │
              ┌───────────────▼───────────────┐
              │  Fire: QuotationGenerated     │
              │         Event                 │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ SendQuotationWhatsApp         │
              │      Listener (Queue)         │
              └───────────────┬───────────────┘
                              │
              ┌───────────────┴───────────────┐
              │                               │
    ┌─────────▼──────────┐      ┌────────────▼────────┐
    │sendWhatsApp        │      │sendEmailNotification│
    │Notification()      │      │        ()           │
    └─────────┬──────────┘      └────────┬────────────┘
              │                          │
    ┌─────────▼──────────┐      ┌────────▼────────────┐
    │Check: mobile_number│      │Check: quotation.email│
    │      enabled       │      │  OR customer.email   │
    └─────────┬──────────┘      └────────┬────────────┘
              │                          │
    ┌─────────▼──────────┐      ┌────────▼────────────┐
    │QuotationService    │      │ QuotationService    │
    │->sendQuotationVia  │      │->sendQuotationVia   │
    │   WhatsApp()       │      │     Email()         │
    └─────────┬──────────┘      └────────┬────────────┘
              │                          │
    ┌─────────▼──────────┐      ┌────────▼────────────┐
    │Generate PDF        │      │ Generate PDF        │
    │comparison          │      │ comparison          │
    └─────────┬──────────┘      └────────┬────────────┘
              │                          │
    ┌─────────▼──────────┐      ┌────────▼────────────┐
    │Build WhatsApp msg: │      │ EmailService        │
    │- Vehicle details   │      │->sendFromQuotation()│
    │- Best premium      │      └────────┬────────────┘
    │- Comparison list   │               │
    │- Savings amount    │      ┌────────▼────────────┐
    └─────────┬──────────┘      │ Template rendering  │
              │                 │ (quotation_ready)   │
    ┌─────────▼──────────┐      └────────┬────────────┘
    │Send via WhatsApp   │               │
    │with PDF attachment │      ┌────────▼────────────┐
    └─────────┬──────────┘      │ Format email HTML   │
              │                 │ - Vehicle info      │
    ┌─────────▼──────────┐      │ - Premium table     │
    │Update quotation:   │      │ - Recommendation    │
    │status = 'Sent'     │      └────────┬────────────┘
    │sent_at = now()     │               │
    └─────────┬──────────┘      ┌────────▼────────────┐
              │                 │ Attach PDF file     │
    ┌─────────▼──────────┐      │ (comparison report) │
    │Delete temp PDF     │      └────────┬────────────┘
    └────────────────────┘               │
                                ┌────────▼────────────┐
                                │ Mail::to()->send()  │
                                └────────┬────────────┘
                                         │
                                ┌────────▼────────────┐
                                │ Update quotation:   │
                                │ status = 'Sent'     │
                                │ sent_at = now()     │
                                └────────┬────────────┘
                                         │
                                ┌────────▼────────────┐
                                │ Delete temp PDF     │
                                └────────┬────────────┘
                                         │
                                ┌────────▼────────────┐
                                │ Email delivered     │
                                │ with PDF attached   │
                                └─────────────────────┘
```

---

## Renewal Reminder Email Flow

```
┌─────────────────────────────────────────────────────────────────┐
│               RENEWAL REMINDER CRON JOB                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                     Laravel Scheduler
                   (Daily at configured time)
                              │
              ┌───────────────▼───────────────┐
              │SendRenewalReminders Command   │
              │  php artisan send:renewal-    │
              │       reminders               │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Get renewal_reminder_days     │
              │ from app_settings             │
              │ (default: 30,15,7,1)          │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Query insurances expiring on: │
              │ - Today + 30 days             │
              │ - Today + 15 days             │
              │ - Today + 7 days              │
              │ - Today + 1 day               │
              │ WHERE is_renewed = 0          │
              │   AND status = 1              │
              └───────────────┬───────────────┘
                              │
                      For each insurance
                              │
              ┌───────────────▼───────────────┐
              │ Calculate days until expiry   │
              │ Determine notification code:  │
              │ - 25-35 days: renewal_30_days │
              │ - 12-18 days: renewal_15_days │
              │ - 5-9 days: renewal_7_days    │
              │ - 0-2 days: renewal_expired   │
              └───────────────┬───────────────┘
                              │
              ┌───────────────┴───────────────┐
              │                               │
    ┌─────────▼──────────┐      ┌────────────▼────────┐
    │Send WhatsApp       │      │ Send Email          │
    │if mobile_number    │      │ if customer.email   │
    │   AND enabled      │      │    AND enabled      │
    └─────────┬──────────┘      └────────┬────────────┘
              │                          │
    ┌─────────▼──────────┐      ┌────────▼────────────┐
    │TemplateService     │      │ EmailService        │
    │->renderFrom        │      │->sendFromInsurance()│
    │  Insurance()       │      └────────┬────────────┘
    │('renewal_XX_days', │               │
    │ 'whatsapp')        │      ┌────────▼────────────┐
    └─────────┬──────────┘      │ Template rendering  │
              │                 │ (renewal_XX_days,   │
    ┌─────────▼──────────┐      │  email)             │
    │If no template:     │      └────────┬────────────┘
    │Fallback to:        │               │
    │renewalReminder()   │      ┌────────▼────────────┐
    │  OR                │      │ If no template:     │
    │renewalReminder     │      │ Use fallback        │
    │  Vehicle()         │      │ message             │
    └─────────┬──────────┘      └────────┬────────────┘
              │                          │
    ┌─────────▼──────────┐      ┌────────▼────────────┐
    │Send via WhatsApp   │      │ Build HTML email:   │
    │API                 │      │ - Policy details    │
    └─────────┬──────────┘      │ - Expiry date       │
              │                 │ - Urgency level     │
    ┌─────────▼──────────┐      │ - Contact info      │
    │Log result          │      └────────┬────────────┘
    │Increment sentCount │               │
    │  OR skippedCount   │      ┌────────▼────────────┐
    └────────────────────┘      │ Mail::to()->send()  │
                                └────────┬────────────┘
                                         │
                                ┌────────▼────────────┐
                                │ Log result          │
                                │ Increment sentCount │
                                │   OR skippedCount   │
                                └────────┬────────────┘
                                         │
                    ┌────────────────────┴────────────────────┐
                    │                                         │
          ┌─────────▼──────────┐              ┌──────────────▼──────────┐
          │ WhatsApp delivered │              │ Email delivered         │
          │ to customer mobile │              │ to customer inbox       │
          └────────────────────┘              └─────────────────────────┘

                              │
              ┌───────────────▼───────────────┐
              │ Command completion summary:   │
              │ - Total found: X              │
              │ - Sent: Y                     │
              │ - Skipped: Z                  │
              └───────────────────────────────┘
```

---

## Template Resolution Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                   TEMPLATE RENDERING PROCESS                     │
└─────────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ TemplateService               │
              │ ->render(typeCode, channel)   │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Find NotificationType         │
              │ WHERE code = typeCode         │
              │   AND is_active = true        │
              └───────────────┬───────────────┘
                              │
                      ┌───────┴───────┐
                      │ Found?        │
                      └───────┬───────┘
                          Yes │ No → Return null
                              │
              ┌───────────────▼───────────────┐
              │ Find NotificationTemplate     │
              │ WHERE notification_type_id    │
              │   AND channel = 'email'       │
              │   AND is_active = true        │
              └───────────────┬───────────────┘
                              │
                      ┌───────┴───────┐
                      │ Found?        │
                      └───────┬───────┘
                          Yes │ No → Return null
                              │       (will use fallback)
              ┌───────────────▼───────────────┐
              │ Extract template_content      │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ VariableResolverService       │
              │ ->resolveTemplate()           │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Extract {{variables}}         │
              │ from template content         │
              └───────────────┬───────────────┘
                              │
                      For each variable
                              │
              ┌───────────────▼───────────────┐
              │ Get variable metadata from    │
              │ VariableRegistryService       │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Resolve by source type:       │
              │ - customer.field              │
              │ - insurance.field             │
              │ - setting:category.key        │
              │ - computed:function           │
              │ - system:value                │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Navigate object relationships │
              │ (e.g., insurance.customer.    │
              │       insuranceCompany.name)  │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Format value based on type:   │
              │ - date → 'd-M-Y'              │
              │ - currency → '₹X,XXX'         │
              │ - percentage → 'X.X%'         │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Replace {{variable}} with     │
              │ resolved value                │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Return fully rendered         │
              │ template string               │
              └───────────────┬───────────────┘
                              │
                       Back to caller
                       (EmailService)
```

---

## Email Formatting Flow

```
┌─────────────────────────────────────────────────────────────────┐
│              EMAIL HTML FORMATTING PROCESS                       │
└─────────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ EmailService                  │
              │ ->formatEmailContent()        │
              └───────────────┬───────────────┘
                              │
            Input: Plain text with markdown
                              │
              ┌───────────────▼───────────────┐
              │ Convert bold text:            │
              │ *text* → <strong>text</strong>│
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Convert line breaks:          │
              │ \n → <br>                     │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Convert URLs to links:        │
              │ http://example.com →          │
              │ <a href="...">...</a>         │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Return formatted HTML         │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ TemplatedNotification         │
              │ Mailable                      │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Render Blade template:        │
              │ templated-notification.blade  │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Inject into template:         │
              │ - Gradient header             │
              │ - Company branding            │
              │ - HTML content                │
              │ - Professional footer         │
              │ - Contact info                │
              │ - Copyright notice            │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Apply responsive CSS:         │
              │ - Mobile breakpoints          │
              │ - Email-safe styles           │
              │ - Inline CSS                  │
              └───────────────┬───────────────┘
                              │
              ┌───────────────▼───────────────┐
              │ Final HTML email ready        │
              │ for delivery                  │
              └───────────────────────────────┘
```

---

## Error Handling Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    ERROR HANDLING FLOW                           │
└─────────────────────────────────────────────────────────────────┘
                              │
                  Email send attempted
                              │
                      ┌───────┴───────┐
                      │ Success?      │
                      └───────┬───────┘
                          Yes │ No
              ┌───────────────┼───────────────┐
              │               │               │
    ┌─────────▼──────┐   ┌───▼────────┐      │
    │ Log success    │   │ Catch      │      │
    │ INFO level     │   │ Exception  │      │
    └────────────────┘   └───┬────────┘      │
                             │               │
                     ┌───────▼──────┐        │
                     │ Log error    │        │
                     │ ERROR level  │        │
                     └───┬──────────┘        │
                         │                   │
                 ┌───────▼──────────┐        │
                 │ Include context: │        │
                 │ - customer_id    │        │
                 │ - email address  │        │
                 │ - error message  │        │
                 │ - stack trace    │        │
                 └───┬──────────────┘        │
                     │                       │
             ┌───────▼────────┐              │
             │ Queue job      │              │
             │ failed?        │              │
             └───────┬────────┘              │
                 Yes │ No                    │
     ┌───────────────┼───────────────┐       │
     │               │               │       │
┌────▼─────┐  ┌──────▼──────┐  ┌────▼──────┐
│Move to   │  │Return false │  │Continue   │
│failed_   │  │Don't re-    │  │with other │
│jobs table│  │throw error  │  │channels   │
└────┬─────┘  └──────┬──────┘  └────┬──────┘
     │               │               │
┌────▼─────────────────────────┐     │
│ Can be retried manually:     │     │
│ php artisan queue:retry <id> │     │
└──────────────────────────────┘     │
                                     │
              ┌──────────────────────┘
              │
┌─────────────▼──────────────┐
│ Other notifications        │
│ (WhatsApp) still execute   │
│ - Error isolation          │
└────────────────────────────┘
```

This comprehensive diagram shows that email failures are isolated and don't impact WhatsApp notifications, maintaining system reliability.

---

## Complete Integration Summary

All flows demonstrate:
1. **Pattern Consistency** - Same structure as WhatsApp
2. **Error Isolation** - Channel failures don't affect each other
3. **Comprehensive Logging** - Full audit trail at every step
4. **Template Support** - Database templates with fallback
5. **Queue Processing** - Async execution for performance
6. **Attachment Handling** - PDF support for documents
7. **Settings Integration** - Dynamic configuration
8. **Validation** - Email and file validation
9. **Professional Design** - Beautiful HTML email templates
10. **Production Ready** - Complete error handling and monitoring
