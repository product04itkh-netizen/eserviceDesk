<?php

use App\Models\Auth\User;
use Illuminate\Database\Seeder;

/**
 * Class UserRoleTableSeeder.
 */
class UserRoleTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // The master admin also gets service-admin/stock-admin: the app's
        // Service/Purchase/Sale modules check for those specific roles
        // in-controller (isServiceAdmin() etc.), separate from the broader
        // 'administrator' role, so without this the seeded admin account
        // would fail every Service-module role check despite being "admin".
        User::find(1)->assignRole([
            config('access.users.admin_role'),
            config('access.users.service_admin_role'),
            config('access.users.stock_admin_role'),
        ]);
        User::find(2)->assignRole('executive');
        User::find(3)->assignRole(config('access.users.default_role'));

        $this->enableForeignKeys();
    }
}
