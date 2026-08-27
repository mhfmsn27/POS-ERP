<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRmaRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rma_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rma_transactions_id')->index();
            $table->unsignedBigInteger('rma_detail_id')->index()->nullable();
            $table->string('subject');
            $table->enum('type', ['taken', 'complete', 'process', 'note'])->default('note');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('rma_records');
    }
}
