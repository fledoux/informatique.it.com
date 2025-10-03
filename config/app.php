<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Europe/Paris',
    'locale' => env('APP_LOCALE', 'fr'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'fr_FR'),
    'brand_name' => env('COMPANY_BRAND_NAME', 'informatique-it.com'),
    'company' => [
        'domain' => env('COMPANY_DOMAIN', 'informatique-it.com'),
        'url' => env('COMPANY_URL', 'https://informatique-it.com'),
        'emails' => [
            'hello' => env('COMPANY_EMAIL_HELLO', 'hello@informatique-it.com'),
            'help' => env('COMPANY_EMAIL_HELP', 'help@informatique-it.com'),
            'dev' => env('COMPANY_EMAIL_DEV', 'dev@informatique-it.com'),
            'legal' => env('COMPANY_EMAIL_LEGAL', 'legal@informatique-it.com'),
            'rgpd' => env('COMPANY_EMAIL_RGPD', 'rgpd@informatique-it.com'),
        ],
    ],
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'asset_url' => env('ASSET_URL'),
];
