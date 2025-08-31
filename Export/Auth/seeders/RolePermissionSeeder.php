<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'description' => 'Manage permissions in the admin panel', 'icon' => 'bi bi-key'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'description' => 'Manage roles in the admin panel', 'icon' => 'bi bi-person-badge'],
            ['name' => 'Manage Admins', 'slug' => 'manage-admins', 'description' => 'Manage admin users', 'icon' => 'bi bi-people'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        $role = Role::firstOrCreate(
            ['slug' => 'administrator'],
            ['name' => 'Administrator', 'description' => 'Full administrator role', 'is_active' => true]
        );

        // Attach all three permissions to the administrator role
        $allPermissionIds = Permission::whereIn('slug', array_column($permissions, 'slug'))->pluck('id')->toArray();
        $role->permissions()->sync($allPermissionIds);
    }
}
