<?php

namespace App\Policies;

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicioPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view servicios') ||
            $user->hasPermissionTo('manage servicios');
    }

    public function view(User $user, Servicio $servicio): bool
    {
        return $user->hasPermissionTo('view servicios') ||
            $user->hasPermissionTo('manage servicios');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage servicios');
    }

    public function update(User $user, Servicio $servicio): bool
    {
        return $user->hasPermissionTo('manage servicios');
    }

    public function delete(User $user, Servicio $servicio): bool
    {
        return $user->hasPermissionTo('manage servicios');
    }
}
