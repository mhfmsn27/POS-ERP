<?php

namespace Poshub\Ecommerce\Middleware;

use App\Models\Admin\Store;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreSessionEcommerce
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
        $session = Session::get("dfstore");

        if ($session == null) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
