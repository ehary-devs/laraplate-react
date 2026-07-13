<?php

namespace Modules\Notification\Services;

use App\Core\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Modules\Notification\Models\Notification;

class NotificationService extends BaseService
{
    /** @param array<string, mixed>|null $data */
    public function record(
        string $type,
        string $title,
        string $message,
        string $level = 'info',
        mixed $causer = null,
        mixed $notifiable = null,
        ?array $data = null,
    ): Notification {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'level' => $level,
            'causer_type' => $causer ? $causer::class : null,
            'causer_id' => $causer?->id,
            'notifiable_type' => $notifiable ? $notifiable::class : null,
            'notifiable_id' => $notifiable?->id,
            'data' => $data,
        ]);
    }

    public function unreadCountFor(User $user): int
    {
        return Notification::forUser($user->id)->unread()->count();
    }

    /** @return Collection<int, Notification> */
    public function recentFor(User $user, int $limit = 10): Collection
    {
        return Notification::forUser($user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function markAllReadFor(User $user): void
    {
        Notification::forUser($user->id)->unread()->update(['read_at' => now()]);
    }
}
