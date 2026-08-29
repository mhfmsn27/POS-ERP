<?php

namespace App\Http\Middleware;

use App\Services\License\LicenseService;
use Closure;
use Illuminate\Http\Request;

class LicenseCheck
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Rute yang dikecualikan dari pemeriksaan lisensi
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        // 2. Validasi Lisensi Domain
        $result = $this->licenseService->validateLicense();

        if (!empty($result['valid'])) {
            return $next($request);
        }

        // 3. Jika Lisensi Tidak Valid:
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'license_invalid',
                'valid' => false,
                'domain' => $result['domain'] ?? $request->getHost(),
                'reason' => $result['reason'] ?? 'DOMAIN_NOT_REGISTERED',
                'message' => $result['message'] ?? 'Lisensi domain belum terdaftar atau tidak aktif. Silakan hubungi administrator POSHUB.',
                'verify_url' => url('/license/locked')
            ], 403);
        }

        return redirect()->route('license.locked');
    }

    /**
     * Mengecek apakah request saat ini merupakan rute yang diizinkan tanpa lisensi.
     *
     * @param Request $request
     * @return bool
     */
    protected function shouldPassThrough(Request $request): bool
    {
        $exemptPatterns = [
            'license*',
            'api/license*',
            'install*',
            'update*',
            'assets/*',
            'css/*',
            'js/*',
            'images/*',
            'theme/*',
            'ecommerce/*',
            'emobile/*',
            'sw.js',
            'manifest.json',
            'favicon.ico'
        ];

        foreach ($exemptPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
