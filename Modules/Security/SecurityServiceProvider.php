<?php

namespace Modules\Security;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Modules\Security\Middleware\SecureHeaders;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->booted(function () {
            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('web', SecureHeaders::class);
        });
    }
}
