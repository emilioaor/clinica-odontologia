<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecretaryIdCallLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_logs', function (Blueprint $table){
            $table->integer('secretary_id')->unsigned()->nullable();
            $table->foreign('secretary_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_logs', function (Blueprint $table){
            $table->dropForeign('call_logs_secretary_id_foreign');
            $table->dropColumn('secretary_id');
        });
    }
}
