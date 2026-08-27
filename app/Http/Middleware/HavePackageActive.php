<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HavePackageActive
{
    /**
     * Handle an incoming request.
     * In Standalone Enterprise mode, all stores and branches are permanently unlocked with lifetime validity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
