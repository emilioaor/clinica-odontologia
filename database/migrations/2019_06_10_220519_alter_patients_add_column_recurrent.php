<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientsAddColumnRecurrent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('recurrent')->default(false);
        });

        $patientHistory = \App\PatientHistory::all();
        foreach ($patientHistory as $history) {
            $threeWeeksAfter = clone $history->created_at;
            $threeWeeksAfter->modify('+3 weeks');

            $isRecurrent = \App\PatientHistory::query()
                ->where('patient_id', $history->patient_id)
                ->where('created_at', '>=', $threeWeeksAfter)
                ->count();

            if ($isRecurrent) {
                $history->patient->recurrent = true;
                $history->patient->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('recurrent');
        });
    }
}
