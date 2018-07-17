<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddDiagnostic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_history', function (Blueprint $table) {
            $table->unsignedInteger('diagnostic_id')->nullable();
            $table->foreign('diagnostic_id')->references('id')->on('users');
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
            $table->dropForeign('patient_history_diagnostic_id_foreign');
            $table->dropColumn('diagnostic_id');
        });
    }
}
