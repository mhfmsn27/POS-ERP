<?php

namespace Poshub\Ecommerce\Providers;

use App\Http\Middleware\Authenticate;
use App\Models\Admin\Customer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\Registrar as RouterRegistrar;
use Illuminate\Support\Facades\Config;
use Poshub\Ecommerce\Components\Admin\ShowPaymentComponent;
use Poshub\Ecommerce\Components\Admin\TabBlogComponent;
use Poshub\Ecommerce\Components\Admin\TabMediaComponent;
use Poshub\Ecommerce\Components\Admin\TabSettingComponent;
use Poshub\Ecommerce\Components\Content\CategoryComponent;
use Poshub\Ecommerce\Components\Content\DeafultBannerComponent;
use Poshub\Ecommerce\Components\Content\NewProductsComponent;
use Poshub\Ecommerce\Components\Content\OurProductsComponent;
use Poshub\Ecommerce\Components\Content\ProductPopularComponent;
use Poshub\Ecommerce\Components\Content\SidebarAccountComponent;
use Poshub\Ecommerce\Components\Content\SliderComponent;
use Poshub\Ecommerce\Components\HeaderComponent;
use Poshub\Ecommerce\Components\FooterComponent;
use Poshub\Ecommerce\Components\MenuComponent;
use Poshub\Ecommerce\Components\MetaComponent;
use Poshub\Ecommerce\Components\Mobile\MobileBannerComponent;
use Poshub\Ecommerce\Components\Mobile\MobileCategoryComponent;
use Poshub\Ecommerce\Components\Mobile\MobileFooterComponent;
use Poshub\Ecommerce\Components\Mobile\MobileHeaderOneComponent;
use Poshub\Ecommerce\Components\Mobile\MobileProductComponent;
use Poshub\Ecommerce\Components\SidebarBlogComponent;
use Poshub\Ecommerce\Components\SidebarShopComponent;
use Poshub\Ecommerce\Middleware\CustomerIsLogin;
use Poshub\Ecommerce\Middleware\CustomerNotLogin;
use Poshub\Ecommerce\Middleware\DeviceKomputerCommerce;
use Poshub\Ecommerce\Middleware\DevicePhoneCommerce;
use Poshub\Ecommerce\Middleware\DomainSiteIdentity;
use Poshub\Ecommerce\Middleware\MustVerifyCustomer;
use Poshub\Ecommerce\Middleware\StoreSessionEcommerce;

class PoshubEcommerceProvider extends ServiceProvider
{
  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->publishFiles();
    $this->loadViewsFrom(__DIR__ . '/../Views', 'ecommerce');
    $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php'); 
    $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    $this->loadRoutesFrom(__DIR__ . '/../Routes/mobile.php');

    $this->loadViewComponentsAs('ecommerce', [
      HeaderComponent::class,
      FooterComponent::class,
      MenuComponent::class,
      SliderComponent::class,
      CategoryComponent::class,
      DeafultBannerComponent::class,
      ProductPopularComponent::class,
      NewProductsComponent::class,
      OurProductsComponent::class,
      SidebarShopComponent::class,
      SidebarAccountComponent::class,
      SidebarBlogComponent::class,
      MetaComponent::class,
      TabMediaComponent::class,
      TabBlogComponent::class,
      TabSettingComponent::class,
      ShowPaymentComponent::class,

      // Mobile Component
      MobileFooterComponent::class,
      MobileCategoryComponent::class,
      MobileBannerComponent::class,
      MobileProductComponent::class,
      MobileHeaderOneComponent::class
    ]);
  }

  /**
   * Bootstrap the application events.
   *
   * @param \Illuminate\Routing\Router $router
   */
  public function boot(RouterRegistrar $router)
  {
    // Middleware
    $router->middlewareGroup('customers_must_verify', [MustVerifyCustomer::class]);
    $router->middlewareGroup('customers_must_login', [CustomerIsLogin::class]);
    $router->middlewareGroup('domain_identification', [DomainSiteIdentity::class]);
    $router->middlewareGroup('customers_not_login', [CustomerNotLogin::class]); 
    $router->middlewareGroup('device_komputer_commerce', [DeviceKomputerCommerce::class]);
    $router->middlewareGroup('device_phone_commerce', [DevicePhoneCommerce::class]);

    $router->middlewareGroup('auth', [Authenticate::class]);

    Config::set('auth.guards.customers', [
      'driver' => 'session',
      'provider' => 'customers',
    ]);

    Config::set('auth.providers.customers', [
      'driver' => 'eloquent',
      'model' => Customer::class,
    ]);
  }

  /**
   * Publish config file for the installer.
   *
   * @return void
   */
  protected function publishFiles()
  {
  }
}
