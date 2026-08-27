<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartlinkBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smartlink_banks', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->integer('store_id')->index();
            $table->string('type');
            $table->string('rekening');
            $table->integer('account_id')->index();
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
        Schema::dropIfExists('smartlink_banks');
    }
}
