<?php

namespace App\Services\Crm;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class RmaServiceTrackerService
{
    /**
     * Membuat Tiket Klaim Servis & Garansi Baru.
     *
     * @param int $storeId
     * @param string $customerName
     * @param string $customerPhone
     * @param string $deviceName
     * @param string|null $serialNumber
     * @param string $issueDescription
     * @param float $estimatedCost
     * @param int|null $customerId
     * @return array
     */
    public function createTicket(
        int $storeId,
        string $customerName,
        string $customerPhone,
        string $deviceName,
        ?string $serialNumber,
        string $issueDescription,
        float $estimatedCost = 0,
        ?int $customerId = null
    ): array {
        if (!Schema::hasTable('rma_service_tickets')) {
            return ['status' => false, 'message' => 'Tabel tiket servis belum aktif.'];
        }

        $ticketNo = 'SRV-' . date('Ymd') . '-' . rand(100, 999);

        $id = DB::table('rma_service_tickets')->insertGetId([
            'store_id'          => $storeId,
            'ticket_no'         => $ticketNo,
            'customer_id'       => $customerId,
            'customer_name'     => $customerName,
            'customer_phone'    => $customerPhone,
            'serial_number'     => $serialNumber,
            'device_name'       => $deviceName,
            'issue_description' => $issueDescription,
            'status'            => 'received',
            'estimated_cost'    => $estimatedCost,
            'actual_cost'       => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Kirim konfirmasi penerimaan unit ke WhatsApp Pelanggan
        try {
            $msg = "Halo Kak *{$customerName}*,\n\n"
                . "Unit *{$deviceName}* telah kami terima di Service Center kami.\n\n"
                . "🎫 No. Tiket: *{$ticketNo}*\n"
                . "⚠️ Kendala: {$issueDescription}\n"
                . "💰 Estimasi Biaya: Rp " . number_format($estimatedCost) . "\n\n"
                . "Kami akan mengabari Anda kembali saat diagnosa selesai.\n"
                . "Terima kasih!";
            SendWhatsappDigitalReceiptJob::dispatch($customerPhone, $msg);
        } catch (\Throwable $e) {
            Log::warning("RMA WA Notification Error: " . $e->getMessage());
        }

        return [
            'status'    => true,
            'ticket_id' => $id,
            'ticket_no' => $ticketNo,
            'message'   => "Tiket servis {$ticketNo} berhasil dibuat."
        ];
    }

    /**
     * Memperbarui status pengerjaan servis dan mengirim notifikasi WhatsApp otomatis ke pelanggan.
     *
     * @param int $ticketId
     * @param string $newStatus
     * @param string|null $technicianNotes
     * @param float|null $actualCost
     * @return array
     */
    public function updateTicketStatus(int $ticketId, string $newStatus, ?string $technicianNotes = null, ?float $actualCost = null): array
    {
        if (!Schema::hasTable('rma_service_tickets')) {
            return ['status' => false, 'message' => 'Tabel tiket servis belum aktif.'];
        }

        $ticket = DB::table('rma_service_tickets')->where('id', $ticketId)->first();
        if (!$ticket) {
            return ['status' => false, 'message' => 'Tiket servis tidak ditemukan.'];
        }

        $update = [
            'status'     => $newStatus,
            'updated_at' => now(),
        ];

        if ($technicianNotes !== null) {
            $update['technician_notes'] = $technicianNotes;
        }
        if ($actualCost !== null) {
            $update['actual_cost'] = $actualCost;
        }

        DB::table('rma_service_tickets')->where('id', $ticketId)->update($update);

        // Status Label Bahasa Indonesia
        $statusLabels = [
            'diagnosing'        => 'Sedang Dalam Tahap Diagnosa',
            'waiting_parts'     => 'Menunggu Pengiriman Spare Part',
            'repairing'         => 'Sedang Dalam Proses Perbaikan',
            'ready_for_pickup'  => 'SELESAI DIPERBAIKI (Siap Diambil)',
            'completed'         => 'Unit Telah Diambil oleh Pelanggan',
            'cancelled'         => 'Servis Dibatalkan',
        ];

        $statusText = $statusLabels[$newStatus] ?? ucfirst($newStatus);

        // Kirim WhatsApp update ke pelanggan
        try {
            $costInfo = ($actualCost !== null && $actualCost > 0) ? "\n💰 Biaya Final: *Rp " . number_format($actualCost) . "*" : "";
            $notesInfo = !empty($technicianNotes) ? "\n📝 Catatan Teknisi: {$technicianNotes}" : "";

            $msg = "Halo Kak *{$ticket->customer_name}*,\n\n"
                . "Update status servis unit *{$ticket->device_name}* (Tiket *#{$ticket->ticket_no}*):\n\n"
                . "📌 Status: *{$statusText}*{$costInfo}{$notesInfo}\n\n"
                . "Silakan hubungi kami jika ada pertanyaan lebih lanjut.\n"
                . "Terima kasih!";
            SendWhatsappDigitalReceiptJob::dispatch($ticket->customer_phone, $msg);
        } catch (\Throwable $e) {}

        return [
            'status'     => true,
            'ticket_no'  => $ticket->ticket_no,
            'new_status' => $newStatus,
            'message'    => "Status tiket {$ticket->ticket_no} berhasil diubah menjadi {$newStatus}."
        ];
    }
}
