<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentsColumnDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dateTime('date')->nullable();
        });

        $payments = App\Payment::withTrashed()->get();

        foreach ($payments as $payment) {
            $payment->date = $payment->created_at;
            $payment->save();
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dateTime('date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
}
