<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriorityFieldToUsersVacationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_vactions',function(Blueprint $table) {
            $table->enum('priority' , ['low','medium','high'])->default('low');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_vactions',function(Blueprint $table) {
            $table->dropColumn(['priority']);
        });
    }
}
