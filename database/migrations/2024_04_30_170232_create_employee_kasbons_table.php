<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeKasbonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_kasbons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('method_id')->index();
            $table->unsignedBigInteger('store_id')->index();
            $table->decimal('amount',22,4)->default(0);
            $table->decimal('pay',22,4)->default(0);
            $table->decimal('due',22,4)->default(0); 
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('kasbon_id')->index()->nullable()->after('expense_id');
            $table->unsignedBigInteger('salary_id')->index()->nullable()->after('kasbon_id');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_kasbons');
    }
}
