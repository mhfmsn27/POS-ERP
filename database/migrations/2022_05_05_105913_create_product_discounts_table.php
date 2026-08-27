<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("store_id")->index();
            $table->unsignedBigInteger("variation_id")->index()->nullable();
            $table->enum("type",['multiprice','voucher','custom'])->default('multiprice');
            $table->string("custom_type",100)->nullable();
            $table->enum("on_action",["pcs","price","voucher"])->default("pcs");
            $table->enum("type_bonus",["price","product","shipping"])->default("price");
            $table->enum("type_discount",["for_variant","for_general"])->default("for_variant");
            $table->char("qty_min")->default(0);
            $table->decimal("qty_price")->default(0);
            $table->enum("amount_type",["fix","percentase"])->default("fix");
            $table->decimal("discount_amount")->default(0);
            $table->unsignedBigInteger("get_product")->index()->nullable();
            $table->string("code")->nullable();
            $table->string("voucher_name",150)->nullable();
            $table->enum("voucher_use",["no_limit","limited"])->default("no_limit");
            $table->char("voucher_limit_number")->default(1);
            $table->string("voucher_limit_date")->nullable();
            $table->char("voucher_claim")->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) { 
            $table->unsignedBigInteger("product_discount_id")->index()->nullable()->after("commission_contact_total");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_discounts');
    }
}
