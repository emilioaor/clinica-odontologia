<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PatientAddColumnPatientReferenceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->integer('patient_reference_id')->unsigned()->nullable();
            $table->foreign('patient_reference_id', 'patient_references_foreign_in_patients')->references('id')->on('patient_references');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign('patient_references_foreign_in_patients');
            $table->dropColumn('patient_reference_id');
        });
    }
}
