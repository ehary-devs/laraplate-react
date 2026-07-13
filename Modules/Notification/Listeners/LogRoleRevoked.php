<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Access\Events\RoleRevoked;
use Modules\Notification\Services\NotificationService;

class LogRoleRevoked extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var RoleRevoked $event */
        $this->service->record(
            type: 'access.role_revoked',
            title: 'Role Revoked',
            message: "Role \"{$event->role->name}\" removed from {$event->user->name}.",
            level: 'warning',
            notifiable: $event->user,
        );
    }
}
