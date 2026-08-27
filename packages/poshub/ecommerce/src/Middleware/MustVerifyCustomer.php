<?php

namespace Poshub\Ecommerce\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class MustVerifyCustomer
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
            $redirectLogin    = redirect()->route('ecommerce.login');
            $redirectVerify   = redirect()->route('ecommerce.verify');

            $device = new Agent();
            if ($device->isMobile()) {
                  $redirectLogin      = redirect()->route('ecommerce.mobile.login');
                  $redirectVerify     = redirect()->route('ecommerce.mobile.verify');
            }

            if (Auth::guard('customers')->check() == false) {
                  return  $request->wantsJson() ?
                        response()->json([
                              'message' => 'Anda belum login, silahkan login terlebih dahulu',
                              'status' => false
                        ]) :
                        $redirectLogin;
            } else {
                  if (Auth::guard('customers')->user()->email_verify == null) {
                        return  $request->wantsJson() ?
                              response()->json([
                                    'message' => 'Silahkan verifikasi Alamat Email Terlebih dahulu',
                                    'status' => false
                              ]) :
                              $redirectVerify;
                  }

                  return $next($request);
            }
      }
}
