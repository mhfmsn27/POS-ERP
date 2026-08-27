<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterForAccountingColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->unsignedBigInteger("created_by")->index()->nullable()->after("note")->comment("Alter For Accounting Column");
            $table->enum("transaction_type", ["transaction", "expense"])->default("transaction")->after("created_by")->comment("Alter For Accounting Column");
            $table->unsignedBigInteger("account_id")->index()->nullable()->after("transaction_type")->comment("Alter For Accounting Column");
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
