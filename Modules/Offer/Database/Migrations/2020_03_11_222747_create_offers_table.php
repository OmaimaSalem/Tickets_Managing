<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->unique();
            $table->integer('total');
            $table->date('date');
            $table->string('greeting')->nullable();
            $table->string('street')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('first_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('others')->nullable();
            $table->string('notes')->nullable();
            $table->string('printed_text')->nullable();


            $table->integer('client_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            // add foreign key
            $table->foreign('client_id')->references('id')->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

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
        Schema::dropIfExists('offers');
    }
}
