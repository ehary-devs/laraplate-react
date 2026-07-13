<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Events\AccountLocked;

/**
 * Per-account login lockout on top of Fortify's IP/username rate limiter.
 *
 * Tracks failed attempts in the cache and, once the configured threshold is
 * reached, locks the account for a fixed window and dispatches AccountLocked.
 */
class AccountLockout
{
    /**
     * Abort authentication with a throttle message when the account is locked.
     *
     * @throws ValidationException
     */
    public function ensureNotLocked(string $email): void
    {
        if (! $this->enabled()) {
            return;
        }

        $email = $this->normalize($email);

        if (! Cache::has($this->lockoutKey($email))) {
            return;
        }

        $seconds = max(0, ((int) Cache::get($this->lockoutUntilKey($email), 0)) - $this->now());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Record a failed login attempt, locking the account when the threshold is hit.
     */
    public function recordFailure(string $email): void
    {
        if (! $this->enabled()) {
            return;
        }

        $email = $this->normalize($email);

        $maxAttempts = (int) config('security.account_lockout.max_attempts', 5);
        $decaySeconds = (int) config('security.account_lockout.decay_minutes', 10) * 60;
        $lockoutMinutes = (int) config('security.account_lockout.lockout_minutes', 15);

        $attempts = ((int) Cache::get($this->attemptsKey($email), 0)) + 1;
        Cache::put($this->attemptsKey($email), $attempts, $decaySeconds);

        if ($attempts < $maxAttempts) {
            return;
        }

        $lockoutSeconds = $lockoutMinutes * 60;
        Cache::put($this->lockoutKey($email), true, $lockoutSeconds);
        Cache::put($this->lockoutUntilKey($email), $this->now() + $lockoutSeconds, $lockoutSeconds);
        Cache::forget($this->attemptsKey($email));

        $user = User::where('email', $email)->first();

        if ($user) {
            event(new AccountLocked($user, $lockoutMinutes));
        }
    }

    /**
     * Clear all lockout state for the account after a successful login.
     */
    public function clear(string $email): void
    {
        $email = $this->normalize($email);

        Cache::forget($this->attemptsKey($email));
        Cache::forget($this->lockoutKey($email));
        Cache::forget($this->lockoutUntilKey($email));
    }

    private function enabled(): bool
    {
        return (bool) config('security.account_lockout.enabled', true);
    }

    private function normalize(string $email): string
    {
        return Str::lower(trim($email));
    }

    private function now(): int
    {
        return now()->getTimestamp();
    }

    private function attemptsKey(string $email): string
    {
        return 'login_attempts:'.md5($email);
    }

    private function lockoutKey(string $email): string
    {
        return 'account_lockout:'.md5($email);
    }

    private function lockoutUntilKey(string $email): string
    {
        return 'account_lockout_until:'.md5($email);
    }
}
