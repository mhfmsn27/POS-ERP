<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaymentMethodAccountId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->enum('service', ['yes', 'no'])->default('yes')->after('name');
            $table->decimal('amount', 22, 4)->default(0)->after('service');
            $table->unsignedBigInteger('account_id')->index()->nullable()->after('amount');
            $table->softDeletes()->after('account_id');
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
