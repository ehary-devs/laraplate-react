<?php

namespace Modules\AuditLog\Listeners;

use App\Core\Listeners\IdempotentListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\AuditLog\Services\AuditLogService;
use Modules\Auth\Events\UserLoggedIn;
use Throwable;

class LogAuditUserLoggedIn extends IdempotentListener implements ShouldQueue
{
    public function __construct(private readonly AuditLogService $service) {}

    protected function process(object $event): void
    {
        /** @var UserLoggedIn $event */
        $this->service->record(
            event: 'login',
            newValues: [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            userId: $event->user->id,
        );
    }

    public function failed(UserLoggedIn $event, Throwable $exception): void
    {
        Log::error('AuditLog listener failed', [
            'listener' => static::class,
            'user_id' => $event->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
