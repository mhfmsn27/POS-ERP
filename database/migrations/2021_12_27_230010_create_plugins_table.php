<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('ref_no');
            $table->string('plugin_icon')->nullable();
            $table->string('customer_id');
            $table->enum('status',['0','1'])->default('1');
            $table->enum('plugis_type',['free','pay'])->default('free');
            $table->unsignedBigInteger('author_id')->index()->nullable();
            $table->unsignedBigInteger('plugin_id')->index()->nullable();
            $table->text('purchase')->nullable();
            $table->unsignedBigInteger('transaction_id')->index()->nullable();
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
        Schema::dropIfExists('plugins');
    }
}
