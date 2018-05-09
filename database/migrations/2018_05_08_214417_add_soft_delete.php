<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('patient_history', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('call_logs', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('patient_history', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
