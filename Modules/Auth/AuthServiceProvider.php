<?php

namespace Modules\Auth;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Auth\Events\AccountLocked;
use Modules\Auth\Listeners\SendAccountLockedEmail;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/views', 'auth');
        $this->registerListeners();
    }

    private function registerListeners(): void
    {
        Event::listen(AccountLocked::class, SendAccountLockedEmail::class);
    }
}
