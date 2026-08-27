<?php

namespace Tests\Feature;

use App\Models\Admin\AccountSetting;
use App\Models\Admin\Merchant;
use App\Models\Admin\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UrgentFixesVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $userA;
    protected Store $storeA;
    protected Merchant $merchantA;

    protected User $userB;
    protected Store $storeB;
    protected Merchant $merchantB;

    protected function setUp(): void
    {
        parent::setUp();

        // Merchant A & Store A
        $this->merchantA = Merchant::create([
            'name' => 'Merchant Test A',
        ]);
        $this->storeA = Store::withoutGlobalScopes()->create([
            'name'        => 'Toko A',
            'merchant_id' => $this->merchantA->id,
            'address'     => 'Jl. Test A',
        ]);
        $this->userA = User::withoutGlobalScopes()->create([
            'name'        => 'User A',
            'email'       => 'usera_' . uniqid() . '@test.com',
            'password'    => bcrypt('password'),
            'role_type'   => 'user',
            'merchant_id' => $this->merchantA->id,
            'store_id'    => $this->storeA->id,
        ]);

        // Merchant B & Store B
        $this->merchantB = Merchant::create([
            'name' => 'Merchant Test B',
        ]);
        $this->storeB = Store::withoutGlobalScopes()->create([
            'name'        => 'Toko B',
            'merchant_id' => $this->merchantB->id,
            'address'     => 'Jl. Test B',
        ]);
        $this->userB = User::withoutGlobalScopes()->create([
            'name'        => 'User B',
            'email'       => 'userb_' . uniqid() . '@test.com',
            'password'    => bcrypt('password'),
            'role_type'   => 'user',
            'merchant_id' => $this->merchantB->id,
            'store_id'    => $this->storeB->id,
        ]);
    }

    /** @test */
    public function request_fails_without_store_header()
    {
        Sanctum::actingAs($this->userA);

        $response = $this->getJson('/service-api/app/inventory/components/variations');

        $response->assertStatus(403)
            ->assertJson([
                'status'  => false,
                'message' => 'Akses Dibatasi: Header Storeid tidak ditemukan.'
            ]);
    }

    /** @test */
    public function idor_cross_tenant_store_access_is_blocked()
    {
        Sanctum::actingAs($this->userA);

        // User A mencoba mengakses Store B milik Merchant B
        $response = $this->withHeaders([
            'Storeid' => $this->storeB->id,
        ])->getJson('/service-api/app/inventory/components/variations');

        $response->assertStatus(403)
            ->assertJson([
                'status'  => false,
                'message' => 'Akses Dibatasi: Toko tidak ditemukan atau bukan milik Merchant Anda.'
            ]);
    }

    /** @test */
    public function valid_store_access_succeeds_with_flexible_headers()
    {
        Sanctum::actingAs($this->userA);

        // Header Storeid
        $response1 = $this->withHeaders([
            'Storeid' => $this->storeA->id,
        ])->getJson('/service-api/app/inventory/components/variations');
        $response1->assertStatus(200);

        // Header storeId (casing dari axios di frontend)
        $response2 = $this->withHeaders([
            'storeId' => $this->storeA->id,
        ])->getJson('/service-api/app/inventory/components/variations');
        $response2->assertStatus(200);
    }

    /** @test */
    public function profit_cost_returns_valid_json_even_without_account_settings()
    {
        Sanctum::actingAs($this->userA);

        $response = $this->withHeaders([
            'Storeid' => $this->storeA->id,
        ])->getJson('/service-api/app/dashboard/profitable');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'pendapatan',
                'pengeluaran',
                'hpp',
                'profit'
            ]);
    }

    /** @test */
    public function my_store_detail_helper_caches_repeated_calls()
    {
        $this->withHeaders([
            'Storeid' => $this->storeA->id,
        ]);

        // Panggilan pertama
        $detail1 = my_store_detail();
        $this->assertNotNull($detail1);
        $this->assertEquals($this->storeA->id, $detail1->id);

        // Panggilan kedua (harus identik dari memoization cache)
        $detail2 = my_store_detail();
        $this->assertEquals($detail1->name, $detail2->name);
    }

    /** @test */
    public function bank_reconciliation_service_extracts_reference_numbers()
    {
        $service = new \App\Services\Accounting\BankReconciliationService();
        $note = "Pembayaran Faktur INV-2026/08/001 dan tagihan PO-99812";
        
        $refs = $service->extractReferenceNumbers($note);
        $this->assertContains('INV-2026/08/001', $refs);
        $this->assertContains('PO-99812', $refs);
    }

    /** @test */
    public function whatsapp_notification_job_is_dispatchable()
    {
        \Illuminate\Support\Facades\Queue::fake();

        \App\Jobs\SendWhatsappNotificationJob::dispatch(
            'Halo ini test',
            '08123456789',
            'dummy_device',
            'dummy_key'
        );

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWhatsappNotificationJob::class);
    }

    /** @test */
    public function health_check_endpoint_returns_json_structure()
    {
        $response = $this->getJson('/api/health');

        $response->assertJsonStructure([
            'status',
            'timestamp',
            'app_name',
            'environment',
            'php_version',
            'checks' => [
                'database',
                'cache',
                'storage',
            ]
        ]);
    }

    /** @test */
    public function master_data_cache_service_remembers_and_purges()
    {
        $cacheService = new \App\Services\Cache\MasterDataCacheService();
        $store = $cacheService->getStoreDetail($this->storeA->id);
        $this->assertNotNull($store);
        $this->assertEquals($this->storeA->id, $store->id);

        $cacheService->purgeStoreCache($this->storeA->id);
        $this->assertTrue(true);
    }

    /** @test */
    public function webhook_verification_service_validates_signatures()
    {
        $webhookService = new \App\Services\Webhook\WebhookVerificationService();
        
        $orderId = 'ORDER-12345';
        $statusCode = '200';
        $grossAmount = '50000';
        $serverKey = 'SB-Mid-server-TEST1234';
        
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        $isValid = $webhookService->verifyMidtrans($orderId, $statusCode, $grossAmount, $serverKey, $signature);
        $this->assertTrue($isValid);

        $isInvalid = $webhookService->verifyMidtrans($orderId, $statusCode, $grossAmount, $serverKey, 'wrong_signature');
        $this->assertFalse($isInvalid);
    }

    /** @test */
    public function transaction_sequence_service_generates_sequential_ref()
    {
        $sequenceService = new \App\Services\Transaction\TransactionSequenceService();
        $seq1 = $sequenceService->generateNextReference('SL', 'sell', $this->storeA->id);

        $this->assertArrayHasKey('invoice_no', $seq1);
        $this->assertArrayHasKey('ref_no', $seq1);
        $this->assertStringContainsString('SL', $seq1['ref_no']);
    }

    /** @test */
    public function sanitize_input_middleware_strips_scripts()
    {
        $middleware = new \App\Http\Middleware\SanitizeInputMiddleware();
        $request = \Illuminate\Http\Request::create('/test', 'POST', [
            'name'     => 'Buku Kas <script>alert("xss")</script>',
            'note'     => 'Catatan <iframe src="evil.com"></iframe> transaksi',
            'password' => 'secret<script>123',
        ]);

        $response = $middleware->handle($request, function ($req) {
            return $req->all();
        });

        $this->assertEquals('Buku Kas ', $response['name']);
        $this->assertEquals('Catatan  transaksi', $response['note']);
        $this->assertEquals('secret<script>123', $response['password']);
    }

    /** @test */
    public function low_stock_alerts_job_is_dispatchable()
    {
        \Illuminate\Support\Facades\Queue::fake();

        \App\Jobs\CheckLowStockAlertsJob::dispatch();

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\CheckLowStockAlertsJob::class);
    }
}
