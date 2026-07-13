<?php

namespace Modules\AuditLog\Listeners;

use App\Core\Listeners\IdempotentListener;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Access\Events\RoleAssigned;
use Modules\AuditLog\Services\AuditLogService;
use Throwable;

class LogAuditRoleAssigned extends IdempotentListener implements ShouldQueue
{
    public function __construct(private readonly AuditLogService $service) {}

    protected function process(object $event): void
    {
        /** @var RoleAssigned $event */
        $this->service->record(
            event: 'role_assigned',
            auditableType: User::class,
            auditableId: $event->user->id,
            newValues: ['role' => $event->role->slug],
            tags: ['rbac'],
        );
    }

    public function failed(RoleAssigned $event, Throwable $exception): void
    {
        Log::error('AuditLog listener failed', [
            'listener' => static::class,
            'user_id' => $event->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
