<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view any users') || $user->hasPermissionTo('edit own profile');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('view user') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create user');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('update user') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('delete user');
    }
}
