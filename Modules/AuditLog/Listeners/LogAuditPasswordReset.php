<?php

namespace Modules\AuditLog\Listeners;

use App\Core\Listeners\IdempotentListener;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\AuditLog\Services\AuditLogService;
use Throwable;

class LogAuditPasswordReset extends IdempotentListener implements ShouldQueue
{
    public function __construct(private readonly AuditLogService $service) {}

    protected function process(object $event): void
    {
        /** @var PasswordReset $event */
        /** @var User $user */
        $user = $event->user;

        $this->service->record(
            event: 'password_reset',
            auditableType: User::class,
            auditableId: $user->id,
            userId: $user->id,
            tags: ['security'],
        );
    }

    public function failed(PasswordReset $event, Throwable $exception): void
    {
        /** @var User $user */
        $user = $event->user;

        Log::error('AuditLog listener failed', [
            'listener' => static::class,
            'user_id' => $user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
