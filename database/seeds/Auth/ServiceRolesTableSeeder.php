<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class ServiceRolesTableSeeder.
 *
 * Seeds the roles actually used in production (config/access.php,
 * ServiceController's role checks, and the eService/stock module views) that
 * PermissionRoleTableSeeder never created. These were previously only ever
 * created ad hoc through the live Role-management UI, so a fresh environment
 * built from the seeders alone was missing them entirely.
 */
class ServiceRolesTableSeeder extends Seeder
{
    /**
     * Run the database seed.
     */
    public function run()
    {
        // All 'admin.*' routes require the 'view backend' permission
        // (see the 'admin' middleware group in app/Http/Kernel.php), so every
        // role below needs it or its users would be locked out entirely.
        $viewBackend = Permission::firstOrCreate(['name' => 'view backend']);

        $roleNames = [
            config('access.users.service_admin_role'),
            config('access.users.service_receptionist_role'),
            config('access.users.service_enginner_role'),
            config('access.users.stock_admin_role'),
            config('access.users.stock_purchase_role'),
            config('access.users.stock_saleorder_role'),
        ];

        foreach ($roleNames as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->givePermissionTo($viewBackend);
        }
    }
}
