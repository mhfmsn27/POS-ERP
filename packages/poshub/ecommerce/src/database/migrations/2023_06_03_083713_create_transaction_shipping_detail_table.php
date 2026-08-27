<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionShippingDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_shipping_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->index();
            $table->string("curir_name");
            $table->string("curir_code");
            $table->string("curir_service");
            $table->string("resi_no")->nullable();
            $table->unsignedBigInteger('to_subdistrict_id')->index();
            $table->text('address_detail')->nullable();
            $table->string("postal_code")->nullable();
            $table->string("phone")->nullable();
            $table->string("name")->nullable();
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
        Schema::dropIfExists('transaction_shipping_detail');
    }
}
