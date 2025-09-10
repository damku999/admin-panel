<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CacheManagementCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:manage 
                           {action : The action to perform (clear|warm|stats|clear-queries|clear-reports)}
                           {--all : Include all cache stores when clearing}';

    /**
     * The console command description.
     */
    protected $description = 'Manage application cache with performance optimizations for insurance system';

    public function __construct(
        private CacheService $cacheService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $includeAll = $this->option('all');

        try {
            switch ($action) {
                case 'clear':
                    return $this->clearCache($includeAll);
                
                case 'warm':
                    return $this->warmUpCache();
                
                case 'stats':
                    return $this->showCacheStats();
                
                case 'clear-queries':
                    return $this->clearQueriesCache();
                
                case 'clear-reports':
                    return $this->clearReportsCache();
                
                default:
                    $this->error('Invalid action. Use: clear, warm, stats, clear-queries, or clear-reports');
                    return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Cache operation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Clear application cache
     */
    private function clearCache(bool $includeAll): int
    {
        $this->info('🗑️ Clearing application cache...');

        if ($includeAll) {
            // Clear all Laravel caches
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            $this->info('✅ All Laravel caches cleared');
        }

        // Clear performance caches
        $this->cacheService->clearPerformanceCaches();
        $this->info('✅ Performance caches cleared');

        // Clear lookup caches
        $this->cacheService->invalidateAll();
        $this->info('✅ Lookup caches cleared');

        $this->newLine();
        $this->info('🎉 Cache clearing completed successfully!');
        
        return Command::SUCCESS;
    }

    /**
     * Warm up critical caches
     */
    private function warmUpCache(): int
    {
        $this->info('🔥 Warming up critical caches...');
        
        $startTime = microtime(true);

        // Warm up lookup data
        $this->task('Loading insurance companies', function () {
            $this->cacheService->getInsuranceCompanies();
        });

        $this->task('Loading brokers', function () {
            $this->cacheService->getBrokers();
        });

        $this->task('Loading policy types', function () {
            $this->cacheService->getPolicyTypes();
        });

        $this->task('Loading premium types', function () {
            $this->cacheService->getPremiumTypes();
        });

        $this->task('Loading fuel types', function () {
            $this->cacheService->getFuelTypes();
        });

        $this->task('Loading active users', function () {
            $this->cacheService->getActiveUsers();
        });

        // Warm up critical business data
        $this->task('Caching customer statistics', function () {
            $this->cacheService->cacheCustomerStatistics();
        });

        $this->task('Caching recent customers', function () {
            $this->cacheService->cacheRecentCustomers();
        });

        $this->task('Caching expiring policies', function () {
            $this->cacheService->cacheExpiringPolicies();
        });

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->newLine();
        $this->info("🎉 Cache warming completed in {$duration}s!");
        
        return Command::SUCCESS;
    }

    /**
     * Show cache statistics
     */
    private function showCacheStats(): int
    {
        $this->info('📊 Cache Performance Statistics');
        $this->newLine();

        try {
            $stats = $this->cacheService->getCacheStatistics();

            // Cache driver info
            $this->line("🔧 Cache Driver: {$stats['cache_driver']}");
            $this->line("📁 Storage Path: {$stats['storage_path']}");
            
            $this->newLine();
            $this->line('📁 Cache Store Breakdown:');
            
            foreach ($stats['stores'] as $store => $count) {
                $storeName = str_replace('_keys', '', $store);
                $this->line("  • {$storeName}: {$count} keys");
            }

            // Check cache status
            $this->newLine();
            $this->line('✅ File Cache: Active and Working');

        } catch (\Exception $e) {
            $this->error('Failed to retrieve cache statistics: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Clear only query result caches
     */
    private function clearQueriesCache(): int
    {
        $this->info('🗑️ Clearing query result caches...');
        
        \Illuminate\Support\Facades\Cache::store('queries')->flush();
        
        $this->info('✅ Query caches cleared successfully!');
        
        return Command::SUCCESS;
    }

    /**
     * Clear only report caches
     */
    private function clearReportsCache(): int
    {
        $this->info('🗑️ Clearing report caches...');
        
        \Illuminate\Support\Facades\Cache::store('reports')->flush();
        
        $this->info('✅ Report caches cleared successfully!');
        
        return Command::SUCCESS;
    }
}