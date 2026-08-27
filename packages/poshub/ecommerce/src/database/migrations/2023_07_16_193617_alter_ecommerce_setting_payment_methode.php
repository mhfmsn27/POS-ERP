<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEcommerceSettingPaymentMethode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ecommerce_api_settings', function (Blueprint $table) {
            $table->enum('payment_method', ['midtrans', 'manual'])->default('midtrans')->after('about_text');
        });

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'success'])->default('success')->after('account_id');
            $table->string('snap_token')->nullable()->after('payment_status');
            $table->timestamp('expire_payment')->nullable()->after('snap_token');
            $table->string('order_id')->nullable()->after('expire_payment');

            $table->string('bank_name')->nullable()->after('order_id');
            $table->string('to_bank')->nullable()->after('bank_name');
            $table->string('file')->nullable()->after('to_bank');
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
