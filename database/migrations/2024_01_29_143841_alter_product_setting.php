<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->enum('stocking_system_type', ['fifo', 'averaging'])->default('averaging');
            $table->dropColumn('smtp_host');
            $table->dropColumn('port');
            $table->dropColumn('username');
            $table->dropColumn('password');
            $table->dropColumn('host');
            $table->dropColumn('user');
            $table->dropColumn('pass');
        });


        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('qty_sold', 22, 4)->default(0)->change();
            $table->decimal('qty_adjusted', 22, 4)->default(0)->change();
            $table->decimal('qty_return', 22, 4)->default(0)->change();
            $table->decimal('qty_transfer', 22, 4)->default(0)->change();
            $table->decimal('qty_expire', 22, 4)->default(0)->change();
            $table->decimal('qty_adjusted_add', 22, 4)->default(0)->after('qty_adjusted');
            $table->enum('publish', ['publish', 'draft', 'void'])->default('draft')->after('unit_qty');
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
