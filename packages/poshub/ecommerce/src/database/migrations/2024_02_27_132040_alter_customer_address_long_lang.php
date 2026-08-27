<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustomerAddressLongLang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->string('long')->nullable()->after('phone');
            $table->string('lang')->nullable()->after('long');
        });

        Schema::table('transactions', function (Blueprint $table) { 
            $table->string('no_rek')->after('type_sell')->nullable();
        });

        Schema::table('transaction_payments', function (Blueprint $table) { 
            $table->string('no_rek')->after('date')->nullable();
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
