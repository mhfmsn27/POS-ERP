<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("account_id")->index();
            $table->unsignedBigInteger("created_by")->index();
            $table->unsignedBigInteger("transaction_payment_id")->index()->nullable();
            $table->unsignedBigInteger("transaction_id")->index()->nullable();
            $table->unsignedBigInteger("transaction_transfer_id")->index()->nullable();
            $table->decimal("amount", 22,4)->default(0);
            $table->enum("type", ["credit", "debit"])->default("credit");
            $table->string("ref_no", 50)->nullable();
            $table->string("sub_type", 50)->nullable();
            $table->string("operation_date")->nullable();
            $table->text("note")->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('account_transactions');
    }
}
