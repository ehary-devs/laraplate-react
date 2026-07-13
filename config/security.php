<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP Security Headers
    |--------------------------------------------------------------------------
    */

    'headers' => [
        'enabled' => env('SECURITY_HEADERS_ENABLED', true),

        'content_security_policy' => env(
            'SECURITY_CSP',
            "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self' ws: wss:; frame-ancestors 'none'",
        ),

        'x_frame_options' => 'DENY',
        'x_content_type_options' => 'nosniff',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'camera=(), microphone=(), geolocation=()',

        // Only sent in production
        'hsts' => 'max-age=31536000; includeSubDomains',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Lockout
    |--------------------------------------------------------------------------
    */

    'account_lockout' => [
        'enabled' => env('SECURITY_LOCKOUT_ENABLED', true),
        'max_attempts' => env('SECURITY_LOCKOUT_ATTEMPTS', 5),
        'decay_minutes' => env('SECURITY_LOCKOUT_DECAY', 10),
        'lockout_minutes' => env('SECURITY_LOCKOUT_DURATION', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    */

    'password' => [
        'min_length' => env('SECURITY_PASSWORD_MIN', 8),
        'history_count' => env('SECURITY_PASSWORD_HISTORY', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Hardening
    |--------------------------------------------------------------------------
    */

    'concurrent_sessions' => [
        // true = prevent concurrent sessions (invalidate old sessions on new login)
        'prevent' => env('SECURITY_PREVENT_CONCURRENT_SESSIONS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Data Masking
    |--------------------------------------------------------------------------
    */

    'masked_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'secret',
        'api_key',
        'card_number',
    ],

];
