<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_categories', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamp('created_at');
          $table->timestamp('updated_at')->nullable();
          $table->integer('created_by')->unsigned();
          $table->integer('updated_by')->unsigned()->nullable();
          $table->string('name')->unique();
          $table->longText('description')->nullable();

          // add foreign key
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
        Schema::dropIfExists('item_categories');
    }
}
