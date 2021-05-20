<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageAndSickVacationToVacationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_vactions', function (Blueprint $table) {
            $table->enum('sick_vacation',['true','false'])->default('false');
            $table->string('sick_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_vactions', function (Blueprint $table) {
            $table->dropColumn(['sick_vacation','sick_image']);
        });
    }
}
