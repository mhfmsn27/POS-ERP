<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        $this->configureRateLimiting();

        $this->routes(function () {


            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::prefix('service-api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/service.php'));

            Route::middleware(['web', 'check_license'])
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::prefix('hibrid')
                ->middleware(['web', 'check_license'])
                ->namespace($this->namespace)
                ->group(base_path('routes/apiv1.php'));

            Route::prefix('pos-admin')->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));


            Route::prefix('administrator')->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/super.php'));

            // $installed = Storage::disk('storage')->exists('installed');
            // if ($installed != false) {
            //     if (Schema::hasTable('licenses')) {
            //         $getLicense = License::first();
            //         if ($getLicense != null) {
            //             $getEcommerce = Plugins::where('code', 'mdh_ecommerce')
            //                 ->where('status', '1')
            //                 ->where('customer_id', $getLicense->customer_id)->first();
            //             if ($getEcommerce != null) {
            //                 Route::middleware('web')
            //                     ->namespace($this->namespace)
            //                     ->group(base_path('routes/ecommerce.php'));
            //             }
            //         }
            //     }
            // }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('auth-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('pos-checkout', function (Request $request) {
            return Limit::perMinute(120)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('data-export', function (Request $request) {
            return Limit::perMinute(15)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
