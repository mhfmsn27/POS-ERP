<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullSpectrumEnterpriseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Aset Tetap (Fixed Assets)
        if (!Schema::hasTable('fixed_assets')) {
            Schema::create('fixed_assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->string('name', 150);
                $table->string('code', 50)->unique();
                $table->string('category', 50)->default('equipment'); // equipment, building, vehicle, machinery
                $table->date('acquisition_date');
                $table->decimal('acquisition_cost', 15, 2)->default(0);
                $table->decimal('salvage_value', 15, 2)->default(0);
                $table->integer('useful_life_months')->default(48); // misal 4 tahun
                $table->enum('depreciation_method', ['straight_line', 'declining_balance'])->default('straight_line');
                $table->unsignedBigInteger('asset_account_id')->nullable()->index(); // Akun Aset Tetap di COA
                $table->unsignedBigInteger('depreciation_account_id')->nullable()->index(); // Akun Beban Penyusutan
                $table->unsignedBigInteger('accumulated_account_id')->nullable()->index(); // Akun Akumulasi Penyusutan
                $table->decimal('current_book_value', 15, 2)->default(0);
                $table->enum('status', ['active', 'fully_depreciated', 'disposed'])->default('active')->index();
                $table->timestamps();
            });
        }

        // 2. Log Penyusutan Bulanan Aset (Fixed Asset Depreciations)
        if (!Schema::hasTable('fixed_asset_depreciations')) {
            Schema::create('fixed_asset_depreciations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id')->index();
                $table->date('depreciation_date')->index();
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('book_value_after', 15, 2)->default(0);
                $table->unsignedBigInteger('account_transaction_id')->nullable()->index();
                $table->timestamps();
            });
        }

        // 3. Pagu Anggaran Departemen vs Realisasi (Department Budgets)
        if (!Schema::hasTable('department_budgets')) {
            Schema::create('department_budgets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('department_id')->nullable()->index();
                $table->integer('period_year')->index();
                $table->integer('period_month')->nullable()->index();
                $table->unsignedBigInteger('account_id')->index(); // Akun Beban di COA
                $table->decimal('budget_amount', 15, 2)->default(0);
                $table->decimal('actual_spent', 15, 2)->default(0);
                $table->timestamps();

                $table->index(['store_id', 'period_year', 'account_id'], 'idx_dept_budget_lookup');
            });
        }

        // 4. Bill of Materials (BOM) Resep Produksi
        if (!Schema::hasTable('bill_of_materials')) {
            Schema::create('bill_of_materials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('finished_product_id')->index();
                $table->unsignedBigInteger('finished_variation_id')->nullable()->index();
                $table->string('name', 150);
                $table->decimal('yield_quantity', 10, 2)->default(1);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // 5. Item Bahan Baku BOM (BOM Items)
        if (!Schema::hasTable('bill_of_materials_items')) {
            Schema::create('bill_of_materials_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bom_id')->index();
                $table->unsignedBigInteger('raw_product_id')->index();
                $table->unsignedBigInteger('raw_variation_id')->nullable()->index();
                $table->decimal('quantity', 10, 4)->default(1);
                $table->decimal('unit_cost', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        // 6. Surat Perintah Kerja Produksi (Manufacturing Work Orders)
        if (!Schema::hasTable('manufacturing_work_orders')) {
            Schema::create('manufacturing_work_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('bom_id')->index();
                $table->string('order_no', 50)->unique();
                $table->decimal('target_quantity', 10, 2)->default(1);
                $table->decimal('actual_quantity', 10, 2)->default(0);
                $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft')->index();
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // 7. Kalkulasi Pajak PPh 21 TER (PP 58/2023) & BPJS (Payroll Compliance)
        if (!Schema::hasTable('payroll_tax_calculations')) {
            Schema::create('payroll_tax_calculations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->integer('period_month')->index();
                $table->integer('period_year')->index();
                $table->decimal('gross_salary', 15, 2)->default(0);
                $table->string('ptkp_status', 10)->default('TK/0'); // TK/0, K/0, K/1, K/2, K/3
                $table->enum('ter_category', ['A', 'B', 'C'])->default('A');
                $table->decimal('ter_rate_percent', 5, 2)->default(0);
                $table->decimal('pph21_amount', 15, 2)->default(0);
                $table->decimal('bpjs_tk_employee', 15, 2)->default(0);
                $table->decimal('bpjs_tk_company', 15, 2)->default(0);
                $table->decimal('bpjs_kes_employee', 15, 2)->default(0);
                $table->decimal('bpjs_kes_company', 15, 2)->default(0);
                $table->decimal('net_take_home_pay', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        // 8. Pelacakan Serial Number / IMEI Per Unit (Product Serial Numbers)
        if (!Schema::hasTable('product_serial_numbers')) {
            Schema::create('product_serial_numbers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->nullable()->index();
                $table->string('serial_number', 100)->unique();
                $table->enum('status', ['in_stock', 'sold', 'rma_repair', 'defective'])->default('in_stock')->index();
                $table->unsignedBigInteger('transaction_id')->nullable()->index();
                $table->date('warranty_expires_at')->nullable()->index();
                $table->timestamps();
            });
        }

        // 9. Lokasi Rak/Bin/Lorong Gudang (Warehouse Bin Locations)
        if (!Schema::hasTable('warehouse_bin_locations')) {
            Schema::create('warehouse_bin_locations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('warehouse_id')->nullable()->index();
                $table->string('zone', 30)->default('A');
                $table->string('aisle', 30)->default('01');
                $table->string('rack', 30)->default('01');
                $table->string('shelf', 30)->default('01');
                $table->string('bin_code', 50)->unique();
                $table->string('description', 150)->nullable();
                $table->timestamps();
            });
        }

        // 10. Tiket Servis, Garansi & RMA (RMA Service Tickets)
        if (!Schema::hasTable('rma_service_tickets')) {
            Schema::create('rma_service_tickets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->string('ticket_no', 50)->unique();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('customer_name', 100);
                $table->string('customer_phone', 30);
                $table->string('serial_number', 100)->nullable()->index();
                $table->string('device_name', 150);
                $table->text('issue_description')->nullable();
                $table->enum('status', [
                    'received', 'diagnosing', 'waiting_parts', 'repairing', 'ready_for_pickup', 'completed', 'cancelled'
                ])->default('received')->index();
                $table->decimal('estimated_cost', 15, 2)->default(0);
                $table->decimal('actual_cost', 15, 2)->default(0);
                $table->text('technician_notes')->nullable();
                $table->timestamps();
            });
        }

        // 11. Otomasi Retensi Pelanggan & Voucher Ultah WhatsApp (Customer Retention Campaigns)
        if (!Schema::hasTable('customer_retention_campaigns')) {
            Schema::create('customer_retention_campaigns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->enum('type', ['birthday', 'anniversary', 'win_back'])->default('birthday')->index();
                $table->unsignedBigInteger('customer_id')->index();
                $table->string('customer_phone', 30);
                $table->string('voucher_code', 50)->index();
                $table->decimal('discount_percent', 5, 2)->default(10);
                $table->timestamp('message_sent_at')->nullable();
                $table->boolean('is_redeemed')->default(false)->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_retention_campaigns');
        Schema::dropIfExists('rma_service_tickets');
        Schema::dropIfExists('warehouse_bin_locations');
        Schema::dropIfExists('product_serial_numbers');
        Schema::dropIfExists('payroll_tax_calculations');
        Schema::dropIfExists('manufacturing_work_orders');
        Schema::dropIfExists('bill_of_materials_items');
        Schema::dropIfExists('bill_of_materials');
        Schema::dropIfExists('department_budgets');
        Schema::dropIfExists('fixed_asset_depreciations');
        Schema::dropIfExists('fixed_assets');
    }
}
