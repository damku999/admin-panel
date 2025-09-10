<?php

namespace App\Providers;

use App\Models\{Customer, CustomerInsurance, Broker, InsuranceCompany};
use App\Observers\CacheInvalidationObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\{Schema, URL};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path('helpers.php');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(125);
        
        // Configure asset URL for subdirectory installation
        if (env('ASSET_URL')) {
            URL::forceRootUrl(env('ASSET_URL'));
        }
        
        // Register cache invalidation observers for performance optimization
        Customer::observe(CacheInvalidationObserver::class);
        CustomerInsurance::observe(CacheInvalidationObserver::class);
        Broker::observe(CacheInvalidationObserver::class);
        InsuranceCompany::observe(CacheInvalidationObserver::class);
    }
}
