<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDropTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_types', function (Blueprint $table) {
            $table->dropColumn('parent_type_id');
            $table->dropColumn('store_id');
            $table->dropColumn('note');
            $table->enum('edit_option', ['yes', 'no'])->default('no')->after('name');
            $table->string('coa_code')->after('edit_option');
            $table->enum('with_price', ['yes', 'no'])->after('coa_code')->default('yes');
            $table->enum('with_modal', ['yes', 'no'])->after('with_price')->default('yes');
            $table->enum('type', ['bank_cash', 'non_bank_cash'])->default('non_bank_cash')->after('with_modal');
            $table->string('default')->nullable()->after('type');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('is_root_parent', ['yes', 'no'])->default('no')->after('note');
            $table->unsignedBigInteger('parent_id')->index()->after('is_root_parent')->nullable();
            $table->decimal('cashflow', 22, 4)->default(0)->after('parent_id');
            $table->string('coa')->index()->after('id');
            $table->unsignedBigInteger('bank_id')->nullable()->index()->after('coa');
            $table->enum('edit_option', ['yes', 'no'])->default('no')->after('bank_id');
            $table->enum('default_data', ['modal', 'utang_usaha', 'pendapatan_usaha', 'kas_atau_bank', 'piutang_usaha', 'utang_atau_kas', 'akumulasi_penyusutan', 'modal_saham'])->nullable()->after('edit_option');
            $table->dropColumn('number');
        });

        Schema::dropIfExists('account_details');
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
