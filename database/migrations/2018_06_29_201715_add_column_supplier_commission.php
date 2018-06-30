<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSupplierCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->addColumn('boolean', 'doctor_commission')->default(false);
        });

        $supplier = new \App\Supplier();
        $supplier->nextPublicId();
        $supplier->name = 'Comision de doctores';
        $supplier->phone = '0000000000';
        $supplier->email = 'doctorcommission@mail.com';
        $supplier->type = \App\Supplier::TYPE_DOCTOR_COMMISSION;
        $supplier->doctor_commission = true;
        $supplier->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('doctor_commission');
        });
    }
}
