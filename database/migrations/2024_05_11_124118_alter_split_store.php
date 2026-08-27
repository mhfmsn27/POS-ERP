<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSplitStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('unit_type')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('image')->nullable();
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('image')->nullable();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('change_price')->nullable();
        });

        Schema::table('raks', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('rak')->nullable();
        });

        Schema::table('account_types', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('default')->nullable();
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('tax_status')->nullable();
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('automatic_sync')->nullable();
        });

        Schema::table('term_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('default')->nullable();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('detail')->nullable();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('tax_default')->nullable();
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('image')->nullable();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('name')->nullable();
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('department_id')->nullable();
        });  

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('max_commission')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('store_id')->nullable()->change();
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
