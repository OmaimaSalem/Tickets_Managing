<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastTimeAndNextTimeAndFreqToTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->timestamp('last_time_work')->after('deadline');
            $table->timestamp('next_time_work')->after('last_time_work');
            $table->enum('freq',['daily','weekly','monthly','yearly'])->default('daily');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task', function (Blueprint $table) {

        });
    }
}
