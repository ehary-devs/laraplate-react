<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Notification\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('email', config('seeder.super_admin_email'))->first();

        if (! $superAdmin) {
            return;
        }

        $notifications = [
            [
                'type' => 'user.created',
                'title' => 'New user registered',
                'message' => 'manager@laraplate.test joined the platform.',
                'level' => 'info',
            ],
            [
                'type' => 'access.role_assigned',
                'title' => 'Role assigned',
                'message' => 'editor@laraplate.test was assigned the Editor role.',
                'level' => 'info',
            ],
            [
                'type' => 'user.created',
                'title' => 'New user registered',
                'message' => 'alice@laraplate.test joined the platform.',
                'level' => 'info',
            ],
            [
                'type' => 'user.created',
                'title' => 'New user registered',
                'message' => 'bob@laraplate.test joined the platform.',
                'level' => 'info',
            ],
            [
                'type' => 'system',
                'title' => 'Seeder completed',
                'message' => 'Demo data has been loaded successfully.',
                'level' => 'success',
                'read_at' => now(),
            ],
        ];

        foreach ($notifications as $data) {
            Notification::create([
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'level' => $data['level'],
                'notifiable_type' => User::class,
                'notifiable_id' => $superAdmin->id,
                'read_at' => $data['read_at'] ?? null,
            ]);
        }
    }
}
