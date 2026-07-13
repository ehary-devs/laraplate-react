<?php

namespace Modules\User\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UserCreated
{
    use Dispatchable, SerializesModels;

    public readonly string $idempotencyKey;

    public function __construct(
        public readonly User $user,
        public readonly ?User $causer = null,
    ) {
        $this->idempotencyKey = (string) Str::uuid();
    }
}
