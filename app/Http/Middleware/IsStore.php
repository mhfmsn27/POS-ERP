<?php

namespace App\Http\Middleware;

use App\Models\Admin\Store;
use Closure;
use Illuminate\Http\Request;

class IsStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $storeId = $request->header('Storeid') ?? $request->header('storeId') ?? $request->header('store_id');

        if (empty($storeId)) {
            return response()->json([
                'message'   => 'Akses Dibatasi: Header Storeid tidak ditemukan.',
                'status'    => false,
            ], 403);
        }

        $user = $request->user();
        if ($user) {
            // Administrator / Super Admin memiliki hak akses penuh ke seluruh toko
            $isAdmin = in_array($user->role_type, ['administrator', 'super_admin', 'admin'], true);

            if (!$isAdmin) {
                if (empty($user->merchant_id)) {
                    return response()->json([
                        'message'   => 'Akses Dibatasi: Akun Anda belum terhubung dengan Merchant manapun.',
                        'status'    => false,
                    ], 403);
                }

                $query = Store::withoutGlobalScopes()
                    ->where('id', $storeId)
                    ->where('merchant_id', $user->merchant_id);

                // Jika user dibatasi pada store_id tertentu
                if (!empty($user->store_id) && (int)$user->store_id !== (int)$storeId) {
                    return response()->json([
                        'message'   => 'Akses Dibatasi: Anda tidak memiliki izin untuk toko/cabang ini.',
                        'status'    => false,
                    ], 403);
                }

                if (!$query->exists()) {
                    return response()->json([
                        'message'   => 'Akses Dibatasi: Toko tidak ditemukan atau bukan milik Merchant Anda.',
                        'status'    => false,
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}

