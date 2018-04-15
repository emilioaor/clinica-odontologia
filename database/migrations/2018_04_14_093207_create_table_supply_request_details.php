<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSupplyRequestDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_request_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supply_request_id')->unsigned();
            $table->foreign('supply_request_id')->references('id')->on('supply_requests');
            $table->integer('supply_id')->unsigned();
            $table->foreign('supply_id')->references('id')->on('supplies');
            $table->integer('qty');
            $table->integer('approved')->nullable();
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
        Schema::dropIfExists('supply_request_details');
    }
}
