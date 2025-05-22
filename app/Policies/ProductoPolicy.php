<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view productos') ||
            $user->hasPermissionTo('manage productos');
    }

    public function view(User $user, Producto $producto): bool
    {
        return $user->hasPermissionTo('view productos') ||
            $user->hasPermissionTo('manage productos');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage productos');
    }

    public function update(User $user, Producto $producto): bool
    {
        return $user->hasPermissionTo('manage productos');
    }

    public function delete(User $user, Producto $producto): bool
    {
        return $user->hasPermissionTo('manage productos');
    }
}
