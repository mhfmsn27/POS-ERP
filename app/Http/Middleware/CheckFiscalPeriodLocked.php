<?php

namespace App\Http\Middleware;

use App\Services\Accounting\FiscalPeriodService;
use Closure;
use Illuminate\Http\Request;

class CheckFiscalPeriodLocked
{
    protected FiscalPeriodService $fiscalPeriodService;

    public function __construct(FiscalPeriodService $fiscalPeriodService)
    {
        $this->fiscalPeriodService = $fiscalPeriodService;
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
        // Periksa tanggal transaksi pada request (hanya untuk POST, PUT, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $transactionDate = $request->input('operation_date')
                ?? $request->input('transaction_date')
                ?? $request->input('date')
                ?? null;

            if ($transactionDate) {
                $storeId = my_store() ?? $request->input('store_id');
                if ($this->fiscalPeriodService->isPeriodLocked((string)$transactionDate, $storeId ? (int)$storeId : null)) {
                    return response()->json([
                        'status'  => false,
                        'message' => "Akses Ditolak: Periode akuntansi untuk tanggal {$transactionDate} telah dikunci/ditutup (Locked Fiscal Period)."
                    ], 422);
                }
            }
        }

        return $next($request);
    }
}
