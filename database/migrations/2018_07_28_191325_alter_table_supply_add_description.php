<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSupplyAddDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supply_request_details', function (Blueprint $table) {
            $table->addColumn('string', 'description', ['length' => 255])->nullable();
            $table->integer('supply_id')->unsigned()->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supply_request_details', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->integer('supply_id')->unsigned()->nullable()->change();
        });
    }
}
