<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Notification\Services\NotificationService;
use Modules\User\Events\UserCreated;

class LogUserCreated extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var UserCreated $event */
        $this->service->record(
            type: 'user.created',
            title: 'New User Created',
            message: "{$event->user->name} ({$event->user->email}) was created.",
            level: 'success',
            causer: $event->causer,
        );
    }
}
