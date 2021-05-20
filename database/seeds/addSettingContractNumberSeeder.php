<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class addSettingContractNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('name', 'admin')->firstOrFail();

        $setting = Setting::create([
            'entity' => 'contract',
            'key' => 'C1',
            'start_number' => '00000001',
            'last_number' => '00000001',
            'current' => true,
            'created_by' => $admin->id
        ]);
    }
}
