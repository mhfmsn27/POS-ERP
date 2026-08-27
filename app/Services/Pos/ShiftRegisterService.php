<?php

namespace App\Services\Pos;

use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Transaction\ShiftRegister;
use App\Models\Transaction\ShiftRegisterTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftRegisterService
{
    /**
     * Menutup shift kasir dengan audit uang fisik dan pembuatan jurnal selisih kas otomatis.
     *
     * @param int $shiftId
     * @param float $physicalCashCount
     * @param string|null $notes
     * @param int|null $userId
     * @return array
     */
    public function closeShiftWithAudit(int $shiftId, float $physicalCashCount, ?string $notes = null, ?int $userId = null): array
    {
        return DB::transaction(function () use ($shiftId, $physicalCashCount, $notes, $userId) {
            $shift = ShiftRegister::withoutGlobalScopes()->findOrFail($shiftId);

            if ($shift->status === 'close') {
                return ['status' => false, 'message' => 'Shift kasir ini sudah ditutup sebelumnya.'];
            }

            // 1. Rekapitulasi Aliran Kas Shift
            $openingCash = (float)ShiftRegisterTransaction::where('shift_register_id', $shiftId)
                ->where('transaction_type', 'opening')
                ->sum('amount');

            $cashSales = (float)ShiftRegisterTransaction::where('shift_register_id', $shiftId)
                ->where('transaction_type', 'sell')
                ->where('pay_method', 'cash')
                ->sum('amount');

            $cashRefunds = (float)ShiftRegisterTransaction::where('shift_register_id', $shiftId)
                ->where('transaction_type', 'refund')
                ->where('pay_method', 'cash')
                ->sum('amount');

            $cashExpenses = (float)ShiftRegisterTransaction::where('shift_register_id', $shiftId)
                ->where('transaction_type', 'expense')
                ->where('pay_method', 'cash')
                ->sum('amount');

            $bankSales = (float)ShiftRegisterTransaction::where('shift_register_id', $shiftId)
                ->where('transaction_type', 'sell')
                ->where('pay_method', 'bank')
                ->sum('amount');

            $otherSales = (float)ShiftRegisterTransaction::where('shift_register_id', $shiftId)
                ->where('transaction_type', 'sell')
                ->where('pay_method', 'other')
                ->sum('amount');

            // Total Kas Seharusnya di Laci (Expected Cash Float)
            $expectedCash = $openingCash + $cashSales - $cashRefunds - $cashExpenses;
            $cashDifference = $physicalCashCount - $expectedCash;

            // 2. Jika Terdapat Selisih Kas (Over/Short), Catat Jurnal Penyesuaian
            $diffJournalRef = null;
            if (abs($cashDifference) > 0.01) {
                $storeId = $shift->store_id;
                $settings = AccountSetting::withoutGlobalScopes()->where('store_id', $storeId)->first()
                    ?? AccountSetting::withoutGlobalScopes()->whereNull('store_id')->first();

                // Cari akun Kas Toko & Akun Selisih Kas
                $cashAccount = null;
                if ($settings && !empty($settings->cash_account)) {
                    $cashAccount = is_numeric($settings->cash_account)
                        ? Account::withoutGlobalScopes()->find($settings->cash_account)
                        : $settings->cash_account;
                }
                if (!$cashAccount) {
                    $cashAccount = Account::withoutGlobalScopes()
                        ->where(function ($q) {
                            $q->where('sub_type', 'cash')
                              ->orWhere('name', 'like', '%Kas%');
                        })
                        ->first();
                }

                $diffAccount = Account::withoutGlobalScopes()
                    ->where(function ($q) {
                        $q->where('name', 'like', '%Selisih Kas%')
                          ->orWhere('name', 'like', '%Cash Short%');
                    })
                    ->first();

                if (!$diffAccount) {
                    if ($cashDifference < 0) {
                        $diffAccountId = $settings ? ($settings->beban_lainnya ?? null) : null;
                        $diffAccount = $diffAccountId ? Account::withoutGlobalScopes()->find($diffAccountId) : Account::withoutGlobalScopes()->where('sub_type', 'expense')->first();
                    } else {
                        $diffAccountId = $settings ? ($settings->pendapatan_lainnya ?? null) : null;
                        $diffAccount = $diffAccountId ? Account::withoutGlobalScopes()->find($diffAccountId) : Account::withoutGlobalScopes()->where('sub_type', 'revenue')->first();
                    }
                }

                if ($cashAccount && $diffAccount) {
                    $diffJournalRef = 'DIFF-SHIFT-' . $shiftId . '-' . date('Ymd');
                    $createdBy = $userId ?? auth()->id() ?? 1;

                    if ($cashDifference < 0) {
                        // Kas Kurang (Shortage) -> Beban Selisih Kas (Debit), Kas Toko (Kredit)
                        $shortageAmount = abs($cashDifference);
                        AccountTransaction::create([
                            'account_id'     => $diffAccount->id,
                            'created_by'     => $createdBy,
                            'amount'         => $shortageAmount,
                            'type'           => 'debit',
                            'sub_type'       => 'cash_shortage',
                            'ref_no'         => $diffJournalRef,
                            'operation_date' => date('Y-m-d'),
                            'name'           => "Selisih Kurang Kas Shift #{$shiftId} - Kasir",
                        ]);
                        AccountTransaction::create([
                            'account_id'     => $cashAccount->id,
                            'created_by'     => $createdBy,
                            'amount'         => $shortageAmount,
                            'type'           => 'credit',
                            'sub_type'       => 'cash_shortage',
                            'ref_no'         => $diffJournalRef,
                            'operation_date' => date('Y-m-d'),
                            'name'           => "Penyesuaian Selisih Kurang Kas Shift #{$shiftId}",
                        ]);
                    } else {
                        // Kas Lebih (Overage) -> Kas Toko (Debit), Pendapatan Selisih Kas (Kredit)
                        $overageAmount = $cashDifference;
                        AccountTransaction::create([
                            'account_id'     => $cashAccount->id,
                            'created_by'     => $createdBy,
                            'amount'         => $overageAmount,
                            'type'           => 'debit',
                            'sub_type'       => 'cash_overage',
                            'ref_no'         => $diffJournalRef,
                            'operation_date' => date('Y-m-d'),
                            'name'           => "Penyesuaian Selisih Lebih Kas Shift #{$shiftId}",
                        ]);
                        AccountTransaction::create([
                            'account_id'     => $diffAccount->id,
                            'created_by'     => $createdBy,
                            'amount'         => $overageAmount,
                            'type'           => 'credit',
                            'sub_type'       => 'cash_overage',
                            'ref_no'         => $diffJournalRef,
                            'operation_date' => date('Y-m-d'),
                            'name'           => "Pendapatan Selisih Lebih Kas Shift #{$shiftId}",
                        ]);
                    }
                }
            }

            // 3. Perbarui Status Shift Register
            $shift->close_amount          = $physicalCashCount;
            $shift->physical_cash_count   = $physicalCashCount;
            $shift->expected_cash_amount  = $expectedCash;
            $shift->cash_difference       = $cashDifference;
            $shift->closing_notes         = $notes;
            $shift->other_amount          = $bankSales + $otherSales;
            $shift->status                = 'close';
            $shift->closed_at             = now();
            $shift->save();

            Log::info("Shift #{$shiftId} closed. Physical: {$physicalCashCount}, Expected: {$expectedCash}, Difference: {$cashDifference}");

            // 4. Kirim Laporan Z-Report ke WhatsApp Owner/Manager via CRMHUB Omnichannel
            try {
                app(\App\Services\Crm\OmnichannelReceiptService::class)->sendShiftZReportToManager($shiftId);
            } catch (\Throwable $waEx) {
                Log::warning("Auto-send Z-Report WA alert failed: " . $waEx->getMessage());
            }

            return [
                'status'               => true,
                'shift_id'             => $shiftId,
                'opening_cash'         => $openingCash,
                'cash_sales'           => $cashSales,
                'non_cash_sales'       => $bankSales + $otherSales,
                'total_refunds'        => $cashRefunds,
                'total_expenses'       => $cashExpenses,
                'expected_cash'        => $expectedCash,
                'physical_cash_count'  => $physicalCashCount,
                'cash_difference'      => $cashDifference,
                'diff_journal_ref'     => $diffJournalRef,
                'message'              => 'Shift kasir berhasil ditutup dan laporan Z-Report telah dibuat.'
            ];
        });
    }

    /**
     * Menghasilkan struktur data Laporan Z-Report Kasir untuk cetak thermal.
     *
     * @param int $shiftId
     * @return array
     */
    public function generateZReport(int $shiftId): array
    {
        $shift = ShiftRegister::withoutGlobalScopes()->with(['user:id,name', 'store:id,name,address,phone'])->findOrFail($shiftId);

        $txCount = ShiftRegisterTransaction::where('shift_register_id', $shiftId)->count();
        $sellCount = ShiftRegisterTransaction::where('shift_register_id', $shiftId)->where('transaction_type', 'sell')->count();

        return [
            'z_report_number'      => 'Z-' . str_pad($shift->id, 6, '0', STR_PAD_LEFT),
            'store_name'           => $shift->store->name ?? 'POS Store',
            'store_address'        => $shift->store->address ?? '',
            'store_phone'          => $shift->store->phone ?? '',
            'cashier_name'         => $shift->user->name ?? 'Kasir',
            'opened_at'            => $shift->created_at ? $shift->created_at->format('d/m/Y H:i:s') : '-',
            'closed_at'            => $shift->closed_at ? date('d/m/Y H:i:s', strtotime($shift->closed_at)) : '-',
            'total_transactions'   => $txCount,
            'sales_count'          => $sellCount,
            'opening_cash'         => (float)$shift->open_amount,
            'expected_cash'        => (float)($shift->expected_cash_amount ?: $shift->open_amount),
            'physical_cash'        => (float)($shift->physical_cash_count ?: $shift->close_amount),
            'cash_difference'      => (float)($shift->cash_difference ?: 0),
            'non_cash_total'       => (float)$shift->other_amount,
            'status'               => $shift->status,
        ];
    }
}
