<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInputMiddleware
{
    /**
     * The names of the attributes that should NOT be sanitized (e.g. passwords, html templates).
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        if (!empty($input)) {
            array_walk_recursive($input, function (&$value, $key) {
                if (is_string($value) && !in_array($key, $this->except, true)) {
                    // Strip dangerous script tags and event handlers
                    $sanitized = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);
                    $sanitized = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $sanitized);
                    $sanitized = preg_replace('#javascript:[^"]*#is', '', $sanitized);
                    $sanitized = preg_replace('#\s*on\w+\s*=\s*(["\']).*?\1#is', '', $sanitized);
                    $value = $sanitized;
                }
            });

            $request->merge($input);
        }

        return $next($request);
    }
}
