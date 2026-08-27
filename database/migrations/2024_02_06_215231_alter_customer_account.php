<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustomerAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('is_account', ['yes', 'no'])->default('no')->after('detail');
            $table->unsignedBigInteger('term_payment')->nullable()->after('is_account');
            $table->unsignedBigInteger('debt')->nullable()->after('term_payment');
            $table->unsignedBigInteger('debt_imprest')->nullable()->after('debt');
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
