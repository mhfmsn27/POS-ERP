<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccountTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->string('name')->nullable()->after('note');
            $table->enum('after_rekonsiliasi', ['yes', 'no'])->default('no')->after('name');
            $table->unsignedBigInteger('account_transaction_id')->index()->nullable()->after('after_rekonsiliasi');
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
