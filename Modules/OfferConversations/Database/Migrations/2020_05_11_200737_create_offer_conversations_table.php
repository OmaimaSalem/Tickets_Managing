<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestamps();

            $table->integer('offer_id')->unsigned();
            $table->longText('conversation');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();

            // add foreign key
            $table->foreign('created_by')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');

            $table->foreign('updated_by')->references('id')->on('users')
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
        Schema::dropIfExists('offer_conversations');
    }
}
