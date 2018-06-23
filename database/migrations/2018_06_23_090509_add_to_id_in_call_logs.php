<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToIdInCallLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->addColumn('integer', 'user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });

        $secretary = \App\User::where('level', \App\User::LEVEL_SECRETARY)->first();

        if ($secretary) {

            $callLogs = \App\CallLog::withTrashed()->get();

            foreach ($callLogs as $callLog) {
                $callLog->user_id = $secretary->id;
                $callLog->save();
            }

            Schema::table('call_logs', function (Blueprint $table) {
                $table->integer('user_id')->nullable(false)->unsigned()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropForeign('call_logs_to_id_foreign');
            $table->dropColumn('to_id');
        });
    }
}
