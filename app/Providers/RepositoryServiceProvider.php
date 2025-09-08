<?php

namespace App\Providers;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\PolicyRepositoryInterface;
use App\Contracts\Repositories\QuotationRepositoryInterface;
use App\Contracts\Services\CustomerServiceInterface;
use App\Contracts\Services\PolicyServiceInterface;
use App\Contracts\Services\QuotationServiceInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\PolicyRepository;
use App\Repositories\QuotationRepository;
use App\Services\CustomerService;
use App\Services\PolicyService;
use App\Services\QuotationService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(QuotationRepositoryInterface::class, QuotationRepository::class);
        $this->app->bind(PolicyRepositoryInterface::class, PolicyRepository::class);

        // Service bindings
        $this->app->bind(CustomerServiceInterface::class, CustomerService::class);
        $this->app->bind(QuotationServiceInterface::class, QuotationService::class);
        $this->app->bind(PolicyServiceInterface::class, PolicyService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}