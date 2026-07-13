<?php

namespace Modules\User\Services;

use App\Core\Services\BaseService;
use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Events\UserCreated;
use Modules\User\Events\UserDeleted;
use Modules\User\Events\UserUpdated;

class UserService extends BaseService
{
    /**
     * Paginated, filtered user list for the DataTable.
     *
     * @return LengthAwarePaginator<int, User>
     */
    public function list(): LengthAwarePaginator
    {
        return $this->paginateQuery(User::query()->with('roles'), [
            'searchable' => ['name', 'email'],
            'sortable' => ['name', 'email', 'created_at'],
            'default_sort' => 'created_at',
            'default_dir' => 'desc',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?User $causer = null): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        PasswordHistory::record($user->id, $user->password);

        $this->syncRoles($user, $data['roles'] ?? []);

        event(new UserCreated($user, $causer));

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data, ?User $causer = null): User
    {
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $passwordChanged = ! empty($data['password']);

        if ($passwordChanged) {
            $user->fill(['password' => $data['password']]);
        }

        $user->save();

        if ($passwordChanged) {
            PasswordHistory::record($user->id, $user->password);
        }

        $this->syncRoles($user, $data['roles'] ?? []);

        event(new UserUpdated($user, $causer));

        return $user->fresh();
    }

    public function delete(User $user, ?User $causer = null): void
    {
        $user->delete();

        event(new UserDeleted($user, $causer));
    }

    private function syncRoles(User $user, mixed $roleIds): void
    {
        $user->roles()->sync(is_array($roleIds) ? $roleIds : []);
        $user->clearPermissionCache();
    }
}
