<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("voucher_id")->index();
            $table->unsignedBigInteger("transaction_id")->index();
            $table->string("type",100)->nullable();
            $table->string("name",150)->nullable();
            $table->decimal("amount",22,4)->default(0);
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
        Schema::dropIfExists('voucher_claims');
    }
}
