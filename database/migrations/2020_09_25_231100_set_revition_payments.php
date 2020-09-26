<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetRevitionPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $payments = \App\Payment::query()
            ->where('date', '<=', '2020-09-15 23:59:59')
            ->get()
        ;

        foreach ($payments as $payment) {
            $payment->checked_in_ticket = true;
            $payment->save();
        }
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
