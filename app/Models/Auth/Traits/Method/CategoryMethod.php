<?php

namespace App\Models\Auth\Traits\Method;

/**
 * Trait RoleMethod.
 */
trait CategoryMethod
{
    /**
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->name === config('access.users.admin_role');
    }

    public function isStockAdmin()
    {
        return $this->name === config('access.users.stock_admin_role');
    }
}
