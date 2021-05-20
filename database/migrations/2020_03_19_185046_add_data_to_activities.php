<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('offer_id')->unsigned()->nullable();
            $table->integer('contract_id')->unsigned()->nullable();

            // add foreign key
            $table->foreign('item_id')->references('id')->on('items')
            ->onDelete('restrict')
            ->onUpdate('restrict');

            $table->foreign('category_id')->references('id')->on('categories')
            ->onDelete('restrict')
            ->onUpdate('restrict');

            $table->foreign('offer_id')->references('id')->on('offers')
            ->onDelete('restrict')
            ->onUpdate('restrict');

            $table->foreign('contract_id')->references('id')->on('contracts')
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
        Schema::table('activities', function (Blueprint $table) {
            //
        });
    }
}
