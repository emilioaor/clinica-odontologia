<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSupplyInventoryMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_inventory_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_movement_id')->unsigned();
            $table->foreign('inventory_movement_id')->references('id')->on('inventory_movements');
            $table->integer('supply_id')->unsigned();
            $table->foreign('supply_id')->references('id')->on('supplies');
            $table->float('qty');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supply_inventory_movements');
    }
}
