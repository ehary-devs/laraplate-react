<?php

namespace Modules\AuditLog\Listeners;

use App\Core\Listeners\IdempotentListener;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Access\Events\PermissionGranted;
use Modules\AuditLog\Services\AuditLogService;
use Throwable;

class LogAuditPermissionGranted extends IdempotentListener implements ShouldQueue
{
    public function __construct(private readonly AuditLogService $service) {}

    protected function process(object $event): void
    {
        /** @var PermissionGranted $event */
        $this->service->record(
            event: 'permission_granted',
            auditableType: User::class,
            auditableId: $event->user->id,
            newValues: ['permission' => $event->permission->slug],
            tags: ['rbac'],
        );
    }

    public function failed(PermissionGranted $event, Throwable $exception): void
    {
        Log::error('AuditLog listener failed', [
            'listener' => static::class,
            'user_id' => $event->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
