<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->integer('price');
            $table->integer('tax')->default(0.19);
            $table->integer('tax_price');
            $table->integer('qty');
            $table->integer('total_price');
            $table->integer('total');
            $table->string('category')->nullable();

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->integer('contract_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();

            // add foreign key
            $table->foreign('contract_id')->references('id')->on('contracts')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('created_by')->references('id')->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('updated_by')->references('id')->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_items');
    }
}
