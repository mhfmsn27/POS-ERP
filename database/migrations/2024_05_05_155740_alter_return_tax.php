<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReturnTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_details', function (Blueprint $table) {
            $table->decimal('tax_total',22,4)->default(0)->after('unit_qty'); 
        }); 

        Schema::table('sales_returns', function (Blueprint $table) {
            $table->decimal('tax_total',22,4)->default(0)->after('unit_qty'); 
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
