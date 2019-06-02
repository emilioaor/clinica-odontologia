<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCallBudgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('service');
            $table->dateTime('date');
            $table->string('phone');
            $table->string('email');
            $table->float('amount');
            $table->dateTime('last_call');
            $table->dateTime('next_call')->nullable();
            $table->integer('next_action')->nullable();
            $table->integer('status');
            $table->integer('call_budget_source_id')->unsigned();
            $table->foreign('call_budget_source_id')->references('id')->on('call_budget_sources');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('call_budgets');
    }
}
