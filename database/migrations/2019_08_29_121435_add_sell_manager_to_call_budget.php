<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSellManagerToCallBudget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_budgets', function (Blueprint $table) {
            $table->integer('sell_manager_id')->unsigned()->nullable();
            $table->foreign('sell_manager_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_budgets', function (Blueprint $table) {
            $table->dropForeign('call_budgets_sell_manager_id_foreign');
            $table->dropColumn('sell_manager_id');
        });
    }
}
