<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SupplyType;
use App\SupplyBrand;
use App\Supply;

class AddRelationBrandAndType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->integer('supply_type_id')->unsigned()->nullable();
            $table->foreign('supply_type_id')->references('id')->on('supply_types');
            $table->integer('supply_brand_id')->unsigned()->nullable();
            $table->foreign('supply_brand_id')->references('id')->on('supply_brands');
            $table->float('width')->nullable();
            $table->float('height')->nullable();
        });

        $type = new SupplyType();
        $type->generatePublicId();
        $type->name = 'Tipo insumo 1';
        $type->save();

        $brand = new SupplyBrand();
        $brand->generatePublicId();
        $brand->name = 'Marca insumo 1';
        $brand->save();

        $supplies = Supply::all();

        foreach ($supplies as $supply) {
            $supply->supply_brand_id = $brand->id;
            $supply->supply_type_id = $type->id;
            $supply->save();
        }

        Schema::table('supplies', function (Blueprint $table) {
            $table->integer('supply_type_id')->unsigned()->nullable(false)->change();
            $table->integer('supply_brand_id')->unsigned()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropForeign('supplies_supply_type_id_foreign');
            $table->dropForeign('supplies_supply_brand_id_foreign');
            $table->dropColumn('supply_type_id');
            $table->dropColumn('supply_brand_id');
            $table->dropColumn('width');
            $table->dropColumn('height');
        });
    }
}
