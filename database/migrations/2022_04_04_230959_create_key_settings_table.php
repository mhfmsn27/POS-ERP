<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_settings', function (Blueprint $table) {
            $table->id();
            $table->string("purchase_key",100)->default("PO")->nullable();
            $table->string("purchase_return_key",100)->default("PO_RTN")->nullable();
            $table->string("sell_key",100)->default("SL")->nullable();
            $table->string("sell_return_key",100)->default("SL_RTN")->nullable();
            $table->string("adjustment_key",100)->default("AS")->nullable();
            $table->string("stock_transfer_key",100)->default("ST")->nullable();
            $table->string("expense_key",100)->default("EP")->nullable();
            $table->string("purchase_payment_key",100)->default("PO_PAY")->nullable();
            $table->string("sell_payment_key",100)->default("SL_PAY")->nullable();
            $table->string("expense_payment_key",100)->default("EP_PAY")->nullable();
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
        Schema::dropIfExists('key_settings');
    }
}
