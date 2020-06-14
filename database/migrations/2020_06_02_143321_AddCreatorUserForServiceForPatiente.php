<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatorUserForServiceForPatiente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_history', function (Blueprint $table){
            $table->integer('creator_user')->unsigned()->nullable();
            $table->foreign('creator_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_history', function (Blueprint $table){
            $table->dropForeign('creator_user');
            $table->dropColumn('creator_user');
        });
    }
}
