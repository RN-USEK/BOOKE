<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing roles and permissions
        Role::query()->delete();
        Permission::query()->delete();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $userRole = Role::create(['name' => 'user']);

        // Generate permissions
        $this->command->info('Creating permissions...');
        
        $permissions = [
            'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',
            'view_any_book', 'view_book', 'create_book', 'update_book', 'delete_book',
            // Add any other permissions you need
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to admin role
        $adminRole->givePermissionTo(Permission::all());

        // Assign book-related permissions to manager role
        $managerRole->givePermissionTo(
            Permission::where('name', 'like', '%book%')->get()
        );

        // Create or update users
        $this->createOrUpdateUser('Admin', 'admin@booke.com', 'a', 'admin');
        $this->createOrUpdateUser('Manager', 'manager@booke.com', 'm', 'manager');
        $this->createOrUpdateUser('User', 'user@booke.com', 'u', 'user');

        $this->command->info('Roles and users created/updated successfully.');
    }

    private function createOrUpdateUser($name, $email, $password, $role)
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password)
            ]
        );

        $user->syncRoles($role);

        $this->command->info("User {$name} created or updated.");
    }
}