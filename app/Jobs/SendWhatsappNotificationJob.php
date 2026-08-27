<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 10;
    public $backoff = [5, 15, 30];

    protected string $message;
    protected string $phone;
    protected string $deviceKey;
    protected string $apiKey;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param string $phone
     * @param string $deviceKey
     * @param string $apiKey
     */
    public function __construct(string $message, string $phone, string $deviceKey, string $apiKey)
    {
        $this->message   = $message;
        $this->phone     = $phone;
        $this->deviceKey = $deviceKey;
        $this->apiKey    = $apiKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response = Http::timeout(8)
                ->accept('application/json')
                ->post('https://app.senderwa.id/api-app/whatsapp/send-message', [
                    'method'     => 'text',
                    'text'       => $this->message,
                    'phone'      => $this->phone,
                    'device_key' => $this->deviceKey,
                    'api_key'    => $this->apiKey,
                ]);

            if ($response->failed()) {
                Log::warning('WhatsApp Gateway returned error response', [
                    'status' => $response->status(),
                    'phone'  => $this->phone,
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp Notification Dispatch Failed: ' . $e->getMessage(), [
                'phone' => $this->phone,
            ]);

            // Allow retry if attempts remain
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }
}
