<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Access\Events\RoleAssigned;
use Modules\Notification\Services\NotificationService;

class LogRoleAssigned extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var RoleAssigned $event */
        $this->service->record(
            type: 'access.role_assigned',
            title: 'Role Assigned',
            message: "Role \"{$event->role->name}\" assigned to {$event->user->name}.",
            level: 'info',
            notifiable: $event->user,
        );
    }
}
