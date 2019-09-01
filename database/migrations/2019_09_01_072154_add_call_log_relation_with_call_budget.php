<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallLogRelationWithCallBudget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->integer('patient_id')->unsigned()->nullable()->change();
            $table->integer('call_budget_id')->unsigned()->nullable();
            $table->foreign('call_budget_id')->references('id')->on('call_budgets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->integer('patient_id')->unsigned()->nullable(false)->change();
            $table->dropForeign('call_logs_call_budget_id_foreign');
            $table->dropColumn('call_budget_id');
        });
    }
}
