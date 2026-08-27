<?php

namespace Poshub\Ecommerce\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class CustomerIsLogin
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

        $redirectRout = redirect()->route('ecommerce.login');

        $device = new Agent();
        if ($device->isMobile()) {
            $redirectRout = redirect()->route('ecommerce.mobile.login');
        }

        if (Auth::guard('customers')->check() == false) {
            return  $request->wantsJson() ?
                response()->json([
                    'message' => 'Anda belum login, silahkan login terlebih dahulu',
                    'status' => false
                ]) :
                $redirectRout;
        } else {
            return $next($request);
        }
    }
}
