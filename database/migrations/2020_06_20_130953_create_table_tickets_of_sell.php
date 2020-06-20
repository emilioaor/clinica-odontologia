<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTicketsOfSell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_of_sell', function (Blueprint $table) {
            $table->increments('id');
            $table->string('public_id', 15)->unique();
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_of_sell_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_of_sell_id')->unsigned();
            $table->foreign('ticket_of_Sell_id')->references('id')->on('tickets_of_sell');
            $table->integer('patient_history_id')->unsigned();
            $table->foreign('patient_history_id')->references('id')->on('patient_history');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_of_sell_details');
        Schema::dropIfExists('tickets_of_sell');
    }
}
