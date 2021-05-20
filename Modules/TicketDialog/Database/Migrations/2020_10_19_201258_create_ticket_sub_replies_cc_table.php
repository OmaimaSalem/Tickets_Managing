<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketSubRepliesCcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_sub_replies_cc', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sub_reply')->unsigned();
            $table->string('email');
            $table->integer('created_by')->unsigned()->default(1);
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            // add foreign key
            $table->foreign('created_by')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');

            $table->foreign('updated_by')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');

            $table->foreign('sub_reply')->references('id')->on('ticket_sub_replies')
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
        Schema::dropIfExists('ticket_sub_replies_cc');
    }
}
