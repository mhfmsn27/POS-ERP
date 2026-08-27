<?php

namespace App\Http\Middleware;

use App\Models\Plugins;
use Closure;
use Illuminate\Http\Request;

class IsEcommerce
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

        $getEcommerce = Plugins::where('code', 'mdh_ecommerce')->where('status', '1')->first();

        if ($getEcommerce) {
            return $next($request);
        } else {
            return redirect()->route('page.auth');
        }
        
    }
}
