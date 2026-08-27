<?php

namespace App\Services\Pos;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KitchenOrderService
{
    /**
     * Mendaftarkan tiket pesanan baru ke Kitchen Display System (KDS).
     *
     * @param int $storeId
     * @param int $transactionId
     * @param array $items
     * @param string|null $tableNumber
     * @param string|null $customerName
     * @param string|null $notes
     * @return array
     */
    public function createTickets(int $storeId, int $transactionId, array $items, ?string $tableNumber = null, ?string $customerName = null, ?string $notes = null): array
    {
        if (!Schema::hasTable('kitchen_order_tickets') || empty($items)) {
            return ['status' => false, 'message' => 'KDS module not active or empty items.'];
        }

        $ticketNo = 'KDS-' . date('His') . '-' . rand(100, 999);

        // Pisahkan item berdasarkan Kitchen vs Bar
        $kitchenItems = [];
        $barItems     = [];

        foreach ($items as $item) {
            $catName = strtolower($item['category_name'] ?? ($item['name'] ?? ''));
            $isBar = (strpos($catName, 'drink') !== false)
                || (strpos($catName, 'minuman') !== false)
                || (strpos($catName, 'kopi') !== false)
                || (strpos($catName, 'tea') !== false)
                || (strpos($catName, 'juice') !== false);

            if ($isBar) {
                $barItems[] = $item;
            } else {
                $kitchenItems[] = $item;
            }
        }

        $createdTickets = [];

        if (!empty($kitchenItems)) {
            $id = DB::table('kitchen_order_tickets')->insertGetId([
                'store_id'        => $storeId,
                'transaction_id'  => $transactionId,
                'ticket_number'   => $ticketNo . '-KT',
                'table_number'    => $tableNumber ?? 'Meja Kasir',
                'customer_name'   => $customerName ?? 'Tamu',
                'station'         => 'kitchen',
                'items_payload'   => json_encode($kitchenItems),
                'status'          => 'pending',
                'notes'           => $notes,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $createdTickets[] = ['id' => $id, 'station' => 'kitchen', 'ticket_number' => $ticketNo . '-KT'];
        }

        if (!empty($barItems)) {
            $id = DB::table('kitchen_order_tickets')->insertGetId([
                'store_id'        => $storeId,
                'transaction_id'  => $transactionId,
                'ticket_number'   => $ticketNo . '-BR',
                'table_number'    => $tableNumber ?? 'Meja Kasir',
                'customer_name'   => $customerName ?? 'Tamu',
                'station'         => 'bar',
                'items_payload'   => json_encode($barItems),
                'status'          => 'pending',
                'notes'           => $notes,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $createdTickets[] = ['id' => $id, 'station' => 'bar', 'ticket_number' => $ticketNo . '-BR'];
        }

        return [
            'status'  => true,
            'tickets' => $createdTickets,
            'message' => 'Pesanan berhasil diteruskan ke layar dapur (KDS).'
        ];
    }

    /**
     * Mengambil tiket pesanan aktif yang sedang diproses di dapur / bar.
     *
     * @param int|null $storeId
     * @param string|null $station
     * @return array
     */
    public function getActiveTickets(?int $storeId = null, ?string $station = null): array
    {
        if (!Schema::hasTable('kitchen_order_tickets')) {
            return ['status' => true, 'tickets' => []];
        }

        $query = DB::table('kitchen_order_tickets')
            ->whereIn('status', ['pending', 'cooking', 'ready']);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        if ($station && $station !== 'all') {
            $query->where('station', $station);
        }

        $tickets = $query->orderBy('id', 'asc')->get();

        $formatted = [];
        foreach ($tickets as $t) {
            $createdAt = \Carbon\Carbon::parse($t->created_at);
            $elapsedMins = (int) $createdAt->diffInMinutes(now());

            $formatted[] = [
                'id'            => $t->id,
                'ticket_number' => $t->ticket_number,
                'table_number'  => $t->table_number,
                'customer_name' => $t->customer_name,
                'station'       => $t->station,
                'status'        => $t->status,
                'items'         => json_decode($t->items_payload, true) ?: [],
                'notes'         => $t->notes,
                'elapsed_mins'  => $elapsedMins,
                'is_urgent'     => $elapsedMins >= 15,
                'created_at'    => $createdAt->format('H:i:s'),
            ];
        }

        return [
            'status'        => true,
            'total_active'  => count($formatted),
            'tickets'       => $formatted,
        ];
    }

    /**
     * Memperbarui status pengerjaan tiket dapur (pending -> cooking -> ready -> served).
     *
     * @param int $ticketId
     * @param string $newStatus
     * @return array
     */
    public function updateTicketStatus(int $ticketId, string $newStatus): array
    {
        if (!Schema::hasTable('kitchen_order_tickets')) {
            return ['status' => false, 'message' => 'Tabel KDS belum aktif.'];
        }

        $ticket = DB::table('kitchen_order_tickets')->where('id', $ticketId)->first();
        if (!$ticket) {
            return ['status' => false, 'message' => 'Tiket dapur tidak ditemukan.'];
        }

        $update = [
            'status'     => $newStatus,
            'updated_at' => now(),
        ];

        if ($newStatus === 'cooking' && !$ticket->started_cooking_at) {
            $update['started_cooking_at'] = now();
        } elseif ($newStatus === 'ready') {
            $update['ready_at'] = now();
        } elseif ($newStatus === 'served') {
            $update['served_at'] = now();
        }

        DB::table('kitchen_order_tickets')->where('id', $ticketId)->update($update);

        return [
            'status'     => true,
            'ticket_id'  => $ticketId,
            'new_status' => $newStatus,
            'message'    => "Status tiket {$ticket->ticket_number} diubah menjadi {$newStatus}."
        ];
    }
}
