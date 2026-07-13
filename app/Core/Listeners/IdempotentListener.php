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
        if (! property_exists($event, 'idempotencyKey') || $event->idempotencyKey === '') {
            return false;
        }

        return Cache::has($this->cacheKey($event));
    }

    private function markProcessed(object $event): void
    {
        if (! property_exists($event, 'idempotencyKey') || $event->idempotencyKey === '') {
            return;
        }

        Cache::put($this->cacheKey($event), true, $this->idempotencyTtl);
    }

    private function cacheKey(object $event): string
    {
        return sprintf('idempotent_listener:%s:%s', static::class, $event->idempotencyKey);
    }
}
