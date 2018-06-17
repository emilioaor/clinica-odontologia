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
            $table->foreign('patient_reference_id')->references('id')->on('patient_references');
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
            $table->dropForeign('patients_patient_reference_id_foreign');
            $table->dropColumn('patient_reference_id');
        });
    }
}
