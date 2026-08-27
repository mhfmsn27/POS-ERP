<?php

namespace App\Services\Analytics;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use App\Models\Admin\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ExecutiveBriefingService
{
    /**
     * Mengompilasi data ringkasan eksekutif harian (Executive Business Briefing).
     *
     * @param int $storeId
     * @param string|null $date Format Y-m-d (default kemarin)
     * @return array
     */
    public function compileDailyBriefing(int $storeId, ?string $date = null): array
    {
        $targetDate = $date ?? now()->subDay()->format('Y-m-d');

        // 1. Total Penjualan & Faktur
        $trxQuery = DB::table('transactions')
            ->where('store_id', $storeId)
            ->where('status', 'final')
            ->whereDate('transaction_date', $targetDate);

        $totalOmzet   = (float) (clone $trxQuery)->sum('final_total');
        $totalOrders  = (int) (clone $trxQuery)->count();

        // 2. Total Kas Masuk per Metode Pembayaran
        $payments = DB::table('transaction_payments')
            ->join('transactions', 'transactions.id', '=', 'transaction_payments.transaction_id')
            ->where('transactions.store_id', $storeId)
            ->whereDate('transactions.transaction_date', $targetDate)
            ->select('transaction_payments.method', DB::raw('sum(transaction_payments.amount) as total'))
            ->groupBy('transaction_payments.method')
            ->pluck('total', 'method');

        $cashInflow = (float)($payments['cash'] ?? 0);
        $qrisInflow = (float)($payments['qris'] ?? $payments['midtrans'] ?? 0);

        // 3. Top 5 Produk Terlaris
        $topProducts = DB::table('sells')
            ->join('transactions', 'transactions.id', '=', 'sells.transaction_id')
            ->join('products', 'products.id', '=', 'sells.product_id')
            ->where('transactions.store_id', $storeId)
            ->where('transactions.status', 'final')
            ->whereDate('transactions.transaction_date', $targetDate)
            ->select('products.name', DB::raw('sum(sells.qty) as total_qty'), DB::raw('sum(sells.qty * sells.unit_price) as total_sales'))
            ->groupBy('products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        // 4. Perhitungan Estimasi Gross Profit (Laba Kotor)
        $cogs = (float) DB::table('sells')
            ->join('transactions', 'transactions.id', '=', 'sells.transaction_id')
            ->join('variations', 'variations.id', '=', 'sells.variation_id')
            ->where('transactions.store_id', $storeId)
            ->where('transactions.status', 'final')
            ->whereDate('transactions.transaction_date', $targetDate)
            ->sum(DB::raw('sells.qty * COALESCE(variations.purchase_price, 0)'));

        $grossProfit = max(0, $totalOmzet - $cogs);

        // 5. Deteksi Anomali / Fraud Kasir
        $anomaliesCount = 0;
        if (Schema::hasTable('cashier_fraud_audit_logs')) {
            $anomaliesCount = DB::table('cashier_fraud_audit_logs')
                ->where('store_id', $storeId)
                ->whereDate('detected_at', $targetDate)
                ->count();
        }

        return [
            'store_id'        => $storeId,
            'date'            => $targetDate,
            'total_omzet'     => $totalOmzet,
            'gross_profit'    => $grossProfit,
            'total_orders'    => $totalOrders,
            'cash_inflow'     => $cashInflow,
            'qris_inflow'     => $qrisInflow,
            'top_products'    => $topProducts->toArray(),
            'anomalies_count' => $anomaliesCount,
        ];
    }

    /**
     * Mengirimkan WhatsApp Daily Morning Executive Briefing ke Owner / Direktur.
     *
     * @param int $storeId
     * @param string $ownerPhone
     * @param string|null $date
     * @return array
     */
    public function sendMorningBriefing(int $storeId, string $ownerPhone, ?string $date = null): array
    {
        $data = $this->compileDailyBriefing($storeId, $date);
        $store = Store::withoutGlobalScopes()->find($storeId);
        $storeName = $store->name ?? 'POSHUB Store';

        $cleanPhone = preg_replace('/[^0-9]/', '', $ownerPhone);
        $formattedDate = date('d F Y', strtotime($data['date']));

        // Susun Top 5 Items string
        $topListStr = "";
        $rank = 1;
        foreach ($data['top_products'] as $item) {
            $topListStr .= "  {$rank}. {$item->name} : *{$item->total_qty} pcs* (Rp " . number_format($item->total_sales) . ")\n";
            $rank++;
        }
        if (empty($topListStr)) {
            $topListStr = "  (Belum ada transaksi)\n";
        }

        $fraudAlert = ($data['anomalies_count'] > 0)
            ? "⚠️ *Catatan Anomali Kasir*: {$data['anomalies_count']} insiden terdeteksi."
            : "✅ *Integritas Kasir*: Bersih (0 anomali).";

        $message = "☕ *EXECUTIVE MORNING BRIEFING* ☕\n"
            . "🏢 *{$storeName}*\n"
            . "🗓️ Periode: *{$formattedDate}*\n\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "📊 *RINGKASAN PENJUALAN*\n"
            . "• Total Omzet : *Rp " . number_format($data['total_omzet']) . "*\n"
            . "• Laba Kotor  : *Rp " . number_format($data['gross_profit']) . "*\n"
            . "• Total Faktur: *{$data['total_orders']} Transaksi*\n"
            . "• Kas Tunai   : Rp " . number_format($data['cash_inflow']) . "\n"
            . "• Non-Tunai   : Rp " . number_format($data['qris_inflow']) . "\n\n"
            . "🏆 *TOP PRODUK TERLARIS*\n"
            . $topListStr . "\n"
            . "🛡️ *STATUS KEAMANAN*\n"
            . $fraudAlert . "\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "Semoga bisnis hari ini berjalan lancar dan sukses!";

        try {
            SendWhatsappDigitalReceiptJob::dispatch($cleanPhone, $message);

            if (Schema::hasTable('executive_briefing_logs')) {
                DB::table('executive_briefing_logs')->insert([
                    'store_id'          => $storeId,
                    'briefing_date'     => $data['date'],
                    'total_omzet'       => $data['total_omzet'],
                    'gross_profit'      => $data['gross_profit'],
                    'cash_inflow'       => $data['cash_inflow'],
                    'qris_inflow'       => $data['qris_inflow'],
                    'top_products_json' => json_encode($data['top_products']),
                    'anomalies_count'   => $data['anomalies_count'],
                    'recipient_phone'   => $cleanPhone,
                    'sent_at'           => now(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            return [
                'status'          => true,
                'recipient_phone' => $cleanPhone,
                'briefing_date'   => $data['date'],
                'message'         => "Executive briefing untuk tanggal {$data['date']} berhasil dikirim ke Owner."
            ];
        } catch (\Throwable $e) {
            Log::warning("Executive briefing WA error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
