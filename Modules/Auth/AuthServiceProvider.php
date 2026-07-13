<?php

namespace Modules\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Auth\Events\AccountLocked;
use Modules\Auth\Events\UserLoggedIn;
use Modules\Auth\Events\UserLoggedOut;
use Modules\Auth\Events\UserRegistered;
use Modules\Auth\Listeners\SendAccountLockedEmail;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/views', 'auth');
        $this->registerListeners();
    }

    /**
     * Bridge Fortify's framework auth events onto the module's domain events
     * so downstream modules (AuditLog, notifications) stay Fortify-agnostic.
     */
    private function registerListeners(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if (! $event->user instanceof User) {
                return;
            }

            $this->preventConcurrentSessions($event->user);

            event(new UserLoggedIn($event->user));
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if ($event->user instanceof User) {
                event(new UserLoggedOut($event->user));
            }
        });

        Event::listen(Registered::class, function (Registered $event): void {
            if ($event->user instanceof User) {
                event(new UserRegistered($event->user));
            }
        });

        Event::listen(AccountLocked::class, SendAccountLockedEmail::class);
    }

    /**
     * Invalidate the user's other sessions when concurrent sessions are disabled.
     */
    private function preventConcurrentSessions(User $user): void
    {
        if (! config('security.concurrent_sessions.prevent', false)) {
            return;
        }

        $request = request();

        if (! $request->hasSession()) {
            return;
        }

        DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
    }
}
