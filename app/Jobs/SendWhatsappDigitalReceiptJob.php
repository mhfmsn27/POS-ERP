<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappDigitalReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 15;
    public $backoff = [5, 15, 30];

    protected string $phone;
    protected string $message;
    protected string $gatewayUrl;
    protected ?string $apiToken;
    protected string $provider;

    public function __construct(string $phone, string $message, string $gatewayUrl, ?string $apiToken = null, string $provider = 'crmhub_omnichannel')
    {
        $this->phone      = $phone;
        $this->message    = $message;
        $this->gatewayUrl = $gatewayUrl;
        $this->apiToken   = $apiToken;
        $this->provider   = $provider;
    }

    public function handle()
    {
        try {
            // Bersihkan format nomor HP (e.g. 0812... -> 62812...)
            $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
            if (substr($cleanPhone, 0, 1) === '0') {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }

            if ($this->provider === 'crmhub_omnichannel') {
                // Format CRMHUB Omnichannel REST API
                $payload = [
                    'recipient' => $cleanPhone,
                    'phone'     => $cleanPhone,
                    'message'   => $this->message,
                    'type'      => 'text',
                ];

                $request = Http::timeout(10)->acceptJson();
                if ($this->apiToken) {
                    $request->withToken($this->apiToken);
                }
                $response = $request->post($this->gatewayUrl, $payload);
            } else {
                // Fallback SenderWA format
                $response = Http::timeout(10)->acceptJson()->post($this->gatewayUrl, [
                    'method'     => 'text',
                    'text'       => $this->message,
                    'phone'      => $cleanPhone,
                    'api_key'    => $this->apiToken,
                ]);
            }

            if ($response->failed()) {
                Log::warning("[OMNICHANNEL WA GATEWAY] Failed delivery to {$cleanPhone}: " . $response->body());
            } else {
                Log::info("[OMNICHANNEL WA GATEWAY] Digital receipt successfully delivered to {$cleanPhone}");
            }
        } catch (\Throwable $e) {
            Log::error("[OMNICHANNEL WA GATEWAY] Error dispatching WhatsApp notification: " . $e->getMessage());
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }
}
