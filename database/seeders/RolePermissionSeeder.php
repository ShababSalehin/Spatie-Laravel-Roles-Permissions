<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'admin']);

        $permissions = [
            ['name' => 'user list'],
            ['name' => 'create user'],
            ['name' => 'edit user'],
            ['name' => 'delete user'],
            ['name' => 'role list'],
            ['name' => 'create role'],
            ['name' => 'edit role'],
            ['name' => 'delete role'],
        ];

        foreach ($permissions as $item) {
            Permission::create($item);
        }

        $role->syncPermissions(Permission::all());

        $user = User::first(); // Retrieve the first user from the database
        $user->assignRole($role); // Assign the 'admin' role to the user
    }
}
