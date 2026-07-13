<?php

use App\Models\User;
use Modules\Notification\Services\NotificationService;

beforeEach(function () {
    $this->service = new NotificationService;
});

describe('record', function () {
    it('creates a notification with the given level', function () {
        $notification = $this->service->record(
            type: 'test.event',
            title: 'Warning',
            message: 'Watch out.',
            level: 'warning',
        );
        expect($notification->level)->toBe('warning');
    });

    it('saves causer morph fields', function () {
        $user = User::factory()->create();
        $notification = $this->service->record('test.event', 'Test', 'msg', causer: $user);
        expect($notification->causer_type)->toBe(User::class);
        expect($notification->causer_id)->toBe($user->id);
    });

    it('saves notifiable morph fields', function () {
        $user = User::factory()->create();
        $notification = $this->service->record('test.event', 'Test', 'msg', notifiable: $user);
        expect($notification->notifiable_type)->toBe(User::class);
        expect($notification->notifiable_id)->toBe($user->id);
    });

    it('saves the extra data payload', function () {
        $notification = $this->service->record('test.event', 'Test', 'msg', data: ['key' => 'value', 'count' => 3]);
        expect($notification->data)->toBe(['key' => 'value', 'count' => 3]);
    });

    it('creates a notification with null morph fields when no causer or notifiable', function () {
        $notification = $this->service->record('test.global', 'Global', 'msg');
        expect($notification->causer_id)->toBeNull();
        expect($notification->notifiable_id)->toBeNull();
    });
});

describe('unreadCountFor', function () {
    it('counts unread notifications for a user', function () {
        $user = User::factory()->create();
        $this->service->record('a', 'A', 'msg', notifiable: $user);
        $this->service->record('b', 'B', 'msg', notifiable: $user);
        $read = $this->service->record('c', 'C', 'msg', notifiable: $user);
        $read->markAsRead();
        expect($this->service->unreadCountFor($user))->toBe(2);
    });

    it('includes global notifications (no notifiable) in the count', function () {
        $user = User::factory()->create();
        $this->service->record('global', 'Global', 'msg');
        expect($this->service->unreadCountFor($user))->toBe(1);
    });
});

describe('markAllReadFor', function () {
    it('marks all unread notifications as read for a user', function () {
        $user = User::factory()->create();
        $this->service->record('a', 'A', 'msg', notifiable: $user);
        $this->service->record('b', 'B', 'msg', notifiable: $user);
        $this->service->markAllReadFor($user);
        expect($this->service->unreadCountFor($user))->toBe(0);
    });
});
