<?php

use Illuminate\Database\Seeder;
use Modules\ItemCategory\Entities\ItemCategory;
use Carbon\Carbon;


class AddDefaultItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      ItemCategory::create([
        'name' => 'Default',
        'description' => 'Default category',
        'created_by' => 1,
        'created_at' => Carbon::now()
      ]);
    }
}
