<?php

namespace Modules\Access\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Modules\Access\Models\Permission;

class PermissionGranted
{
    use Dispatchable, SerializesModels;

    public readonly string $idempotencyKey;

    public function __construct(
        public readonly User $user,
        public readonly Permission $permission,
    ) {
        $this->idempotencyKey = (string) Str::uuid();
    }
}
