<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDomainLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:generate {domain : Nama domain pembeli, e.g. tokosaya.com} {--client= : Nama pembeli/merchant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate RSA Cryptographically Signed 1-Domain License untuk Google Sheet Lisensi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $domain = strtolower(trim(preg_replace('/^www\./i', '', $this->argument('domain'))));
        $client = $this->option('client') ?: 'POSHUB Enterprise Client';

        $privKeyFile = base_path('scratch/license_private.key');
        if (!file_exists($privKeyFile)) {
            $this->error('File private key tidak ditemukan di scratch/license_private.key!');
            return 1;
        }

        $privKey = file_get_contents($privKeyFile);
        $licenseKey = 'POSHUB-2026-' . strtoupper(substr(md5($domain . time()), 0, 8)) . '-' . rand(1000, 9999);

        $payload = $domain . '|' . $licenseKey;
        openssl_sign($payload, $signature, $privKey, OPENSSL_ALGO_SHA256);
        $base64Signature = base64_encode($signature);

        $this->info("====================================================================");
        $this->info("🔑 GENERATOR LISENSI RESMI 1 DOMAIN (POSHUB ENTERPRISE)");
        $this->info("====================================================================");
        $this->line("<comment>Domain        :</comment> " . $domain);
        $this->line("<comment>Client Name   :</comment> " . $client);
        $this->line("<comment>License Key   :</comment> " . $licenseKey);
        $this->line("<comment>RSA Signature :</comment> " . $base64Signature);
        $this->info("--------------------------------------------------------------------");
        $this->info("📋 BARIS UNTUK DITEMPEL DI GOOGLE SHEET (Copy-Paste Baris Ini):");
        $this->line("\"{$domain}\",\"{$licenseKey}\",\"{$base64Signature}\",\"{$client}\"");
        $this->info("====================================================================");

        return 0;
    }
}
