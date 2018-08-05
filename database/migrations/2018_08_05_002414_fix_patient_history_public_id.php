<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixPatientHistoryPublicId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $services = \App\PatientHistory::query()->whereNull('public_id')->withTrashed()->get();

        $c = 0;

        foreach ($services as $service) {
            $c++;

            $service->public_id = 'SERV' . $c;
            $service->save();
        }

        Schema::table('patient_history', function (Blueprint $table) {
            $table->string('public_id', 30)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
