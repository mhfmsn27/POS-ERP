<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnExpenseTypeCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger("category_id")->nullable()->change();
            $table->unsignedBigInteger("sales_commission_id")->index()->nullable()->after("payment_status");
            $table->enum("type", ["commission", "expense"])->default("expense")->after("sales_commission_id");
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
