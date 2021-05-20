<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_type');
            $table->integer('attribute_id');
            $table->string('attribute_name');
            $table->string('attribute_value');
            $table->longText('email_content');
            $table->string('email_time');
            $table->datetime('mail_start');
            $table->datetime('next_run_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_emails');
    }
}
