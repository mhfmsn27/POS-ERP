<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTaxProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('tax_option',['active','no'])->default('no')->after('tax'); 
            $table->decimal('tax_one',22,4)->default(0)->after('tax_option');
            $table->decimal('tax_two',22,4)->default(0)->after('tax_one');
        }); 

        Schema::table('variations', function (Blueprint $table) {
            $table->enum('tax_sell',['yes','no'])->default('no')->after('taxrate');
            $table->enum('tax_purchase',['yes','no'])->default('no')->after('tax_sell'); 
        }); 

        Schema::table('customers', function (Blueprint $table) { 
            $table->string('npwp')->nullable()->after('debt_imprest');
            $table->enum('type',['bumn','general'])->default('general')->after('npwp');
            $table->enum('tax_option',['yes','no'])->default('no')->after('type'); 
            $table->enum('tax_default',['yes','no'])->default('no')->after('tax_option');
        }); 

        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('tax_option',['yes','no'])->default('no')->after('debt_imprest'); 
            $table->enum('tax_default',['yes','no'])->default('no')->after('tax_option');
            $table->string('npwp')->nullable()->after('tax_default'); 
        }); 

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('tax_total',22,4)->default(0)->after('item_tax'); 
        }); 

        Schema::table('sells', function (Blueprint $table) {
            $table->decimal('item_tax',22,4)->default(0)->change(); 
            $table->decimal('tax_total',22,4)->default(0)->after('item_tax'); 
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
