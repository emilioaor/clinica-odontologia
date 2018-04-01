<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCallStatusHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_status_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('call_log_id')->unsigned();
            $table->foreign('call_log_id')->references('id')->on('call_logs');
            $table->text('note');
            $table->integer('status');
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
        Schema::dropIfExists('call_status_history');
    }
}
