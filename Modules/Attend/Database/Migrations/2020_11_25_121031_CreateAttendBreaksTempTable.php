<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendBreaksTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attend_breaks_temp', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('attend_id');
            $table->foreign('attend_id')->references('id')->on('attends')->onDelete('cascade');
            $table->time('from');
            $table->time('to')->nullable();
            $table->double('break_time')->nullable();
            $table->softDeletes();
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
        Schema::drop('attend_breaks_temp');
    }
}
