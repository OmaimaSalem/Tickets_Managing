<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class RemoveOwnerIdFromProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            \DB::statement("INSERT INTO owner_project (project_id, owner_id) SELECT id, owner_id FROM projects");
            $table->dropColumn(['owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
		    $table->integer('owner_id')->unsigned();
        });
    }
}
