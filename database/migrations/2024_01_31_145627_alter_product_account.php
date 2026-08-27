<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('is_active', ['yes', 'no'])->default('yes')->after('description');
            $table->enum('is_account', ['yes', 'no'])->default('no')->after('is_active');
            $table->unsignedBigInteger('supply')->index()->nullable()->after('is_account');
            $table->unsignedBigInteger('sale')->index()->nullable()->after('supply');
            $table->unsignedBigInteger('retur_sale')->index()->nullable()->after('sale');
            $table->unsignedBigInteger('discount_sale')->index()->nullable()->after('retur_sale');
            $table->unsignedBigInteger('sent')->index()->nullable()->after('discount_sale');
            $table->unsignedBigInteger('cost')->index()->nullable()->after('sent');
            $table->unsignedBigInteger('retur_purchase')->nullable()->after('cost');
            $table->unsignedBigInteger('supplier_debt')->nullable()->after('retur_purchase');
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
