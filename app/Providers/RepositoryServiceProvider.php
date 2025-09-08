<?php

namespace App\Providers;

use App\Contracts\Repositories\BrokerRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\CustomerInsuranceRepositoryInterface;
use App\Contracts\Repositories\InsuranceCompanyRepositoryInterface;
use App\Contracts\Repositories\PolicyRepositoryInterface;
use App\Contracts\Repositories\QuotationRepositoryInterface;
use App\Contracts\Services\BrokerServiceInterface;
use App\Contracts\Services\CustomerServiceInterface;
use App\Contracts\Services\CustomerInsuranceServiceInterface;
use App\Contracts\Services\InsuranceCompanyServiceInterface;
use App\Contracts\Services\PolicyServiceInterface;
use App\Contracts\Services\QuotationServiceInterface;
use App\Repositories\BrokerRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerInsuranceRepository;
use App\Repositories\InsuranceCompanyRepository;
use App\Repositories\PolicyRepository;
use App\Repositories\QuotationRepository;
use App\Services\BrokerService;
use App\Services\CustomerService;
use App\Services\CustomerInsuranceService;
use App\Services\InsuranceCompanyService;
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
        $this->app->bind(BrokerRepositoryInterface::class, BrokerRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(CustomerInsuranceRepositoryInterface::class, CustomerInsuranceRepository::class);
        $this->app->bind(InsuranceCompanyRepositoryInterface::class, InsuranceCompanyRepository::class);
        $this->app->bind(QuotationRepositoryInterface::class, QuotationRepository::class);
        $this->app->bind(PolicyRepositoryInterface::class, PolicyRepository::class);

        // Service bindings
        $this->app->bind(BrokerServiceInterface::class, BrokerService::class);
        $this->app->bind(CustomerServiceInterface::class, CustomerService::class);
        $this->app->bind(CustomerInsuranceServiceInterface::class, CustomerInsuranceService::class);
        $this->app->bind(InsuranceCompanyServiceInterface::class, InsuranceCompanyService::class);
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