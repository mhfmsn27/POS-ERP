<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSupplierAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('code');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->enum('is_account',['yes','no'])->default('no')->after('address');
            $table->unsignedBigInteger('term_payment')->index()->nullable()->after('is_account');
            $table->unsignedBigInteger('debt')->index()->nullable()->after('term_payment');
            $table->unsignedBigInteger('debt_imprest')->index()->nullable()->after('debt');
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
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
