<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionPackagePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_package_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_package_id')->index();
            $table->decimal('amount',22,4)->default(0);
            $table->enum('status',['pending','success'])->default('pending'); 
            $table->string('order_id')->nullable();
            $table->string('token')->nullable();
            $table->string('method')->nullable();
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
        Schema::dropIfExists('transaction_package_payments');
    }
}
