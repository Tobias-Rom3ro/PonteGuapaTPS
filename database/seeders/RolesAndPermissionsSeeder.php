<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::create(['name' => 'administrador']);
        $empleadoRole = Role::create(['name' => 'empleado']);
        $usuarioRole = Role::create(['name' => 'usuario']);

        // Crear permisos de usuarios
        $manageProductos = Permission::create(['name' => 'manage productos']);
        $viewProductos = Permission::create(['name' => 'view productos']);
        $viewAnyUsers = Permission::create(['name' => 'view any users']);
        $viewUser = Permission::create(['name' => 'view user']);
        $createUser = Permission::create(['name' => 'create user']);
        $updateUser = Permission::create(['name' => 'update user']);
        $deleteUser = Permission::create(['name' => 'delete user']);
        $editOwnProfile = Permission::create(['name' => 'edit own profile']);

        // Permisos para tipos de servicios
        $manageTipoServicios = Permission::create(['name' => 'manage tipo servicios']);
        $viewTipoServicios = Permission::create(['name' => 'view tipo servicios']);

        // Permisos para servicios
        $manageServicios = Permission::create(['name' => 'manage servicios']);
        $viewServicios = Permission::create(['name' => 'view servicios']);

        // Asignar permisos a roles
        $adminRole->givePermissionTo([
            $viewAnyUsers, $viewUser, $createUser, $updateUser, $deleteUser, $editOwnProfile,
            $manageTipoServicios, $viewTipoServicios, $manageServicios, $viewServicios ,$manageProductos, $viewProductos

        ]);

        $empleadoRole->givePermissionTo([
            $editOwnProfile, $viewTipoServicios, $viewServicios, $viewProductos
        ]);

        $usuarioRole->givePermissionTo([
            $editOwnProfile, $viewTipoServicios, $viewServicios, $viewProductos
        ]);

        // Crear usuarios por defecto
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@ponteguapa.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);

        $empleado = User::create([
            'name' => 'Empleado',
            'email' => 'empleado@ponteguapa.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $empleado->assignRole($empleadoRole);

        $usuario = User::create([
            'name' => 'Usuario',
            'email' => 'usuario@ponteguapa.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $usuario->assignRole($usuarioRole);
    }
}
