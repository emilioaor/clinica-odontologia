<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MarkCommissionsAsPayed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $patientHistory = \App\PatientHistory::query()
            ->where('created_at', '<=', '2020-09-30 23:59:59')
            ->get()
        ;

        foreach ($patientHistory as $history) {
            $history->mark_as_payed = true;
            $history->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $patientHistory = \App\PatientHistory::query()
            ->where('created_at', '<=', '2020-09-30 23:59:59')
            ->get()
        ;

        foreach ($patientHistory as $history) {
            $history->mark_as_payed = false;
            $history->save();
        }
    }
}
