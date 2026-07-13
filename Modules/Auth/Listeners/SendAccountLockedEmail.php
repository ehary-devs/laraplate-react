<?php

namespace Modules\Auth\Listeners;

use App\Core\Listeners\IdempotentListener;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Events\AccountLocked;
use Modules\Auth\Mail\AccountLockedMail;

class SendAccountLockedEmail extends IdempotentListener
{
    protected function process(object $event): void
    {
        /** @var AccountLocked $event */
        Mail::send(new AccountLockedMail($event->user, $event->lockoutMinutes));
    }
}
