<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('total_before_tax', 22, 4)->default(0)->change();
            $table->string('status')->nullable()->change();
            $table->timestamp('due_date')->nullable()->after('transaction_date');
            $table->string('supplier_ref')->after('ref_no')->nullable();
            $table->decimal('tax_final', 22, 4)->default(0)->after('supplier_ref');
            $table->decimal('discount_final',22,4)->default(0)->after('tax_final');
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
