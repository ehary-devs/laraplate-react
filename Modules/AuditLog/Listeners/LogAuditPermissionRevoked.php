<?php

namespace Modules\AuditLog\Listeners;

use App\Core\Listeners\IdempotentListener;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Access\Events\PermissionRevoked;
use Modules\AuditLog\Services\AuditLogService;
use Throwable;

class LogAuditPermissionRevoked extends IdempotentListener implements ShouldQueue
{
    public function __construct(private readonly AuditLogService $service) {}

    protected function process(object $event): void
    {
        /** @var PermissionRevoked $event */
        $this->service->record(
            event: 'permission_revoked',
            auditableType: User::class,
            auditableId: $event->user->id,
            oldValues: ['permission' => $event->permission->slug],
            tags: ['rbac'],
        );
    }

    public function failed(PermissionRevoked $event, Throwable $exception): void
    {
        Log::error('AuditLog listener failed', [
            'listener' => static::class,
            'user_id' => $event->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
