<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPublicIdPatientHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_history', function (Blueprint $table) {
            $table->addColumn('string', 'public_id', ['length' => 30])->unique()->nullable();
        });

        $patientHistory = \App\PatientHistory::all();
        $c = 1;

        foreach ($patientHistory as $history) {

            $history->public_id = 'SER' . $c;
            $history->save();

            $c++;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_history', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
}
