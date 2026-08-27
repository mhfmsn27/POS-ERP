<?php

namespace Poshub\Ecommerce\Middleware;

use Closure;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Symfony\Component\HttpFoundation\Response;

class DomainSiteIdentity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host       = request()->getHost();
        $subdomain  = explode('.', $host)[0];
        //$subdomain  = 'mdh'; 
        if (session()->get('mydomain') != $subdomain || session()->get('dfstore') == null) {
            $settings = EcommerceApiSetting::where("domain_site", $subdomain)->where("ecommerce_activation", "yes")->first(['id', 'domain_site', 'store_id', 'copyright']);


            if ($settings) {
                session()->put('dfstore', $settings->store_id);
                session()->put('mydomain', $subdomain);
                return $next($request);
            } else {
                return redirect()->route('page.auth');
            }
        }


        return $next($request);
    }
}
