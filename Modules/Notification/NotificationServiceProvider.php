<?php

namespace Modules\Notification;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Access\Events\RoleAssigned;
use Modules\Access\Events\RoleRevoked;
use Modules\Auth\Events\AccountLocked;
use Modules\Auth\Events\UserLoggedIn;
use Modules\Auth\Events\UserLoggedOut;
use Modules\Auth\Events\UserRegistered;
use Modules\Notification\Listeners\LogAccountLocked;
use Modules\Notification\Listeners\LogRoleAssigned;
use Modules\Notification\Listeners\LogRoleRevoked;
use Modules\Notification\Listeners\LogUserCreated;
use Modules\Notification\Listeners\LogUserDeleted;
use Modules\Notification\Listeners\LogUserLoggedIn;
use Modules\Notification\Listeners\LogUserLoggedOut;
use Modules\Notification\Listeners\LogUserRegistered;
use Modules\User\Events\UserCreated;
use Modules\User\Events\UserDeleted;

class NotificationServiceProvider extends ServiceProvider
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
        Event::listen(UserLoggedIn::class, LogUserLoggedIn::class);
        Event::listen(UserLoggedOut::class, LogUserLoggedOut::class);
        Event::listen(UserRegistered::class, LogUserRegistered::class);
        Event::listen(AccountLocked::class, LogAccountLocked::class);
        Event::listen(RoleAssigned::class, LogRoleAssigned::class);
        Event::listen(RoleRevoked::class, LogRoleRevoked::class);
        Event::listen(UserCreated::class, LogUserCreated::class);
        Event::listen(UserDeleted::class, LogUserDeleted::class);
    }
}
