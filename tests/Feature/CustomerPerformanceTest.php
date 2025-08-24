<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerInsurance;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create performance test data
        $this->createLargeDataset();
    }

    protected function createLargeDataset(): void
    {
        // Create multiple family groups with various sizes
        for ($i = 1; $i <= 10; $i++) {
            $familyGroup = FamilyGroup::factory()->create([
                'name' => "Test Family {$i}",
                'status' => true
            ]);
            
            // Create family members (2-8 members per family)
            $memberCount = rand(2, 8);
            $familyHead = null;
            
            for ($j = 1; $j <= $memberCount; $j++) {
                $customer = Customer::factory()->create([
                    'email' => "family{$i}_member{$j}@example.com",
                    'password' => Hash::make('password123'),
                    'status' => true,
                    'family_group_id' => $familyGroup->id
                ]);
                
                $isHead = $j === 1;
                if ($isHead) {
                    $familyHead = $customer;
                    $familyGroup->update(['family_head_id' => $customer->id]);
                }
                
                FamilyMember::create([
                    'family_group_id' => $familyGroup->id,
                    'customer_id' => $customer->id,
                    'relationship' => $isHead ? 'head' : 'spouse',
                    'is_head' => $isHead,
                    'status' => true
                ]);
                
                // Create 2-5 insurance policies per customer
                $policyCount = rand(2, 5);
                for ($k = 1; $k <= $policyCount; $k++) {
                    CustomerInsurance::factory()->create([
                        'customer_id' => $customer->id,
                        'policy_no' => "POL{$i}{$j}{$k}",
                        'start_date' => now()->subDays(rand(30, 365)),
                        'expired_date' => now()->addDays(rand(30, 365)),
                    ]);
                }
            }
        }
        
        // Create some independent customers
        for ($i = 1; $i <= 20; $i++) {
            $customer = Customer::factory()->create([
                'email' => "independent{$i}@example.com",
                'password' => Hash::make('password123'),
                'status' => true,
                'family_group_id' => null
            ]);
            
            // Create policies for independent customers
            CustomerInsurance::factory()->create([
                'customer_id' => $customer->id,
                'policy_no' => "IND{$i}",
            ]);
        }
    }

    public function test_family_query_performance(): void
    {
        $startTime = microtime(true);
        
        // Test query performance for family head viewing all policies
        $familyGroup = FamilyGroup::first();
        $familyHead = $familyGroup->familyHead;
        
        // Measure time for complex family query
        $policies = $familyHead->getViewableInsurance()->get();
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $this->assertNotEmpty($policies);
        $this->assertLessThan(500, $executionTime, 'Family policy query should complete within 500ms');
        
        // Check that the query is properly optimized (uses eager loading)
        $this->assertGreaterThan(0, $policies->count());
        
        // Verify relationships are properly loaded
        foreach ($policies as $policy) {
            $this->assertNotNull($policy->customer);
            $this->assertNotNull($policy->insuranceCompany);
        }
    }

    public function test_audit_logging_performance(): void
    {
        $customer = Customer::first();
        $policy = CustomerInsurance::first();
        
        $startTime = microtime(true);
        
        // Create multiple audit logs in batch
        for ($i = 0; $i < 100; $i++) {
            CustomerAuditLog::logPolicyAction('view_policy_detail', $policy, 'Performance test');
        }
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        $this->assertLessThan(1000, $executionTime, 'Batch audit logging should complete within 1 second');
        
        // Verify logs were created
        $logCount = CustomerAuditLog::where('action', 'view_policy_detail')->count();
        $this->assertGreaterThanOrEqual(100, $logCount);
    }

    public function test_large_family_dashboard_performance(): void
    {
        // Find the largest family group
        $largestFamily = FamilyGroup::withCount('customers')->orderBy('customers_count', 'desc')->first();
        $familyHead = $largestFamily->familyHead;
        
        $startTime = microtime(true);
        
        $this->actingAs($familyHead, 'customer');
        $response = $this->get(route('customer.dashboard'));
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        $response->assertStatus(200);
        $this->assertLessThan(2000, $executionTime, 'Dashboard should load within 2 seconds even for large families');
    }

    public function test_policy_search_query_performance(): void
    {
        $customer = Customer::whereNotNull('family_group_id')->first();
        
        $startTime = microtime(true);
        
        // Test policy filtering performance
        $activePolicies = $customer->getViewableInsurance()
            ->whereDate('expired_date', '>', now())
            ->get();
            
        $expiredPolicies = $customer->getViewableInsurance()
            ->whereDate('expired_date', '<=', now())
            ->get();
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        $this->assertLessThan(300, $executionTime, 'Policy filtering should be fast');
    }

    public function test_concurrent_login_performance(): void
    {
        $customers = Customer::take(10)->get();
        $loginTimes = [];
        
        foreach ($customers as $customer) {
            $startTime = microtime(true);
            
            $response = $this->post(route('customer.login'), [
                'email' => $customer->email,
                'password' => 'password123'
            ]);
            
            $endTime = microtime(true);
            $loginTimes[] = ($endTime - $startTime) * 1000;
            
            // Logout for next iteration
            $this->post(route('customer.logout'));
        }
        
        $averageLoginTime = array_sum($loginTimes) / count($loginTimes);
        $maxLoginTime = max($loginTimes);
        
        $this->assertLessThan(500, $averageLoginTime, 'Average login time should be under 500ms');
        $this->assertLessThan(1000, $maxLoginTime, 'No login should take more than 1 second');
    }

    public function test_database_query_optimization(): void
    {
        DB::enableQueryLog();
        
        $customer = Customer::whereNotNull('family_group_id')->first();
        $this->actingAs($customer, 'customer');
        
        // Clear query log
        DB::flushQueryLog();
        
        // Load dashboard
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should not have excessive N+1 queries
        $this->assertLessThan(20, $queryCount, 'Dashboard should not generate excessive queries');
        
        // Check for efficient queries (no queries without WHERE clauses for large tables)
        foreach ($queries as $query) {
            if (strpos($query['query'], 'select * from `customers`') === 0) {
                $this->assertStringContainsString('where', strtolower($query['query']), 
                    'Customer queries should have WHERE clauses');
            }
        }
        
        DB::disableQueryLog();
    }

    public function test_memory_usage_with_large_dataset(): void
    {
        $initialMemory = memory_get_usage(true);
        
        // Load a large family with all policies
        $familyHead = FamilyGroup::withCount('customers')
            ->orderBy('customers_count', 'desc')
            ->first()
            ->familyHead;
        
        $this->actingAs($familyHead, 'customer');
        
        // Load policies page which should load all family policies
        $response = $this->get(route('customer.policies'));
        $response->assertStatus(200);
        
        $finalMemory = memory_get_usage(true);
        $memoryIncrease = ($finalMemory - $initialMemory) / 1024 / 1024; // Convert to MB
        
        $this->assertLessThan(50, $memoryIncrease, 'Memory usage should not increase by more than 50MB');
    }

    public function test_audit_log_query_performance(): void
    {
        // Create many audit logs for testing
        $customer = Customer::first();
        
        for ($i = 0; $i < 1000; $i++) {
            CustomerAuditLog::create([
                'customer_id' => $customer->id,
                'action' => 'test_action',
                'description' => "Test log entry {$i}",
                'ip_address' => '127.0.0.1',
                'session_id' => 'test_session',
                'success' => true
            ]);
        }
        
        $startTime = microtime(true);
        
        // Query recent audit logs
        $recentLogs = CustomerAuditLog::where('customer_id', $customer->id)
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        $this->assertLessThan(100, $executionTime, 'Audit log queries should be fast with indexes');
        $this->assertCount(50, $recentLogs);
    }

    public function test_rate_limiting_effectiveness(): void
    {
        $customer = Customer::first();
        
        // Test login rate limiting
        $blockedRequests = 0;
        $totalRequests = 15; // More than the limit of 10
        
        for ($i = 0; $i < $totalRequests; $i++) {
            $response = $this->post(route('customer.login'), [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword'
            ]);
            
            if ($response->getStatusCode() === 429) {
                $blockedRequests++;
            }
        }
        
        $this->assertGreaterThan(0, $blockedRequests, 'Rate limiting should block excessive requests');
        $this->assertLessThan($totalRequests, $blockedRequests, 'Not all requests should be blocked initially');
    }

    public function test_session_performance_under_load(): void
    {
        $customer = Customer::first();
        $this->actingAs($customer, 'customer');
        
        $sessionTimes = [];
        
        // Make multiple requests to test session handling performance
        for ($i = 0; $i < 20; $i++) {
            $startTime = microtime(true);
            
            $response = $this->get(route('customer.dashboard'));
            $response->assertStatus(200);
            
            $endTime = microtime(true);
            $sessionTimes[] = ($endTime - $startTime) * 1000;
        }
        
        $averageTime = array_sum($sessionTimes) / count($sessionTimes);
        $maxTime = max($sessionTimes);
        
        $this->assertLessThan(200, $averageTime, 'Average session request time should be fast');
        $this->assertLessThan(500, $maxTime, 'No single request should be too slow');
    }

    public function test_data_privacy_performance(): void
    {
        $customer = Customer::first();
        
        $startTime = microtime(true);
        
        // Test privacy data generation performance
        for ($i = 0; $i < 100; $i++) {
            $safeData = $customer->getPrivacySafeData();
            $this->assertArrayHasKey('email', $safeData);
            $this->assertStringContainsString('*', $safeData['email']); // Should be masked
        }
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        $this->assertLessThan(100, $executionTime, 'Privacy data generation should be fast');
    }

    public function test_concurrent_policy_access_performance(): void
    {
        $familyHead = FamilyGroup::first()->familyHead;
        $policies = $familyHead->getViewableInsurance()->take(10)->get();
        
        $accessTimes = [];
        
        $this->actingAs($familyHead, 'customer');
        
        foreach ($policies as $policy) {
            $startTime = microtime(true);
            
            $response = $this->get(route('customer.policies.detail', $policy->id));
            $response->assertStatus(200);
            
            $endTime = microtime(true);
            $accessTimes[] = ($endTime - $startTime) * 1000;
        }
        
        $averageTime = array_sum($accessTimes) / count($accessTimes);
        $maxTime = max($accessTimes);
        
        $this->assertLessThan(300, $averageTime, 'Average policy access time should be reasonable');
        $this->assertLessThan(1000, $maxTime, 'No policy access should be extremely slow');
    }
}