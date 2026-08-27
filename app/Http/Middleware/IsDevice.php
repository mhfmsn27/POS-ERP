<?php

namespace App\Http\Middleware;

use App\Models\Admin\Setting;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class IsDevice
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
        $settings = Setting::first();
        if ($settings->mobile_version == 'on') {
            $device = new Agent();
            if ($device->isMobile()) {
                return redirect()->route('m.index');
            } else {
                return $next($request);
            }
        }
        return $next($request);
    }
}
