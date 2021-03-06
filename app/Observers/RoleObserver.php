<?php

namespace App\Observers;

use Spatie\Permission\Models\Role;
use \Illuminate\Http\Request;

class RoleObserver
{
    private $input;

    public function __construct(Request $request)
    {
        $this->input = $request->all();
    }

    /**
     * Handle the role "created" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
        if (isset($this->input['permissions'])) {
            $permissionData = $this->input['permissions'];
            unset($this->input['permissions']);

            // insert permissions for role
            $role->syncPermissions($permissionData);
        }
    }

    /**
     * Handle the role "updated" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {
        if (isset($this->input['permissions'])) {
            $permissions = $this->input['permissions'];
            unset($this->input['permissions']);

            $role->syncPermissions($permissions);
        }
    }

    /**
     * Handle the role "deleted" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
        // get permission of this role
        $permissions = $role->permissions->pluck('name', 'id');

        // revoke(remove) all permission from this role
        if (!empty($permissions)) {
            $role->revokePermissionTo($permissions);
        }
    }

    /**
     * Handle the role "restored" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        //
    }

    /**
     * Handle the role "force deleted" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function forceDeleted(Role $role)
    {
        //
    }
}
