<?php

if (!function_exists('app_currency')) {
    function app_currency(): string {
        return app(\App\Services\AppSettingService::class)
            ->get('app_currency', 'application', 'INR');
    }
}

if (!function_exists('app_currency_symbol')) {
    function app_currency_symbol(): string {
        return app(\App\Services\AppSettingService::class)
            ->get('app_currency_symbol', 'application', '₹');
    }
}

if (!function_exists('app_date_format')) {
    function app_date_format(): string {
        return app(\App\Services\AppSettingService::class)
            ->get('app_date_format', 'application', 'd/m/Y');
    }
}

if (!function_exists('app_time_format')) {
    function app_time_format(): string {
        return app(\App\Services\AppSettingService::class)
            ->get('app_time_format', 'application', '12h');
    }
}

if (!function_exists('format_indian_currency')) {
    function format_indian_currency($amount): string {
        return app_currency_symbol() . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('format_app_date')) {
    function format_app_date($date): string {
        if (!$date) return 'N/A';
        return \Carbon\Carbon::parse($date)->format(app_date_format());
    }
}

if (!function_exists('format_app_time')) {
    function format_app_time($datetime): string {
        if (!$datetime) return 'N/A';
        $format = app_time_format() === '24h' ? 'H:i' : 'h:i A';
        return \Carbon\Carbon::parse($datetime)->format($format);
    }
}

if (!function_exists('format_app_datetime')) {
    function format_app_datetime($datetime): string {
        if (!$datetime) return 'N/A';
        $dateFormat = app_date_format();
        $timeFormat = app_time_format() === '24h' ? 'H:i' : 'h:i A';
        return \Carbon\Carbon::parse($datetime)->format($dateFormat . ' ' . $timeFormat);
    }
}

if (!function_exists('is_email_notification_enabled')) {
    function is_email_notification_enabled(): bool {
        return app(\App\Services\AppSettingService::class)
            ->get('email_notifications_enabled', 'notifications', true) === 'true';
    }
}

if (!function_exists('is_whatsapp_notification_enabled')) {
    function is_whatsapp_notification_enabled(): bool {
        return app(\App\Services\AppSettingService::class)
            ->get('whatsapp_notifications_enabled', 'notifications', true) === 'true';
    }
}

if (!function_exists('is_birthday_wishes_enabled')) {
    function is_birthday_wishes_enabled(): bool {
        return app(\App\Services\AppSettingService::class)
            ->get('birthday_wishes_enabled', 'notifications', true) === 'true';
    }
}

if (!function_exists('get_renewal_reminder_days')) {
    function get_renewal_reminder_days(): array {
        $days = app(\App\Services\AppSettingService::class)
            ->get('renewal_reminder_days', 'notifications', '30,15,7,1');
        return array_map('intval', explode(',', $days));
    }
}
