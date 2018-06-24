<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnQtyInPatientHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_history', function (Blueprint $table) {
            $table->addColumn('integer', 'qty')->nullable();
            $table->addColumn('float', 'unit_price')->nullable();
        });

        $patientHistory = \App\PatientHistory::withTrashed()->get();

        foreach ($patientHistory as $history) {
            $history->qty = 1;
            $history->unit_price = $history->price;
            $history->save();
        }

        Schema::table('patient_history', function (Blueprint $table) {
            $table->integer('qty')->nullable(false)->change();
            $table->float('unit_price')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_history', function (Blueprint $table) {
            $table->dropColumn('qty');
            $table->dropColumn('unit_price');
        });
    }
}
