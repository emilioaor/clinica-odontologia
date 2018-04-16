<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePatientHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->integer('assistant_id')->unsigned();
            $table->string('tooth')->nullable();
            $table->float('price');
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('doctor_id')->references('id')->on('users');
            $table->foreign('assistant_id')->references('id')->on('users');
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
        Schema::dropIfExists('patient_history');
    }
}
