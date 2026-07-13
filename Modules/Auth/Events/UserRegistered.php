<?php

namespace Modules\Auth\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UserRegistered
{
    use Dispatchable, SerializesModels;

    public readonly string $idempotencyKey;

    public function __construct(public readonly User $user)
    {
        $this->idempotencyKey = (string) Str::uuid();
    }
}
