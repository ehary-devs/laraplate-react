<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Auth\Events\UserRegistered;
use Modules\Notification\Services\NotificationService;

class LogUserRegistered extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var UserRegistered $event */
        $this->service->record(
            type: 'user.registered',
            title: 'New User Registered',
            message: "{$event->user->name} ({$event->user->email}) created an account.",
            level: 'success',
            causer: $event->user,
        );
    }
}
