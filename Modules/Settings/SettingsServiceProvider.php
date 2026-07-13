<?php

namespace Modules\Settings;

use Illuminate\Support\ServiceProvider;
use Modules\Settings\Services\SettingsService;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
    }

    public function boot(): void
    {
        //
    }
}
