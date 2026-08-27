<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftRegisterTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_register_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_register_id')->index();
            $table->decimal('amount',22,4)->default(0);
            $table->enum("pay_method",["cash","bank","cheque","other"])->default("cash");
            $table->enum("transaction_type",["sell","opening","refund","expense","other"])->default("sell");
            $table->unsignedBigInteger('transaction_id')->index()->nullable();
            $table->timestamps();

            $table->foreign('shift_register_id')->references('id')->on('shift_registers'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_register_transactions');
    }
}
