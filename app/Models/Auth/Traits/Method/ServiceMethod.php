<?php

namespace App\Models\Auth\Traits\Method;

/**
 * Trait RoleMethod.
 */
trait ServiceMethod
{
    /**
     * @return mixed
     */

    // ->roles()->get() builds and runs a brand new query every call — with
    // these 4 methods each called several times per row while rendering a
    // DataTables page (action_buttons/edit_button/delete_button/assign_button
    // all check isServiceX()), that's 10+ identical, uncached queries per
    // row. ->roles (property access) lets Eloquent cache the relation on
    // auth()->user()'s already-request-cached instance, so it's one query
    // total per request instead of one per call. Same result either way —
    // the logged-in user's own role set doesn't change mid-request.
    public function isAdmin()
    {

        return auth()->user()->roles->first()->name===config('access.users.admin_role');

    }

    public function isServiceAdmin()
    {
        return auth()->user()->roles->first()->name===config('access.users.service_admin_role');
    }

    public function isServiceReceptionist()
    {
        return auth()->user()->roles->first()->name === config('access.users.service_receptionist_role');
    }

    public function isServiceEnginner()
    {
        return auth()->user()->roles->first()->name === config('access.users.service_enginner_role');
    }

}
