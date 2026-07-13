<?php

namespace Modules\AuditLog\Listeners;

use App\Core\Listeners\IdempotentListener;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\AuditLog\Services\AuditLogService;
use Modules\Auth\Events\AccountLocked;
use Throwable;

class LogAuditAccountLocked extends IdempotentListener implements ShouldQueue
{
    public function __construct(private readonly AuditLogService $service) {}

    protected function process(object $event): void
    {
        /** @var AccountLocked $event */
        $this->service->record(
            event: 'account_locked',
            auditableType: User::class,
            auditableId: $event->user->id,
            newValues: [
                'reason' => 'too_many_failed_attempts',
                'locked_until' => now()->addMinutes($event->lockoutMinutes)->toIso8601String(),
            ],
            userId: $event->user->id,
            tags: ['security', 'critical'],
        );
    }

    public function failed(AccountLocked $event, Throwable $exception): void
    {
        Log::error('AuditLog listener failed', [
            'listener' => static::class,
            'user_id' => $event->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
