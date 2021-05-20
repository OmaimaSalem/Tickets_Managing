<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailTemplatesAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('mail_templates_attachments', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('mail_template_id')->unsigned();
        $table->string('attachment_path')->unique();
        $table->integer('created_by')->unsigned();
        $table->integer('updated_by')->unsigned()->nullable();
        $table->timestamps();

        // add foreign key
        $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

        $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

        $table->foreign('mail_template_id')->references('id')->on('mail_templates')
                ->onDelete('restrict')
                ->onUpdate('restrict');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_templates_attachments');
    }
}
