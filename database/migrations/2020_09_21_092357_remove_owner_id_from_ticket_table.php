<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOwnerIdFromTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            \DB::statement("INSERT INTO owner_ticket (ticket_id,owner_id) (SELECT id, owner_id FROM tickets where owner_id <> 0)");
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
        Schema::table('tickets', function (Blueprint $table) {
		    $table->integer('owner_id')->unsigned();
        });

    }
}
