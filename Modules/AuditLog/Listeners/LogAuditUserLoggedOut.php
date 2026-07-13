<?php

namespace Modules\AuditLog\Listeners;

use App\Core\Listeners\IdempotentListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\AuditLog\Services\AuditLogService;
use Modules\Auth\Events\UserLoggedOut;
use Throwable;

class LogAuditUserLoggedOut extends IdempotentListener implements ShouldQueue
{
    public function __construct(private readonly AuditLogService $service) {}

    protected function process(object $event): void
    {
        /** @var UserLoggedOut $event */
        $this->service->record(
            event: 'logout',
            userId: $event->user->id,
        );
    }

    public function failed(UserLoggedOut $event, Throwable $exception): void
    {
        Log::error('AuditLog listener failed', [
            'listener' => static::class,
            'user_id' => $event->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
