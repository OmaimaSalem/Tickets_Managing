<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderFieldToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('order_column')->unsigned()->default(0);
        });

        // $order_column = 1;
        // foreach(\App\Models\Ticket::all() as $ticket) {
        //     $ticket->order_column =  $order_column;
        //     $ticket->save();
        //     $order_column++;
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['order_column']);
        });
    }
}
