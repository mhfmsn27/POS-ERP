<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("transaction_id")->index();
            $table->unsignedBigInteger("commission_contact_id")->index();
            $table->enum("commission_contact_type", ["none", "agent", "user", "employee"])->default("none");
            $table->decimal("commission_total", 22, 4)->default(0);
            $table->decimal("commission_percentase", 22, 4)->default(0);
            $table->decimal("commission_total_return", 22, 4)->default(0);
            $table->enum("status",["due","pay"])->default("due"); 
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
        Schema::dropIfExists('sales_commissions');
    }
}
