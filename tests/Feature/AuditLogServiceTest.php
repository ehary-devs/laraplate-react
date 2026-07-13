<?php

use App\Models\User;
use Modules\AuditLog\Models\AuditLog;
use Modules\AuditLog\Services\AuditLogService;

beforeEach(function () {
    $this->service = new AuditLogService;
});

it('records an audit entry with the given event and values', function () {
    $user = User::factory()->create();
    $log = $this->service->record(
        event: 'role_assigned',
        auditableType: User::class,
        auditableId: $user->id,
        newValues: ['role' => 'editor'],
        userId: $user->id,
        tags: ['rbac'],
    );

    expect($log->event)->toBe('role_assigned');
    expect($log->new_values)->toBe(['role' => 'editor']);
    expect($log->tags)->toBe(['rbac']);
    expect($log->auditable_id)->toBe($user->id);
});

it('captures the acting user id from auth when not passed', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $log = $this->service->record(event: 'login');
    expect($log->user_id)->toBe($user->id);
});

it('persists audit entries to the database', function () {
    $this->service->record(event: 'logout', userId: 1);
    expect(AuditLog::where('event', 'logout')->exists())->toBeTrue();
});
