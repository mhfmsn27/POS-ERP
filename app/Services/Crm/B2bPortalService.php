<?php

namespace App\Services\Crm;

use App\Models\Admin\Customer;
use App\Models\Product\Supplier;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class B2bPortalService
{
    /**
     * Mengambil profil lengkap & saldo piutang untuk Portal Pelanggan B2B.
     *
     * @param int $customerId
     * @param int $storeId
     * @return array
     */
    public function getCustomerB2bProfile(int $customerId, int $storeId): array
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return ['status' => false, 'message' => 'Data pelanggan B2B tidak ditemukan.'];
        }

        // Hitung total piutang aktif
        $unpaidInvoices = Transaction::withoutGlobalScopes()
            ->with(['sell', 'sell.product'])
            ->where('customer_id', $customerId)
            ->where('store_id', $storeId)
            ->where('type', 'sell')
            ->whereIn('status', ['due', 'partial'])
            ->latest()
            ->get();

        $totalOutstanding = (float)$unpaidInvoices->sum('final_total');
        $creditLimit = (float)($customer->credit_limit ?? 50000000); // Default plafon kredit B2B
        $availableCredit = max(0, $creditLimit - $totalOutstanding);

        $invoiceList = [];
        foreach ($unpaidInvoices as $inv) {
            $invoiceList[] = [
                'transaction_id' => $inv->id,
                'invoice_no'     => $inv->ref_no ?? ('INV-' . $inv->id),
                'date'           => date('d/m/Y', strtotime($inv->transaction_date ?? $inv->created_at)),
                'due_date'       => date('d/m/Y', strtotime(($inv->transaction_date ?? $inv->created_at) . ' +30 days')),
                'total_amount'   => (float)$inv->final_total,
                'status'         => strtoupper($inv->status),
                'download_url'   => url('/pos-admin/prints/faktur-penjualan/' . $inv->id),
                'payment_url'    => url('/web/checkout/pay/' . $inv->id)
            ];
        }

        return [
            'status'            => true,
            'customer'          => [
                'id'            => $customer->id,
                'name'          => $customer->name,
                'company'       => $customer->company_name ?? $customer->name,
                'phone'         => $customer->phone,
                'email'         => $customer->email,
                'loyalty_points'=> $customer->total_points ?? 0
            ],
            'financials'        => [
                'credit_limit'     => $creditLimit,
                'total_outstanding'=> $totalOutstanding,
                'available_credit' => $availableCredit,
                'utilization_pct'  => $creditLimit > 0 ? round(($totalOutstanding / $creditLimit) * 100, 1) : 0,
            ],
            'pending_invoices'  => $invoiceList
        ];
    }

    /**
     * Mengambil data PO dan tagihan untuk Portal Supplier / Vendor.
     *
     * @param int $supplierId
     * @param int $storeId
     * @return array
     */
    public function getVendorPortalData(int $supplierId, int $storeId): array
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return ['status' => false, 'message' => 'Data supplier/vendor tidak ditemukan.'];
        }

        // Ambil Purchase Orders
        $orders = Transaction::withoutGlobalScopes()
            ->with(['sell', 'sell.product'])
            ->where('supplier_id', $supplierId)
            ->where('store_id', $storeId)
            ->where('type', 'purchase')
            ->latest()
            ->get();

        $activePo = [];
        $totalPayable = 0;

        foreach ($orders as $po) {
            if (in_array($po->status, ['due', 'partial'])) {
                $totalPayable += (float)$po->final_total;
            }

            $activePo[] = [
                'po_id'          => $po->id,
                'po_number'      => $po->ref_no ?? ('PO-' . $po->id),
                'date'           => date('d/m/Y', strtotime($po->transaction_date ?? $po->created_at)),
                'status'         => strtoupper($po->status),
                'total_amount'   => (float)$po->final_total,
                'items_count'    => $po->sell->count(),
                'download_po_url'=> url('/pos-admin/prints/pembayaran-pembelian/' . $po->id)
            ];
        }

        return [
            'status'            => true,
            'supplier'          => [
                'id'            => $supplier->id,
                'name'          => $supplier->name,
                'contact_person'=> $supplier->contact_person ?? $supplier->name,
                'phone'         => $supplier->phone,
                'email'         => $supplier->email
            ],
            'total_payable'     => $totalPayable,
            'purchase_orders'   => $activePo
        ];
    }

    /**
     * Konfirmasi kesiapan / pengiriman barang oleh Supplier (e-POD Dispatch).
     *
     * @param int $poId
     * @param string $resiTracking
     * @param string|null $driverInfo
     * @return array
     */
    public function confirmVendorDispatch(int $poId, string $resiTracking, ?string $driverInfo = null): array
    {
        $po = Transaction::withoutGlobalScopes()->where('id', $poId)->where('type', 'purchase')->first();
        if (!$po) {
            return ['status' => false, 'message' => 'PO tidak ditemukan.'];
        }

        $notes = "[VENDOR DISPATCHED] Resi: {$resiTracking} | Info Driver: " . ($driverInfo ?: '-');
        $po->additional_notes = trim($po->additional_notes . "\n" . $notes);
        $po->save();

        return [
            'status'        => true,
            'po_id'         => $po->id,
            'tracking_no'   => $resiTracking,
            'message'       => 'Konfirmasi pengiriman berhasil dicatat. Status PO diperbarui.'
        ];
    }
}
