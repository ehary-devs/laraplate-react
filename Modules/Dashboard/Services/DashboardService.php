<?php

namespace Modules\Dashboard\Services;

use App\Models\User;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;
use Modules\Notification\Models\Notification;

class DashboardService
{
    /**
     * Aggregate counts shown in the dashboard stat widgets.
     *
     * @return array{users: int, roles: int, permissions: int, unread_notifications: int}
     */
    public function stats(): array
    {
        return [
            'users' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'unread_notifications' => Notification::unread()->count(),
        ];
    }

    /**
     * Most recent notifications for the activity feed.
     *
     * @return array<int, array{id: string, title: string, message: string, level: string, created_at: string}>
     */
    public function recentNotifications(int $limit = 5): array
    {
        return Notification::query()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Notification $notification): array => [
                'id' => $notification->uuid,
                'title' => $notification->title,
                'message' => (string) $notification->message,
                'level' => $notification->level ?? 'info',
                'created_at' => $notification->created_at->diffForHumans(),
            ])
            ->all();
    }
}
