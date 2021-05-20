<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAttributesFieldsNoneRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attribute_emails', function (Blueprint $table) {
            $table->integer('attribute_id')->nullable()->change();
            $table->string('attribute_name')->nullable()->change();
            $table->string('attribute_value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attribute_emails', function (Blueprint $table) {
            //
        });
    }
}
