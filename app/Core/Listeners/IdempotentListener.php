<?php

namespace App\Core\Listeners;

use Illuminate\Support\Facades\Cache;

abstract class IdempotentListener
{
    public int $timeout = 30;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60];

    protected int $idempotencyTtl = 86400;

    final public function handle(object $event): void
    {
        if ($this->alreadyProcessed($event)) {
            return;
        }

        $this->process($event);
        $this->markProcessed($event);
    }

    abstract protected function process(object $event): void;

    private function alreadyProcessed(object $event): bool
    {
        $key = $this->idempotencyKey($event);

        if ($key === null) {
            return false;
        }

        return Cache::has($this->cacheKey($key));
    }

    private function markProcessed(object $event): void
    {
        $key = $this->idempotencyKey($event);

        if ($key === null) {
            return;
        }

        Cache::put($this->cacheKey($key), true, $this->idempotencyTtl);
    }

    private function idempotencyKey(object $event): ?string
    {
        if (! property_exists($event, 'idempotencyKey')) {
            return null;
        }

        $key = $event->idempotencyKey;

        return is_string($key) && $key !== '' ? $key : null;
    }

    private function cacheKey(string $idempotencyKey): string
    {
        return sprintf('idempotent_listener:%s:%s', static::class, $idempotencyKey);
    }
}
