<?php

namespace Modules\Access;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Access\Middleware\PermissionMiddleware;
use Modules\Access\Middleware\RoleMiddleware;

class AccessServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMiddleware();
        $this->registerGate();
    }

    private function loadMiddleware(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('role', RoleMiddleware::class);
        $router->aliasMiddleware('permission', PermissionMiddleware::class);
    }

    private function registerGate(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }

            return null;
        });

        Gate::after(function ($user, $ability) {
            return $user->hasPermissionTo($ability);
        });
    }
}
