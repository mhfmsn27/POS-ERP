<?php

namespace App\Services\Pos;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerDisplayService
{
    /**
     * Memperbarui state keranjang pada layar kedua pelanggan (Customer Display).
     *
     * @param int $storeId
     * @param array $cartData
     * @param int|null $userId
     * @return array
     */
    public function updateDisplayState(int $storeId, array $cartData, ?int $userId = null): array
    {
        if (!Schema::hasTable('pos_customer_display_states')) {
            return ['status' => false, 'message' => 'Tabel pos_customer_display_states belum tersedia.'];
        }

        $sessionToken = 'CDS-STORE-' . $storeId;
        $items = $cartData['items'] ?? [];
        $status = empty($items) ? 'idle' : ($cartData['status'] ?? 'scanning');

        DB::table('pos_customer_display_states')->updateOrInsert(
            ['session_token' => $sessionToken],
            [
                'store_id'         => $storeId,
                'user_id'          => $userId ?? auth()->id() ?? 1,
                'status'           => $status,
                'cart_payload'     => json_encode($items),
                'subtotal'         => (float)($cartData['subtotal'] ?? 0),
                'discount_total'   => (float)($cartData['discount_total'] ?? 0),
                'tax_total'        => (float)($cartData['tax_total'] ?? 0),
                'grand_total'      => (float)($cartData['grand_total'] ?? 0),
                'pay_amount'       => (float)($cartData['pay_amount'] ?? 0),
                'change_amount'    => (float)($cartData['change_amount'] ?? 0),
                'banner_promo_url' => $cartData['banner_url'] ?? null,
                'updated_at'       => now(),
            ]
        );

        return [
            'status'        => true,
            'session_token' => $sessionToken,
            'display_status'=> $status,
        ];
    }

    /**
     * Mengambil data keranjang untuk dirender di layar monitor pelanggan.
     *
     * @param string $sessionToken
     * @return array
     */
    public function getDisplayState(string $sessionToken): array
    {
        if (!Schema::hasTable('pos_customer_display_states')) {
            return ['status' => 'idle', 'items' => [], 'grand_total' => 0];
        }

        $state = DB::table('pos_customer_display_states')
            ->where('session_token', $sessionToken)
            ->first();

        if (!$state) {
            return [
                'session_token'  => $sessionToken,
                'status'         => 'idle',
                'items'          => [],
                'grand_total'    => 0,
                'subtotal'       => 0,
                'discount_total' => 0,
                'change_amount'  => 0,
            ];
        }

        return [
            'session_token'    => $state->session_token,
            'status'           => $state->status,
            'items'            => json_decode($state->cart_payload, true) ?: [],
            'subtotal'         => (float)$state->subtotal,
            'discount_total'   => (float)$state->discount_total,
            'tax_total'        => (float)$state->tax_total,
            'grand_total'      => (float)$state->grand_total,
            'pay_amount'       => (float)$state->pay_amount,
            'change_amount'    => (float)$state->change_amount,
            'banner_promo_url' => $state->banner_promo_url,
            'last_updated'     => $state->updated_at,
        ];
    }
}
