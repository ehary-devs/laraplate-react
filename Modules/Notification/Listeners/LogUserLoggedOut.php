<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Auth\Events\UserLoggedOut;
use Modules\Notification\Services\NotificationService;

class LogUserLoggedOut extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var UserLoggedOut $event */
        $this->service->record(
            type: 'user.logout',
            title: 'User Logged Out',
            message: "{$event->user->name} signed out.",
            level: 'info',
            causer: $event->user,
            notifiable: $event->user,
        );
    }
}
