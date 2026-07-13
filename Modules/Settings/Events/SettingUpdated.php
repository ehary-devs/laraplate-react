<?php

namespace Modules\Settings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Modules\Settings\Models\Setting;

class SettingUpdated
{
    use Dispatchable, SerializesModels;

    public readonly string $idempotencyKey;

    public function __construct(
        public readonly Setting $setting,
        public readonly mixed $oldValue = null,
        public readonly mixed $newValue = null,
    ) {
        $this->idempotencyKey = (string) Str::uuid();
    }
}
