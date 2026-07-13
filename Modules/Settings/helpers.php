<?php

use Modules\Settings\Services\SettingsService;

if (! function_exists('setting')) {
    /**
     * Retrieve an application setting value by key.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}
