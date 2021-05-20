<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('setting_id')->unsigned();
            $table->integer('offer_id')->unsigned();

            // add foreign key
            $table->foreign('setting_id')->references('id')->on('setting')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('offer_id')->references('id')->on('offers')
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
        Schema::table('contracts', function (Blueprint $table) {

        });
    }
}
