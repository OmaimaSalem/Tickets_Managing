<?php

use Illuminate\Database\Seeder;
use Modules\MailTemplate\Entities\MailTemplateCategory;
use Modules\MailTemplate\Entities\MailTemplate;

class MailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MailTEmplateCategory::create([
          'name' => 'Default',
          'created_by' => '1'
        ]);

        MailTemplate::create([
          'title' => 'Default Title',
          'subject' => 'Default Subject',
          'body' => 'Default Body',
          'mail_template_category_id' => '1',
          'created_by' => '1'
        ]);
    }
}
