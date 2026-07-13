<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Auth\Events\UserLoggedIn;
use Modules\Notification\Services\NotificationService;

class LogUserLoggedIn extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var UserLoggedIn $event */
        $this->service->record(
            type: 'user.login',
            title: 'User Logged In',
            message: "{$event->user->name} signed in.",
            level: 'info',
            causer: $event->user,
            notifiable: $event->user,
        );
    }
}
