<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAllDatabaseMerchantId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->after('timezone')->nullable();
            $table->enum('role_type', ['administrator', 'user'])->after('merchant_id')->default('user');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->default(1);
        });

        Schema::table('taxrates', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->after('taxrate')->default(1);
        });

        Schema::table('settings_hrms', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->after('salary_tax')->default(1);
        });

        Schema::table('printers', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->after('char_per_line')->default(1);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->after('guard_name')->default(1);
        });

        Schema::table('key_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->after('expense_payment_key')->default(1);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->index()->default(1)->after('accountant_use');
            $table->unsignedBigInteger('package_id')->index()->after('merchant_id');
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
