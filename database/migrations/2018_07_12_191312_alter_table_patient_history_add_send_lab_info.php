<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePatientHistoryAddSendLabInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_history', function (Blueprint $table) {
            $table->addColumn('integer', 'supplier_id')->unsigned()->nullable();
            //$table->foreign('supplier_id')->references('id')->on('suppliers')->change();
            $table->addColumn('integer', 'responsible_id')->unsigned()->nullable();
            //$table->foreign('responsible_id')->references('id')->on('users')->change();
            $table->date('send_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
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
            //$table->dropForeign('patient_history_supplier_id_foreign');
            //$table->dropForeign('patient_history_responsible_id_foreign');
            $table->dropColumn('supplier_id');
            $table->dropColumn('responsible_id');
            $table->dropColumn('send_date');
            $table->dropColumn('delivery_date');
        });
    }
}
