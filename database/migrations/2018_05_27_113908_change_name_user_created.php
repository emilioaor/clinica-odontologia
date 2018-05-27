<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameUserCreated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->addColumn('integer', 'user_created_id')->unsigned()->nullable();
            $table->foreign('user_created_id')->references('id')->on('users');
        });

        $payments = \App\Payment::all();

        foreach ($payments as $payment) {
            $payment->user_created_id = $payment->user_created;
            $payment->save();
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_user_created_foreign');
            $table->dropColumn('user_created');
            $table->integer('user_created_id')->unsigned()->nullable(false)->change();
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
            $table->addColumn('integer', 'user_created')->unsigned()->nullable();
            $table->foreign('user_created')->references('id')->on('users');
        });

        $payments = \App\Payment::all();

        foreach ($payments as $payment) {
            $payment->user_created = $payment->user_created_id;
            $payment->save();
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_user_created_id_foreign');
            $table->dropColumn('user_created_id');
            $table->integer('user_created')->unsigned()->nullable(false)->change();
        });
    }
}
