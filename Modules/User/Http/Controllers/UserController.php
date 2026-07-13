<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Access\Models\Role;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\UserService;

class UserController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function index(): Response
    {
        return Inertia::render('users/index', [
            'users' => UserResource::collection($this->users->list())
                ->response()
                ->getData(true),
            'filters' => request()->only(['search', 'sort', 'dir', 'per_page']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/create', [
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->users->create($request->validated(), $request->user());

        return to_route('user.index')->with('success', 'User created.');
    }

    public function edit(User $user): Response
    {
        $user->load('roles');

        return Inertia::render('users/edit', [
            'user' => (new UserResource($user))->response()->getData(true),
            'roles' => $this->roleOptions(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update($user, $request->validated(), $request->user());

        return to_route('user.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->users->delete($user, request()->user());

        return to_route('user.index')->with('success', 'User deleted.');
    }

    /**
     * @return array<int, array{id: int, name: string, slug: string}>
     */
    private function roleOptions(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ])
            ->all();
    }
}
