<?php

namespace App\Services\Webhook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookVerificationService
{
    /**
     * Verifikasi signature HMAC SHA256 standar.
     *
     * @param Request $request
     * @param string $secretKey
     * @param string $signatureHeader
     * @return bool
     */
    public function verifyHmacSha256(Request $request, string $secretKey, string $signatureHeader = 'X-Signature'): bool
    {
        $signature = $request->header($signatureHeader);
        if (empty($signature)) {
            return false;
        }

        $payload = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $secretKey);

        return hash_equals($expected, $signature);
    }

    /**
     * Verifikasi signature Midtrans SHA512.
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $serverKey
     * @param string $providedSignature
     * @return bool
     */
    public function verifyMidtrans(string $orderId, string $statusCode, string $grossAmount, string $serverKey, string $providedSignature): bool
    {
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return hash_equals($expected, $providedSignature);
    }
}
