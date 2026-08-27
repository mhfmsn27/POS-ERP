<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesCommissionAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_commission_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name',50); 
            $table->string('email',50)->nullable();
            $table->string('phone',50)->nullable();
            $table->longText('address')->nullable(); 
            $table->char("commission_percentase")->default(0); 
            $table->char("max_commission")->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_commission_agents');
    }
}
