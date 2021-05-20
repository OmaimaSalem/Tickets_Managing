<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Modules\Category\Entities\Category;
use Carbon\Carbon;

class addMissingPermissions extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
      $permissions = [
          'category-list',
          'category-create',
          'category-edit',
          'category-delete',
          'item-list',
          'item-create',
          'item-edit',
          'item-delete',
          'offer-list',
          'offer-create',
          'offer-edit',
          'offer-delete',
          'contract-list',
          'contract-create',
          'contract-edit',
          'contract-delete',
          'projectComment-list',
          'projectComment-create',
          'projectComment-edit',
          'projectComment-delete',
          'ticketComment-list',
          'ticketComment-create',
          'ticketComment-edit',
          'ticketComment-delete',
          'taskComment-list',
          'taskComment-create',
          'taskComment-edit',
          'taskComment-delete',
          'wiki-list',
          'report-list',
          'manage-attendees',
          'hr-assistant',
          'hr',
          'mail-template'

      ];

      $admin = User::where('name', 'admin')->firstOrFail();

      foreach ($permissions as $permission) {
          Permission::create([
              'name' => $permission,
              'created_by' => $admin->id,
              'created_at' => Carbon::now()
          ]);
      }

      // add Permission to admin role
      $role = Role::where('name', 'admin')->firstOrFail();
      $role->givePermissionTo($permissions);
  }
}
