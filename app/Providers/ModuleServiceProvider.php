<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->discoverModules();
    }

    public function boot(): void
    {
        //
    }

    private function discoverModules(): void
    {
        $modulesPath = base_path('Modules');

        if (! File::isDirectory($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $configFile = $modulePath.'/module.json';

            if (! File::exists($configFile)) {
                continue;
            }

            $config = json_decode(File::get($configFile), true);

            if (! ($config['active'] ?? false)) {
                continue;
            }

            $moduleName = basename($modulePath);
            $providerClass = "Modules\\{$moduleName}\\{$moduleName}ServiceProvider";

            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }
}
