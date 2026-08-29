<?php

namespace App\Services\License;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    /**
     * Cache validity duration in seconds (Default: 6 hours).
     */
    protected int $cacheTtl;

    /**
     * Maximum offline grace period in seconds (Default: 7 days).
     */
    protected int $offlineGracePeriod;

    /**
     * Local storage file path for license cache.
     */
    protected string $cachePath;

    /**
     * Embedded RSA Public Key.
     */
    protected string $publicKey;

    public function __construct()
    {
        $this->cachePath = storage_path('framework/cache/poshub_license.dat');
        $this->cacheTtl = (int) config('license.cache_ttl', env('LICENSE_CACHE_TTL', 21600));
        $this->offlineGracePeriod = (int) config('license.offline_grace_period', env('LICENSE_OFFLINE_GRACE_PERIOD', 604800));
        $this->publicKey = config('license.public_key', '');
    }

    /**
     * Ekstraksi dan normalisasi domain dari Request.
     *
     * @param Request|null $request
     * @return string
     */
    public function getDomainFromRequest(?Request $request = null): string
    {
        if (!$request) {
            $request = request();
        }

        $host = $request->header('x-forwarded-host') 
            ?: $request->header('host') 
            ?: $request->getHost();

        // Handle port removal (e.g. localhost:8000 -> localhost)
        $host = explode(':', $host)[0];

        // Remove leading www. and trim
        $domain = preg_replace('/^www\./i', '', trim($host));
        return strtolower($domain);
    }

    /**
     * Mengecek apakah domain termasuk whitelist development / localhost (100% Bebas).
     *
     * @param string $domain
     * @return bool
     */
    public function isLocalOrDevDomain(string $domain): bool
    {
        if (config('app.env') === 'local' || env('ALLOW_ALL_LICENSE') === true || env('ALLOW_ALL_LICENSE') === 'true') {
            return true;
        }

        $devDomains = [
            'localhost',
            '127.0.0.1',
            '::1',
            '0.0.0.0'
        ];

        if (in_array($domain, $devDomains, true)) {
            return true;
        }

        // Wildcard local extensions: *.test, *.local, *.example, *.invalid
        if (preg_match('/\.(test|local|example|invalid)$/i', $domain)) {
            return true;
        }

        return false;
    }

    /**
     * Validasi kriptografis RSA-2048 / SHA-256 Signature.
     *
     * @param string $domain
     * @param string $licenseKey
     * @param string $base64Signature
     * @return bool
     */
    public function verifyCryptographicSignature(string $domain, string $licenseKey, string $base64Signature): bool
    {
        if (empty($this->publicKey) || empty($base64Signature)) {
            return false;
        }

        try {
            $rawSignature = base64_decode($base64Signature);
            $payload = strtolower(trim($domain)) . '|' . trim($licenseKey);
            $verifyResult = openssl_verify($payload, $rawSignature, $this->publicKey, OPENSSL_ALGO_SHA256);

            return ($verifyResult === 1);
        } catch (\Throwable $e) {
            Log::warning('[POSHUB License] Gagal verifikasi RSA signature: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghasilkan Cryptographic Operation Key untuk binding logika transaksi & akuntansi.
     *
     * @param string $operation (e.g. 'pos_trx', 'accounting_cogs', 'receipt_stamp')
     * @param string|null $domain
     * @return string
     */
    public function deriveOperationKey(string $operation, ?string $domain = null): string
    {
        $domain = $domain ?: $this->getDomainFromRequest();

        // 1. Localhost / Dev Bypass: Hasilkan kunci dev yang valid
        if ($this->isLocalOrDevDomain($domain)) {
            return hash_hmac('sha256', $operation . '|dev-unrestricted|' . $domain, config('app.key', 'poshub-dev-key'));
        }

        // 2. Production: Hasilkan kunci berdasarkan lisensi yang sah
        $status = $this->validateLicense($domain);
        if (empty($status['valid'])) {
            return 'INVALID_UNLICENSED_OPERATION_KEY';
        }

        $licenseKey = $status['license_key'] ?? 'VALID-ENTERPRISE-KEY';
        $appKey = config('app.key', 'poshub-enterprise-secure-key-2026');

        return hash_hmac('sha256', $operation . '|' . $domain . '|' . $licenseKey, $appKey);
    }

    /**
     * Menghasilkan Digital Checksum untuk transaksi kasir.
     *
     * @param float|int $amount
     * @param string $invoiceNo
     * @return string
     */
    public function generateTransactionChecksum($amount, string $invoiceNo): string
    {
        $opKey = $this->deriveOperationKey('pos_trx');
        return substr(hash_hmac('sha256', $invoiceNo . '|' . number_format((float)$amount, 2, '.', ''), $opKey), 0, 16);
    }

    /**
     * Validasi domain lisensi aktif (1 Domain Lock).
     *
     * @param string|null $domain
     * @param bool $forceRefresh
     * @return array
     */
    public function validateLicense(?string $domain = null, bool $forceRefresh = false): array
    {
        $domain = $domain ?: $this->getDomainFromRequest();

        if (empty($domain)) {
            return [
                'valid' => false,
                'reason' => 'EMPTY_DOMAIN',
                'message' => 'Domain atau host tidak dapat dibaca dari permintaan.',
                'domain' => $domain
            ];
        }

        // 1. Whitelist Local / Development (Bebas untuk Pengembang)
        if ($this->isLocalOrDevDomain($domain)) {
            return [
                'valid' => true,
                'domain' => $domain,
                'license_key' => 'DEV-LOCAL-UNRESTRICTED',
                'cached' => false,
                'is_dev' => true,
                'message' => 'Lisensi aktif (Mode Development / Localhost).'
            ];
        }

        $cached = $this->readCache();
        $now = time();

        // 2. Gunakan cache lokal jika masih valid, domain cocok, dan anti-tampering lolos
        if (!$forceRefresh && $cached && isset($cached['domain'], $cached['valid'], $cached['last_check'])) {
            if ($cached['domain'] === $domain && ($now - $cached['last_check']) < $this->cacheTtl) {
                return [
                    'valid' => (bool)$cached['valid'],
                    'domain' => $cached['domain'],
                    'license_key' => $cached['license_key'] ?? null,
                    'cached' => true,
                    'last_check' => $cached['last_check'],
                    'message' => $cached['valid'] ? 'Lisensi domain terverifikasi (Cache).' : 'Domain tidak terdaftar di lisensi server.'
                ];
            }
        }

        // 3. Verifikasi ke Google Sheets Central Repository
        $sheetId = config('license.sheet_id', env('LICENSE_SHEET_ID', ''));
        if (empty($sheetId)) {
            if ($cached && $cached['domain'] === $domain) {
                return [
                    'valid' => (bool)$cached['valid'],
                    'domain' => $cached['domain'],
                    'license_key' => $cached['license_key'] ?? null,
                    'cached' => true,
                    'message' => 'Lisensi aktif (Cached fallback).'
                ];
            }

            return [
                'valid' => true,
                'domain' => $domain,
                'license_key' => 'STANDALONE-ENTERPRISE',
                'cached' => false,
                'message' => 'Mode Enterprise Standalone Aktif.'
            ];
        }

        try {
            $remoteData = $this->fetchFromGoogleSheets($sheetId, $domain);
            $isValid = ($remoteData !== null);

            $payload = [
                'domain' => $domain,
                'valid' => $isValid,
                'license_key' => $remoteData['license_key'] ?? null,
                'last_check' => $now,
                'verified_at' => date('Y-m-d H:i:s', $now)
            ];

            $this->writeCache($payload);

            return [
                'valid' => $isValid,
                'domain' => $domain,
                'license_key' => $payload['license_key'],
                'cached' => false,
                'message' => $isValid ? 'Lisensi domain resmi terverifikasi aktif.' : 'Domain ini belum terdaftar pada lisensi resmi enterprise.'
            ];

        } catch (\Throwable $e) {
            Log::warning('[POSHUB License] Server lisensi pusat tidak dapat dihubungi: ' . $e->getMessage());

            // 4. Smart Offline Grace Period: Izinkan jika cache sebelumnya valid dan dalam masa grace period (7 hari)
            if ($cached && !empty($cached['valid']) && $cached['domain'] === $domain) {
                $age = $now - $cached['last_check'];
                if ($age < $this->offlineGracePeriod) {
                    return [
                        'valid' => true,
                        'domain' => $domain,
                        'license_key' => $cached['license_key'] ?? null,
                        'cached' => true,
                        'grace_period' => true,
                        'grace_expires_in_hours' => round(($this->offlineGracePeriod - $age) / 3600),
                        'message' => 'Server lisensi offline. Beroperasi dalam masa tenggang offline (Grace Period).'
                    ];
                }
            }

            return [
                'valid' => false,
                'reason' => 'SERVER_UNREACHABLE',
                'domain' => $domain,
                'message' => 'Tidak dapat memvalidasi lisensi ke server pusat: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Membaca dan memparsing file CSV dari Google Sheets.
     *
     * @param string $sheetId
     * @param string $targetDomain
     * @return array|null
     */
    protected function fetchFromGoogleSheets(string $sheetId, string $targetDomain): ?array
    {
        $csvUrl = "https://docs.google.com/spreadsheets/d/{$sheetId}/export?format=csv&gid=0";

        $response = Http::timeout(8)
            ->withHeaders([
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache'
            ])
            ->get($csvUrl);

        if (!$response->successful()) {
            throw new \Exception("HTTP status " . $response->status() . " saat membaca Google Sheets.");
        }

        $csvText = trim($response->body());
        $lines = explode("\n", $csvText);

        if (count($lines) < 2) {
            return null;
        }

        $target = strtolower(trim($targetDomain));

        // Format CSV:
        // Kolom 0 = Domain (e.g. tokosaya.com)
        // Kolom 1 = License Key (e.g. POSHUB-2026-XXXX)
        // Kolom 2 = RSA Cryptographic Signature (Opsional/Enterprise)
        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i]);
            if (empty($row[0])) continue;

            $sheetDomain = strtolower(trim(str_replace(['"', "'"], '', $row[0])));
            if ($sheetDomain === $target) {
                $licenseKey = isset($row[1]) ? trim($row[1]) : 'ACTIVE-KEY';
                $rsaSignature = isset($row[2]) ? trim($row[2]) : null;

                // Jika kolom RSA Signature terisi, validasi menggunakan Public Key
                if (!empty($rsaSignature)) {
                    $isSigValid = $this->verifyCryptographicSignature($sheetDomain, $licenseKey, $rsaSignature);
                    if (!$isSigValid) {
                        Log::error("[POSHUB License] Signature RSA tidak cocok untuk domain {$sheetDomain}.");
                        return null;
                    }
                }

                return [
                    'domain' => $sheetDomain,
                    'license_key' => $licenseKey
                ];
            }
        }

        return null;
    }

    /**
     * Force refresh lisensi dari Google Sheets.
     *
     * @param string|null $domain
     * @return array
     */
    public function refreshLicense(?string $domain = null): array
    {
        $domain = $domain ?: $this->getDomainFromRequest();
        $this->clearCache();
        return $this->validateLicense($domain, true);
    }

    /**
     * Mengambil ringkasan status lisensi sistem saat ini.
     *
     * @return array
     */
    public function getLicenseStatus(): array
    {
        $domain = $this->getDomainFromRequest();
        return $this->validateLicense($domain, false);
    }

    /**
     * Menulis cache terenkripsi dengan HMAC-SHA256 Signature.
     *
     * @param array $data
     * @return void
     */
    protected function writeCache(array $data): void
    {
        try {
            $json = json_encode($data);
            $key = config('app.key', 'poshub-enterprise-secure-key-2026');
            $signature = hash_hmac('sha256', $json, $key);

            $payload = base64_encode(json_encode([
                'signature' => $signature,
                'data' => $data
            ]));

            $dir = dirname($this->cachePath);
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }

            File::put($this->cachePath, $payload);
        } catch (\Throwable $e) {
            Log::error('[POSHUB License] Gagal menulis cache lisensi: ' . $e->getMessage());
        }
    }

    /**
     * Membaca cache lisensi lokal dan memverifikasi integritas signature (Anti-Tampering).
     *
     * @return array|null
     */
    protected function readCache(): ?array
    {
        if (!File::exists($this->cachePath)) {
            return null;
        }

        try {
            $raw = File::get($this->cachePath);
            $decoded = json_decode(base64_decode($raw), true);

            if (!isset($decoded['signature'], $decoded['data'])) {
                return null;
            }

            $key = config('app.key', 'poshub-enterprise-secure-key-2026');
            $expectedSignature = hash_hmac('sha256', json_encode($decoded['data']), $key);

            // Verifikasi anti-tampering
            if (!hash_equals($expectedSignature, $decoded['signature'])) {
                Log::warning('[POSHUB License] Signature cache lisensi tidak cocok (Potensi manipulasi file cache).');
                return null;
            }

            return $decoded['data'];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Menghapus cache lisensi lokal.
     *
     * @return void
     */
    public function clearCache(): void
    {
        if (File::exists($this->cachePath)) {
            File::delete($this->cachePath);
        }
    }
}
