<?php

namespace App\Policies;

use App\Models\TipoServicio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoServicioPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view tipo servicios') ||
            $user->hasPermissionTo('manage tipo servicios');
    }

    public function view(User $user, TipoServicio $tipoServicio): bool
    {
        return $user->hasPermissionTo('view tipo servicios') ||
            $user->hasPermissionTo('manage tipo servicios');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage tipo servicios');
    }

    public function update(User $user, TipoServicio $tipoServicio): bool
    {
        return $user->hasPermissionTo('manage tipo servicios');
    }

    public function delete(User $user, TipoServicio $tipoServicio): bool
    {
        return $user->hasPermissionTo('manage tipo servicios');
    }
}
