<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoMerchant
{
    /**
     * Handle an incoming request.
     * In Standalone Enterprise mode, all authenticated company staff/admins have access.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            return $next($request);
        }

        return redirect()->route('page.auth');
    }
}
