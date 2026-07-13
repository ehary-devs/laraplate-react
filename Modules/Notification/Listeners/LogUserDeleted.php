<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Notification\Services\NotificationService;
use Modules\User\Events\UserDeleted;

class LogUserDeleted extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var UserDeleted $event */
        $this->service->record(
            type: 'user.deleted',
            title: 'User Deleted',
            message: "{$event->user->name} ({$event->user->email}) was deleted.",
            level: 'warning',
            causer: $event->causer,
        );
    }
}
