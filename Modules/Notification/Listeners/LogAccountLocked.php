<?php

namespace Modules\Notification\Listeners;

use App\Core\Listeners\IdempotentListener;
use Modules\Auth\Events\AccountLocked;
use Modules\Notification\Services\NotificationService;

class LogAccountLocked extends IdempotentListener
{
    public function __construct(private readonly NotificationService $service) {}

    protected function process(object $event): void
    {
        /** @var AccountLocked $event */
        $this->service->record(
            type: 'account.locked',
            title: 'Akun Dikunci',
            message: "Akun {$event->user->name} dikunci {$event->lockoutMinutes} menit akibat terlalu banyak gagal login.",
            level: 'warning',
            causer: null,
            notifiable: $event->user,
        );
    }
}
