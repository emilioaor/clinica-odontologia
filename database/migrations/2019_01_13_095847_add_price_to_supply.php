<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToSupply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->float('price')->default(0);
        });

        $supplies = \App\Supply::all();

        foreach ($supplies as $supply) {
            if ($supply->width === null) {
                $supply->width = 0;
            }

            if ($supply->height === null) {
                $supply->height = 0;
            }

            $supply->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
}
