<?php

if (! function_exists('app_currency')) {
    function app_currency(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('app_currency', 'application', 'INR');
    }
}

if (! function_exists('app_currency_symbol')) {
    function app_currency_symbol(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('app_currency_symbol', 'application', '₹');
    }
}

if (! function_exists('app_date_format')) {
    function app_date_format(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('app_date_format', 'application', 'd/m/Y');
    }
}

if (! function_exists('app_time_format')) {
    function app_time_format(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('app_time_format', 'application', '12h');
    }
}

if (! function_exists('format_indian_currency')) {
    function format_indian_currency($amount): string
    {
        return app_currency_symbol().' '.number_format($amount, 2);
    }
}

if (! function_exists('format_app_date')) {
    function format_app_date($date): string
    {
        if (! $date) {
            return 'N/A';
        }

        return \Carbon\Carbon::parse($date)->format(app_date_format());
    }
}

if (! function_exists('format_app_time')) {
    function format_app_time($datetime): string
    {
        if (! $datetime) {
            return 'N/A';
        }
        $format = app_time_format() === '24h' ? 'H:i' : 'h:i A';

        return \Carbon\Carbon::parse($datetime)->format($format);
    }
}

if (! function_exists('format_app_datetime')) {
    function format_app_datetime($datetime): string
    {
        if (! $datetime) {
            return 'N/A';
        }
        $dateFormat = app_date_format();
        $timeFormat = app_time_format() === '24h' ? 'H:i' : 'h:i A';

        return \Carbon\Carbon::parse($datetime)->format($dateFormat.' '.$timeFormat);
    }
}

if (! function_exists('is_email_notification_enabled')) {
    function is_email_notification_enabled(): bool
    {
        return app(\App\Services\AppSettingService::class)
            ->get('email_notifications_enabled', 'notifications', true) === 'true';
    }
}

if (! function_exists('is_whatsapp_notification_enabled')) {
    function is_whatsapp_notification_enabled(): bool
    {
        return app(\App\Services\AppSettingService::class)
            ->get('whatsapp_notifications_enabled', 'notifications', true) === 'true';
    }
}

if (! function_exists('is_birthday_wishes_enabled')) {
    function is_birthday_wishes_enabled(): bool
    {
        return app(\App\Services\AppSettingService::class)
            ->get('birthday_wishes_enabled', 'notifications', true) === 'true';
    }
}

if (! function_exists('get_renewal_reminder_days')) {
    function get_renewal_reminder_days(): array
    {
        $days = app(\App\Services\AppSettingService::class)
            ->get('renewal_reminder_days', 'notifications', '30,15,7,1');

        return array_map('intval', explode(',', $days));
    }
}

if (! function_exists('company_name')) {
    function company_name(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('company_name', 'company', 'Parth Rawal Insurance Advisor');
    }
}

if (! function_exists('company_advisor_name')) {
    function company_advisor_name(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('company_advisor_name', 'company', 'Parth Rawal');
    }
}

if (! function_exists('company_website')) {
    function company_website(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('company_website', 'company', 'https://parthrawal.in');
    }
}

if (! function_exists('company_phone')) {
    function company_phone(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('company_phone', 'company', '+91 97277 93123');
    }
}

if (! function_exists('company_phone_whatsapp')) {
    function company_phone_whatsapp(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('company_phone_whatsapp', 'company', '919727793123');
    }
}

if (! function_exists('company_title')) {
    function company_title(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('company_title', 'company', 'Your Trusted Insurance Advisor');
    }
}

if (! function_exists('company_tagline')) {
    function company_tagline(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('company_tagline', 'company', 'Think of Insurance, Think of Us.');
    }
}

if (! function_exists('email_from_address')) {
    function email_from_address(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('email_from_address', 'email', 'noreply@example.com');
    }
}

if (! function_exists('email_from_name')) {
    function email_from_name(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('email_from_name', 'email', company_name());
    }
}

if (! function_exists('email_reply_to')) {
    function email_reply_to(): string
    {
        return app(\App\Services\AppSettingService::class)
            ->get('email_reply_to', 'email', email_from_address());
    }
}
