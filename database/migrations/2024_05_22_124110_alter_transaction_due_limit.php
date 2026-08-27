<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionDueLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->char('due_limit')->after('service_tax')->default(0);
            $table->timestamp('due_end')->after('due_limit')->nullable();
        });

        Schema::table('transaction_dues', function (Blueprint $table) {
            $table->char('due_limit')->after('total_due_amount')->default(0);
            $table->timestamp('due_end')->after('due_limit')->nullable();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('accountant_use', ['yes', 'no'])->default('yes')->after('warehouse_default_id');
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
