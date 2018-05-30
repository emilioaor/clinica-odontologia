<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableExpensesRemovePatientId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_patient_id_foreign');
            $table->dropColumn('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->addColumn('integer', 'patient_id')->nullable()->unsigned();
            $table->foreign('patient_id')->references('id')->on('patients');
        });
    }
}
