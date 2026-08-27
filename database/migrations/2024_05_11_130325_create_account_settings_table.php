<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index();
            // Customer
            $table->unsignedBigInteger('customer_debt')->nullable();
            $table->unsignedBigInteger('customer_debt_imprest')->nullable();

            // Supplier
            $table->unsignedBigInteger('supplier_debt')->nullable();
            $table->unsignedBigInteger('supplier_debt_imprest')->nullable();

            // Product
            $table->unsignedBigInteger('product_supply')->nullable();
            $table->unsignedBigInteger('product_sale')->nullable();
            $table->unsignedBigInteger('product_retur_sale')->nullable();
            $table->unsignedBigInteger('product_discount_sale')->nullable();
            $table->unsignedBigInteger('product_sent')->nullable();
            $table->unsignedBigInteger('product_cost')->nullable();
            $table->unsignedBigInteger('product_retur_purchase')->nullable();
            $table->unsignedBigInteger('product_supplier_debt')->nullable();
            $table->unsignedBigInteger('cost_shipping_transaction')->nullable(); 

            $table->unsignedBigInteger('salaries')->nullable(); 
            $table->unsignedBigInteger('kasbon')->nullable(); 
            $table->unsignedBigInteger('discount_sale')->index()->nullable();
            $table->unsignedBigInteger('commission')->index()->nullable();

            $table->unsignedBigInteger('tax_input')->nullable(); 
            $table->unsignedBigInteger('tax_output')->nullable();
            $table->unsignedBigInteger('tax_gap')->nullable();
            $table->unsignedBigInteger('pph_two_two')->index()->nullable();
            $table->unsignedBigInteger('pph_two_tree')->index()->nullable();
            $table->unsignedBigInteger('beban_operasional')->index()->nullable();
            $table->unsignedBigInteger('beban_lainnya')->index()->nullable();
            $table->unsignedBigInteger('pendapatan_lainnya')->index()->nullable(); 
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
        Schema::dropIfExists('account_settings');
    }
}
