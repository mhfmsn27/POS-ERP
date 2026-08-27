<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekonsiliasiDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekonsiliasi_data', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->integer('account_id')->index();
            $table->string('transaction_account_id')->nullable();
            $table->decimal('amount', 22, 4)->default(0);
            $table->decimal('saldo', 22, 4)->default(0);
            $table->date('date');
            $table->enum('type',['credit','debit'])->default('debit');
            $table->string('note')->nullable();
            $table->enum('status',['yes','no'])->default('no');
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
        Schema::dropIfExists('rekonsiliasi_data');
    }
}
