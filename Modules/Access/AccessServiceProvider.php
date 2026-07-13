<?php

namespace Modules\Access;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
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
        $this->loadModuleRoutes();
    }

    private function loadModuleRoutes(): void
    {
        Route::middleware('web')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        });
    }

    private function loadMiddleware(): void
    {
        $router = $this->app->make(Router::class);
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
