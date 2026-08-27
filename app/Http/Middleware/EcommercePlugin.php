<?php

namespace App\Http\Middleware;

use App\Models\Plugins;
use Closure;
use Illuminate\Http\Request;

class EcommercePlugin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $getEcommerce = Plugins::where('code', 'mdh_ecommerce')->where('status','1')->first();
        return $getEcommerce != null ? $next($request) :   redirect()->route('login');
    }
}
