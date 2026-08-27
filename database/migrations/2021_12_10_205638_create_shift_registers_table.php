<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("store_id")->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->dateTime('closed_at')->nullable();
            $table->decimal("open_amount", 22, 4)->default(0);
            $table->decimal("close_amount", 22, 4)->default(0);
            $table->enum("status",["open","close"])->default("open");
            $table->decimal("other_amount",22,4)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_registers');
    }
}
