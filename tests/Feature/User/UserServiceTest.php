<?php

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Modules\Access\Models\Role;
use Modules\User\Events\UserCreated;
use Modules\User\Events\UserDeleted;
use Modules\User\Services\UserService;

beforeEach(function () {
    $this->service = new UserService;
});

describe('create', function () {
    it('creates a user, records password history, and fires UserCreated', function () {
        Event::fake([UserCreated::class]);

        $user = $this->service->create([
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'password' => 'Password123!',
            'roles' => [],
        ]);

        expect($user->exists)->toBeTrue();
        expect(PasswordHistory::where('user_id', $user->id)->count())->toBe(1);
        Event::assertDispatched(UserCreated::class);
    });

    it('assigns roles on create', function () {
        $role = Role::create(['name' => 'Editor', 'slug' => 'editor']);

        $user = $this->service->create([
            'name' => 'Ada',
            'email' => 'ada2@example.com',
            'password' => 'Password123!',
            'roles' => [$role->id],
        ]);

        expect($user->hasRole('editor'))->toBeTrue();
    });
});

describe('delete', function () {
    it('soft-deletes and fires UserDeleted', function () {
        Event::fake([UserDeleted::class]);
        $user = User::factory()->create();

        $this->service->delete($user);

        expect($user->fresh()->trashed())->toBeTrue();
        Event::assertDispatched(UserDeleted::class);
    });
});

describe('list (DataTable helper)', function () {
    it('searches by name across the searchable columns', function () {
        User::factory()->create(['name' => 'Findable Person']);
        User::factory()->count(3)->create();

        request()->merge(['search' => 'Findable']);

        $result = $this->service->list();

        expect($result->total())->toBe(1);
    });

    it('caps per-page to the allowed set, defaulting to 10', function () {
        User::factory()->count(15)->create();
        request()->merge(['per_page' => 999]);

        $result = $this->service->list();

        expect($result->perPage())->toBe(10);
    });

    it('honours an allowed per-page value', function () {
        User::factory()->count(30)->create();
        request()->merge(['per_page' => 25]);

        $result = $this->service->list();

        expect($result->perPage())->toBe(25);
    });
});
