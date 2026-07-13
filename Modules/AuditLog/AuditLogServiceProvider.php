<?php

namespace Modules\AuditLog;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Access\Events\PermissionGranted;
use Modules\Access\Events\PermissionRevoked;
use Modules\Access\Events\RoleAssigned;
use Modules\Access\Events\RoleRevoked;
use Modules\AuditLog\Listeners\LogAuditAccountLocked;
use Modules\AuditLog\Listeners\LogAuditPasswordReset;
use Modules\AuditLog\Listeners\LogAuditPermissionGranted;
use Modules\AuditLog\Listeners\LogAuditPermissionRevoked;
use Modules\AuditLog\Listeners\LogAuditRoleAssigned;
use Modules\AuditLog\Listeners\LogAuditRoleRevoked;
use Modules\AuditLog\Listeners\LogAuditUserLoggedIn;
use Modules\AuditLog\Listeners\LogAuditUserLoggedOut;
use Modules\Auth\Events\AccountLocked;
use Modules\Auth\Events\UserLoggedIn;
use Modules\Auth\Events\UserLoggedOut;

class AuditLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerListeners();
    }

    private function registerListeners(): void
    {
        Event::listen(UserLoggedIn::class, LogAuditUserLoggedIn::class);
        Event::listen(UserLoggedOut::class, LogAuditUserLoggedOut::class);
        Event::listen(AccountLocked::class, LogAuditAccountLocked::class);
        Event::listen(PasswordReset::class, LogAuditPasswordReset::class);
        Event::listen(RoleAssigned::class, LogAuditRoleAssigned::class);
        Event::listen(RoleRevoked::class, LogAuditRoleRevoked::class);
        Event::listen(PermissionGranted::class, LogAuditPermissionGranted::class);
        Event::listen(PermissionRevoked::class, LogAuditPermissionRevoked::class);
    }
}
