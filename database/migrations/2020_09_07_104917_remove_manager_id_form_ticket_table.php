<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveManagerIdFormTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            \DB::statement("INSERT INTO manager_ticket (ticket_id,manager_id) (SELECT id, manager_id FROM tickets where manager_id <> 0)");
            $table->dropColumn(['manager_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
		    $table->integer('manager_id')->unsigned();
        });
    }
}
