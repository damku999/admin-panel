<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const LONG_TTL = 7200; // 2 hours for shared hosting optimization
    private const INSURANCE_COMPANIES_KEY = 'insurance_companies_active';
    private const BROKERS_KEY = 'brokers_active';
    private const POLICY_TYPES_KEY = 'policy_types_active';
    private const PREMIUM_TYPES_KEY = 'premium_types_active';
    private const FUEL_TYPES_KEY = 'fuel_types_active';
    private const USERS_KEY = 'users_active';
    
    public function getInsuranceCompanies(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::INSURANCE_COMPANIES_KEY, self::LONG_TTL, function () {
            return \App\Models\InsuranceCompany::where('status', 1)->get();
        });
    }
    
    public function getBrokers(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::BROKERS_KEY, self::LONG_TTL, function () {
            return \App\Models\Broker::where('status', 1)->get();
        });
    }
    
    public function getPolicyTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::POLICY_TYPES_KEY, self::LONG_TTL, function () {
            return \App\Models\PolicyType::where('status', 1)->get();
        });
    }
    
    public function getPremiumTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::PREMIUM_TYPES_KEY, self::LONG_TTL, function () {
            return \App\Models\PremiumType::where('status', 1)->get();
        });
    }
    
    public function getFuelTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::FUEL_TYPES_KEY, self::LONG_TTL, function () {
            return \App\Models\FuelType::where('status', 1)->get();
        });
    }
    
    public function getActiveUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::USERS_KEY, self::LONG_TTL, function () {
            return \App\Models\User::where('status', 1)->get();
        });
    }
    
    public function invalidateInsuranceCompanies(): void
    {
        Cache::forget(self::INSURANCE_COMPANIES_KEY);
    }
    
    public function invalidateBrokers(): void
    {
        Cache::forget(self::BROKERS_KEY);
    }
    
    public function invalidatePolicyTypes(): void
    {
        Cache::forget(self::POLICY_TYPES_KEY);
    }
    
    public function invalidatePremiumTypes(): void
    {
        Cache::forget(self::PREMIUM_TYPES_KEY);
    }
    
    public function invalidateFuelTypes(): void
    {
        Cache::forget(self::FUEL_TYPES_KEY);
    }
    
    public function invalidateUsers(): void
    {
        Cache::forget(self::USERS_KEY);
    }
    
    public function invalidateAll(): void
    {
        $this->invalidateInsuranceCompanies();
        $this->invalidateBrokers();
        $this->invalidatePolicyTypes();
        $this->invalidatePremiumTypes();
        $this->invalidateFuelTypes();
        $this->invalidateUsers();
    }
    
    public function clearApplicationCache(): void
    {
        Cache::flush();
    }
    
    public function warmupCache(): void
    {
        $this->getInsuranceCompanies();
        $this->getBrokers();
        $this->getPolicyTypes();
        $this->getPremiumTypes();
        $this->getFuelTypes();
        $this->getActiveUsers();
        $this->cacheCustomerStatistics();
    }
    
    public function cacheCustomerStatistics(): array
    {
        return Cache::remember('customer_statistics', 1800, function () { // 30 minutes
            return [
                'total_customers' => \App\Models\Customer::count(),
                'active_customers' => \App\Models\Customer::where('status', 1)->count(),
                'recent_customers' => \App\Models\Customer::where('created_at', '>=', now()->subDays(7))->count(),
                'total_policies' => \App\Models\CustomerInsurance::count(),
                'active_policies' => \App\Models\CustomerInsurance::where('status', 1)->count(),
                'expiring_policies' => \App\Models\CustomerInsurance::where('expired_date', '<=', now()->addDays(30))->count(),
            ];
        });
    }
    
    public function invalidateCustomerStatistics(): void
    {
        Cache::forget('customer_statistics');
    }
}