<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_payments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('day')->default(0);
            $table->decimal('discount', 22, 4)->default(0);
            $table->float('due_date')->default(0);
            $table->text('note')->nullable();
            $table->enum('default',['yes','no'])->default('no');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_payments');
    }
}
