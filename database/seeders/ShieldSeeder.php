<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissionsJson = '[{"name":"super_admin","guard_name":"web","permissions":["view_role", "create_role", "update_role", "delete_role"]}]';
        $directPermissionsJson = '[]';

        $this->createRolesWithPermissions($rolesWithPermissionsJson);
        // $this->createDirectPermissions($directPermissionsJson);

        $this->command->info('Shield Seeding Completed.');
    }

    protected function createRolesWithPermissions(string $rolesWithPermissionsJson): void
    {
        $rolesWithPermissions = json_decode($rolesWithPermissionsJson, true);

        if (empty($rolesWithPermissions)) {
            return;
        }

        $roleModel = Utils::getRoleModel();
        $permissionModel = Utils::getPermissionModel();
        $adminUser = User::where('is_admin', 1)->first();

        foreach ($rolesWithPermissions as $roleData) {
            $role = $roleModel::firstOrCreate([
                'name' => $roleData['name'],
                'guard_name' => $roleData['guard_name'],
            ]);

            if ($adminUser && $roleData['name'] === 'super_admin') {
                $adminUser->assignRole($role);
            }


            if ($role->name == 'super_admin') {
                $permissions = collect($roleData['permissions'])->map(function ($permission) use ($permissionModel, $roleData) {
                    return $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $roleData['guard_name'],
                    ]);
                });

                $role->syncPermissions($permissions);
            }
        }
    }

    protected function createDirectPermissions(string $directPermissionsJson): void
    {
        $directPermissions = json_decode($directPermissionsJson, true);
        if (empty($directPermissions)) {
            return;
        }

        $permissionModel = Utils::getPermissionModel();

        foreach ($directPermissions as $permissionData) {
            $permissionModel::firstOrCreate([
                'name' => $permissionData['name'],
                'guard_name' => $permissionData['guard_name'],
            ]);
        }
    }
}
