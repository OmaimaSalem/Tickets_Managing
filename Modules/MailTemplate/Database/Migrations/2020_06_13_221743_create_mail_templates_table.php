<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('mail_templates', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('title');
          $table->string('subject');
          $table->longText('body');
          $table->integer('created_by')->unsigned();
          $table->integer('mail_template_category_id');
          $table->integer('updated_by')->unsigned()->nullable();
          $table->timestamps();

          // add foreign key
          $table->foreign('mail_template_category_id')->references('id')->on('mail_templates_categories')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
          $table->foreign('created_by')->references('id')->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

          $table->foreign('updated_by')->references('id')->on('users')
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
        Schema::dropIfExists('mail_templates');
    }
}
