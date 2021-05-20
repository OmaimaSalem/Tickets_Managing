<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCalendarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->date('date');
            $table->time('from')->nullable();
            $table->time('to')->nullable();
            $table->boolean('all_day')->default(0);
            $table->string('location');
            $table->string('description');
            $table->unsignedBigInteger('creator_id');
            $table->enum('status',['shown','hidden'])->default('shown');
            $table->string('day_name',10)->nullable();
            $table->boolean('repeat')->nullable();
            $table->enum('repeat_type',['daily','weakly','monthly'])->nullable();
            $table->date('end_at')->nullable();
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
        Schema::dropIfExists('calendar');
    }
}
