<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBudgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('public_id', 10)->unique();
            $table->string('business_name');
            $table->string('business_logo')->nullable();
            $table->string('title')->nullable();
            $table->string('client_label')->nullable();
            $table->string('client_value');
            $table->string('creation_date_label')->nullable();
            $table->date('creation_date_value')->nullable();
            $table->string('expiration_date_label')->nullable();
            $table->date('expiration_date_value')->nullable();
            $table->string('total_head_label')->nullable();
            $table->float('total_head_value', 10, 2);
            $table->string('discount_label')->nullable();
            $table->integer('discount_type')->nullable();
            $table->float('discount_value')->nullable();
            $table->string('tax_label')->nullable();
            $table->integer('tax_type')->nullable();
            $table->float('tax_value')->nullable();
            $table->string('shaping_label')->nullable();
            $table->float('shaping_value')->nullable();
            $table->string('subtotal_footer_label')->nullable();
            $table->float('subtotal_footer_value', 10, 2);
            $table->string('total_footer_label')->nullable();
            $table->float('total_footer_value', 10, 2);
            $table->string('amount_paid_label')->nullable();
            $table->float('amount_paid_value', 10, 2)->nullable();
            $table->string('notes_label')->nullable();
            $table->text('notes_value')->nullable();
            $table->string('terms_label')->nullable();
            $table->text('terms_value')->nullable();
            $table->string('table_description_label')->nullable();
            $table->string('table_quantity_label')->nullable();
            $table->string('table_price_label')->nullable();
            $table->string('table_total_label')->nullable();
            $table->string('currency_symbol', 3);
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
        Schema::dropIfExists('budgets');
    }
}
