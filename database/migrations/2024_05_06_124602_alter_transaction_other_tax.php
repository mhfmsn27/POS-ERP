<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionOtherTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->decimal('goverment_tax', 22, 4)->default(0)->after('tax_total');
            $table->decimal('service_tax', 22, 4)->default(0)->after('goverment_tax');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('goverment_tax', 22, 4)->default(0)->after('shipping_alocation');
            $table->decimal('service_tax', 22, 4)->default(0)->after('goverment_tax');
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
