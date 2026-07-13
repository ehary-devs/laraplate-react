<?php

namespace Modules\Dashboard;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::middleware('web')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        });
    }
}
