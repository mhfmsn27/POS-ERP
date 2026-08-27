<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChooseStore
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
        if (auth()->check()) {
            if (my_store() != null) {
                return $next($request);
            }
            return redirect()->route('store.choose');
        }

        return redirect()->route('page.auth');
    }
}
