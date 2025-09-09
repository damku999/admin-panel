<?php

namespace Tests\Unit\Services;

use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Mockery;

class CacheServiceTest extends TestCase
{
    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new CacheService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_insurance_companies_returns_cached_data()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('insurance_companies_active', 7200, \Closure::class)
            ->andReturn(collect());

        $result = $this->cacheService->getInsuranceCompanies();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_brokers_returns_cached_data()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('brokers_active', 7200, \Closure::class)
            ->andReturn(collect());

        $result = $this->cacheService->getBrokers();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_policy_types_returns_cached_data()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('policy_types_active', 7200, \Closure::class)
            ->andReturn(collect());

        $result = $this->cacheService->getPolicyTypes();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_premium_types_returns_cached_data()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('premium_types_active', 7200, \Closure::class)
            ->andReturn(collect());

        $result = $this->cacheService->getPremiumTypes();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_fuel_types_returns_cached_data()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('fuel_types_active', 7200, \Closure::class)
            ->andReturn(collect());

        $result = $this->cacheService->getFuelTypes();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_active_users_returns_cached_data()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('users_active', 7200, \Closure::class)
            ->andReturn(collect());

        $result = $this->cacheService->getActiveUsers();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_cache_customer_statistics_returns_cached_array()
    {
        $expectedStats = [
            'total_customers' => 100,
            'active_customers' => 80,
            'recent_customers' => 10,
            'total_policies' => 150,
            'active_policies' => 120,
            'expiring_policies' => 5
        ];

        Cache::shouldReceive('remember')
            ->once()
            ->with('customer_statistics', 1800, \Closure::class)
            ->andReturn($expectedStats);

        $result = $this->cacheService->cacheCustomerStatistics();

        $this->assertSame($expectedStats, $result);
    }

    public function test_invalidate_insurance_companies_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('insurance_companies_active');

        $this->cacheService->invalidateInsuranceCompanies();
    }

    public function test_invalidate_brokers_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('brokers_active');

        $this->cacheService->invalidateBrokers();
    }

    public function test_invalidate_policy_types_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('policy_types_active');

        $this->cacheService->invalidatePolicyTypes();
    }

    public function test_invalidate_premium_types_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('premium_types_active');

        $this->cacheService->invalidatePremiumTypes();
    }

    public function test_invalidate_fuel_types_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('fuel_types_active');

        $this->cacheService->invalidateFuelTypes();
    }

    public function test_invalidate_users_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('users_active');

        $this->cacheService->invalidateUsers();
    }

    public function test_invalidate_customer_statistics_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('customer_statistics');

        $this->cacheService->invalidateCustomerStatistics();
    }

    public function test_invalidate_all_calls_all_invalidation_methods()
    {
        Cache::shouldReceive('forget')->times(6);

        $this->cacheService->invalidateAll();
    }

    public function test_clear_application_cache_flushes_all_cache()
    {
        Cache::shouldReceive('flush')
            ->once();

        $this->cacheService->clearApplicationCache();
    }

    public function test_warmup_cache_calls_all_cache_methods()
    {
        Cache::shouldReceive('remember')->times(7)->andReturn(collect());

        $this->cacheService->warmupCache();
    }
}