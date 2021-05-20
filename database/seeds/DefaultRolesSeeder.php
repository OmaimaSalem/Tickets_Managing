<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Carbon\Carbon;

class DefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::truncate();
        Role::truncate();
        $permissions = permissions();
        $roles_with_permissions = default_roles_with_permissions();
        $user = User::first();

        foreach ($permissions as $keys => $permissions_list) {
            foreach($permissions_list as $permission) {
                if(Permission::where('name',$permission)->count() <= 0) {
                    Permission::create([
                       'name' => $permission,
                       'created_by' => $user->id,
                       'created_at' => Carbon::now()
                    ]);
                   }
            }
        }

        foreach($roles_with_permissions as $role => $permissions) {
            if(Role::where('name',$role)->count() <= 0) {
                    $db_role = Role::create([
                        'name' => $role,
                        'created_by' => $user->id,
                        'created_at' => Carbon::now()
                    ]);
                    $db_role->syncPermissions($permissions);
            }else {
                $getRole = Role::where('name',$role)->first();
                $getRole->syncPermissions($permissions);
            }
        }
        $user->assignRole('Full Administrator');


    }
}
