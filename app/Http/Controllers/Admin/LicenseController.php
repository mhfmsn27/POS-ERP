<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\License\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Mengambil status lisensi saat ini (JSON).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function status(Request $request): JsonResponse
    {
        $result = $this->licenseService->getLicenseStatus();
        return response()->json($result);
    }

    /**
     * Validasi domain tertentu atau domain saat ini.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        $domain = $request->input('domain');
        $result = $this->licenseService->validateLicense($domain);

        $status = !empty($result['valid']) ? 200 : 403;
        return response()->json($result, $status);
    }

    /**
     * Force refresh lisensi dari Google Sheets.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $domain = $request->input('domain');
        $result = $this->licenseService->refreshLicense($domain);

        return response()->json([
            'success' => !empty($result['valid']),
            'data' => $result
        ]);
    }

    /**
     * Menampilkan halaman Lock Screen Lisensi resmi POSHUB.
     *
     * @param Request $request
     * @return View
     */
    public function showLocked(Request $request): View
    {
        $domain = $this->licenseService->getDomainFromRequest($request);
        $status = $this->licenseService->validateLicense($domain);

        return view('license.locked', [
            'domain' => $domain,
            'status' => $status
        ]);
    }
}
