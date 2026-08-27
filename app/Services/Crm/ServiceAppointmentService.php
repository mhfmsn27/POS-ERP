<?php

namespace App\Services\Crm;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ServiceAppointmentService
{
    /**
     * Membuat Janji Temu / Booking Jasa Baru.
     *
     * @param int $storeId
     * @param string $customerName
     * @param string $customerPhone
     * @param string $serviceName
     * @param string $date Format Y-m-d
     * @param string $startTime Format H:i
     * @param string|null $endTime
     * @param int|null $staffId
     * @param string|null $staffName
     * @param float $fee
     * @param int|null $customerId
     * @return array
     */
    public function bookAppointment(
        int $storeId,
        string $customerName,
        string $customerPhone,
        string $serviceName,
        string $date,
        string $startTime,
        ?string $endTime = null,
        ?int $staffId = null,
        ?string $staffName = null,
        float $fee = 0,
        ?int $customerId = null
    ): array {
        if (!Schema::hasTable('service_appointments')) {
            return ['status' => false, 'message' => 'Tabel service_appointments belum aktif.'];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
        $appointmentDate = date('Y-m-d', strtotime($date));

        $id = DB::table('service_appointments')->insertGetId([
            'store_id'         => $storeId,
            'customer_id'      => $customerId,
            'customer_name'    => $customerName,
            'customer_phone'   => $cleanPhone,
            'staff_id'         => $staffId,
            'staff_name'       => $staffName ?? 'Staf Bertugas',
            'service_name'     => $serviceName,
            'appointment_date' => $appointmentDate,
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'status'           => 'booked',
            'estimated_fee'    => $fee,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Kirim konfirmasi booking via WhatsApp
        try {
            $staffInfo = $staffName ? "\n👤 Terapis/Staf: *{$staffName}*" : "";
            $msg = "Halo Kak *{$customerName}*,\n\n"
                . "Booking layanan *{$serviceName}* Anda telah *BERHASIL DICATAT*! 📅\n\n"
                . "🗓️ Tanggal: *" . date('d/m/Y', strtotime($appointmentDate)) . "*\n"
                . "⏰ Pukul: *{$startTime} WIB*{$staffInfo}\n"
                . "💰 Estimasi Biaya: Rp " . number_format($fee) . "\n\n"
                . "Mohon hadir 10 menit sebelum waktu sesi. Sampai jumpa!";
            SendWhatsappDigitalReceiptJob::dispatch($cleanPhone, $msg);
        } catch (\Throwable $e) {
            Log::warning("Appointment WA booking error: " . $e->getMessage());
        }

        return [
            'status'         => true,
            'appointment_id' => $id,
            'message'        => "Booking janji temu {$serviceName} untuk {$customerName} berhasil dibuat."
        ];
    }

    /**
     * Memindai janji temu yang akan datang dan mengirimkan pesan pengingat (Reminder) via WhatsApp.
     *
     * @param int $storeId
     * @param string|null $targetDate Format Y-m-d (default besok)
     * @return array
     */
    public function sendReminders(int $storeId, ?string $targetDate = null): array
    {
        if (!Schema::hasTable('service_appointments')) {
            return ['status' => false, 'message' => 'Tabel belum tersedia.'];
        }

        $date = $targetDate ? date('Y-m-d', strtotime($targetDate)) : now()->addDay()->format('Y-m-d');

        $appointments = DB::table('service_appointments')
            ->where('store_id', $storeId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['booked', 'confirmed'])
            ->whereNull('reminder_sent_at')
            ->get();

        $sentCount = 0;

        foreach ($appointments as $apt) {
            $msg = "Pengingat Janji Temu ⏰\n\n"
                . "Halo Kak *{$apt->customer_name}*,\n"
                . "Mengingatkan kembali jadwal layanan *{$apt->service_name}* Anda:\n\n"
                . "🗓️ Hari/Tgl: *" . date('d/m/Y', strtotime($apt->appointment_date)) . "*\n"
                . "⏰ Jam: *{$apt->start_time} WIB*\n"
                . "👤 Staf: *{$apt->staff_name}*\n\n"
                . "Jika ada perubahan jadwal, silakan balas pesan ini. Terima kasih!";

            try {
                SendWhatsappDigitalReceiptJob::dispatch($apt->customer_phone, $msg);

                DB::table('service_appointments')->where('id', $apt->id)->update([
                    'reminder_sent_at' => now(),
                    'updated_at'       => now(),
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                Log::warning("Appointment WA reminder error: " . $e->getMessage());
            }
        }

        return [
            'status'     => true,
            'target_date'=> $date,
            'sent_count' => $sentCount,
            'message'    => "Pengingat berhasil dikirimkan ke {$sentCount} pelanggan."
        ];
    }

    /**
     * Memperbarui status janji temu (misal: confirmed, completed, cancelled).
     *
     * @param int $appointmentId
     * @param string $status
     * @param string|null $notes
     * @return array
     */
    public function updateStatus(int $appointmentId, string $status, ?string $notes = null): array
    {
        if (!Schema::hasTable('service_appointments')) {
            return ['status' => false, 'message' => 'Tabel belum aktif.'];
        }

        $updateData = ['status' => $status, 'updated_at' => now()];
        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }

        DB::table('service_appointments')->where('id', $appointmentId)->update($updateData);

        return [
            'status'         => true,
            'appointment_id' => $appointmentId,
            'new_status'     => $status,
            'message'        => "Status janji temu berhasil diubah menjadi {$status}."
        ];
    }

    /**
     * Mengambil daftar janji temu berdasarkan tanggal dan status.
     *
     * @param int $storeId
     * @param string|null $date
     * @param string|null $status
     * @return array
     */
    public function getAppointments(int $storeId, ?string $date = null, ?string $status = null): array
    {
        if (!Schema::hasTable('service_appointments')) {
            return ['status' => true, 'appointments' => []];
        }

        $query = DB::table('service_appointments')
            ->where('store_id', $storeId);

        if ($date) {
            $query->where('appointment_date', date('Y-m-d', strtotime($date)));
        }

        if ($status) {
            $query->where('status', $status);
        }

        $list = $query->orderBy('appointment_date', 'asc')->orderBy('start_time', 'asc')->get();

        return [
            'status'             => true,
            'total_appointments' => count($list),
            'appointments'       => $list
        ];
    }
}
