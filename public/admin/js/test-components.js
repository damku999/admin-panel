/**
 * Component System Test Suite
 * Validates that all components are loaded and working correctly
 */

// Test configuration
const ComponentTests = {
    
    // Test results storage
    results: {},
    
    /**
     * Run all component tests
     */
    runAllTests: function() {
        console.log('🧪 Starting Component System Tests...');
        
        this.testCoreManager();
        this.testUtilities();
        this.testNotificationManager();
        this.testModalManager();
        this.testDataTableManager();
        this.testFileUploadManager();
        
        this.displayResults();
    },
    
    /**
     * Test CoreManager functionality
     */
    testCoreManager: function() {
        const tests = [];
        
        try {
            // Test CoreManager existence
            tests.push({
                name: 'CoreManager exists',
                result: typeof window.CoreManager !== 'undefined',
                details: 'CoreManager object available globally'
            });
            
            // Test component registration
            tests.push({
                name: 'Component registration',
                result: typeof CoreManager.register === 'function' && typeof CoreManager.get === 'function',
                details: 'CoreManager has register and get methods'
            });
            
            // Test auto-initialization
            tests.push({
                name: 'Auto-initialization',
                result: CoreManager.components && CoreManager.components.size > 0,
                details: `${CoreManager.components ? CoreManager.components.size : 0} components registered`
            });
            
            // Test performance monitoring
            const status = CoreManager.getStatus();
            tests.push({
                name: 'Performance monitoring',
                result: status.performance && typeof status.performance.totalInitTime === 'number',
                details: `Total init time: ${status.performance?.totalInitTime?.toFixed(2) || 0}ms`
            });
            
        } catch (error) {
            tests.push({
                name: 'CoreManager error',
                result: false,
                details: error.message
            });
        }
        
        this.results.coreManager = tests;
    },
    
    /**
     * Test utility functions
     */
    testUtilities: function() {
        const tests = [];
        
        try {
            // Test Validators
            tests.push({
                name: 'Validators available',
                result: typeof window.Validators !== 'undefined',
                details: 'Validators utility loaded'
            });
            
            // Test specific validator
            if (window.Validators) {
                const emailTest = Validators.email('test@example.com');
                tests.push({
                    name: 'Email validation',
                    result: emailTest === true,
                    details: 'Email validator working correctly'
                });
            }
            
            // Test Formatters
            tests.push({
                name: 'Formatters available',
                result: typeof window.Formatters !== 'undefined',
                details: 'Formatters utility loaded'
            });
            
            // Test Helpers
            tests.push({
                name: 'Helpers available',
                result: typeof window.Helpers !== 'undefined',
                details: 'Helpers utility loaded'
            });
            
            // Test debounce function
            if (window.Helpers && typeof Helpers.debounce === 'function') {
                const debouncedFn = Helpers.debounce(() => {}, 100);
                tests.push({
                    name: 'Debounce function',
                    result: typeof debouncedFn === 'function',
                    details: 'Debounce utility working'
                });
            }
            
        } catch (error) {
            tests.push({
                name: 'Utilities error',
                result: false,
                details: error.message
            });
        }
        
        this.results.utilities = tests;
    },
    
    /**
     * Test NotificationManager
     */
    testNotificationManager: function() {
        const tests = [];
        
        try {
            // Check if NotificationManager is registered
            const hasNotificationManager = CoreManager && (CoreManager.has('notifications') || CoreManager.get('notifications') !== undefined);
            tests.push({
                name: 'NotificationManager registered',
                result: hasNotificationManager,
                details: hasNotificationManager ? 'Registered in CoreManager' : 'Not found in CoreManager'
            });
            
            // Test notification methods
            if (hasNotificationManager) {
                const notificationManager = CoreManager.get('notifications');
                tests.push({
                    name: 'Show method available',
                    result: typeof notificationManager.show === 'function',
                    details: 'show() method exists'
                });
                
                tests.push({
                    name: 'Confirm method available',
                    result: typeof notificationManager.confirm === 'function',
                    details: 'confirm() method exists'
                });
            }
            
        } catch (error) {
            tests.push({
                name: 'NotificationManager error',
                result: false,
                details: error.message
            });
        }
        
        this.results.notifications = tests;
    },
    
    /**
     * Test ModalManager
     */
    testModalManager: function() {
        const tests = [];
        
        try {
            // Check if ModalManager is registered
            const hasModalManager = CoreManager && (CoreManager.has('modals') || CoreManager.get('modals') !== undefined);
            tests.push({
                name: 'ModalManager registered',
                result: hasModalManager,
                details: hasModalManager ? 'Registered in CoreManager' : 'Not found in CoreManager'
            });
            
            // Test modal methods
            if (hasModalManager) {
                const modalManager = CoreManager.get('modals');
                tests.push({
                    name: 'Show method available',
                    result: typeof modalManager.show === 'function',
                    details: 'show() method exists'
                });
                
                tests.push({
                    name: 'Hide method available',
                    result: typeof modalManager.hide === 'function',
                    details: 'hide() method exists'
                });
            }
            
            // Test legacy modal functions
            tests.push({
                name: 'Legacy showModal function',
                result: typeof window.showModal === 'function',
                details: 'Legacy compatibility maintained'
            });
            
        } catch (error) {
            tests.push({
                name: 'ModalManager error',
                result: false,
                details: error.message
            });
        }
        
        this.results.modals = tests;
    },
    
    /**
     * Test DataTableManager
     */
    testDataTableManager: function() {
        const tests = [];
        
        try {
            // Check if DataTableManager is registered
            const hasDataTableManager = CoreManager && (CoreManager.has('datatables') || CoreManager.get('datatables') !== undefined);
            tests.push({
                name: 'DataTableManager registered',
                result: hasDataTableManager,
                details: hasDataTableManager ? 'Registered in CoreManager' : 'Not found in CoreManager'
            });
            
            // Test DataTableManager methods
            if (hasDataTableManager) {
                const dataTableManager = CoreManager.get('datatables');
                tests.push({
                    name: 'Initialize method available',
                    result: typeof dataTableManager.initializeTable === 'function',
                    details: 'initializeTable() method exists'
                });
                
                tests.push({
                    name: 'Refresh method available',
                    result: typeof dataTableManager.refresh === 'function',
                    details: 'refresh() method exists'
                });
            }
            
            // Test if DataTable library is available
            tests.push({
                name: 'DataTables library',
                result: typeof $.fn.DataTable !== 'undefined',
                details: 'jQuery DataTables plugin loaded'
            });
            
        } catch (error) {
            tests.push({
                name: 'DataTableManager error',
                result: false,
                details: error.message
            });
        }
        
        this.results.datatables = tests;
    },
    
    /**
     * Test FileUploadManager
     */
    testFileUploadManager: function() {
        const tests = [];
        
        try {
            // Check if FileUploadManager is registered
            const hasFileUploadManager = CoreManager && (CoreManager.has('fileUploads') || CoreManager.get('fileUploads') !== undefined);
            tests.push({
                name: 'FileUploadManager registered',
                result: hasFileUploadManager,
                details: hasFileUploadManager ? 'Registered in CoreManager' : 'Not found in CoreManager'
            });
            
            // Test FileUploadManager methods
            if (hasFileUploadManager) {
                const fileUploadManager = CoreManager.get('fileUploads');
                tests.push({
                    name: 'Initialize method available',
                    result: typeof fileUploadManager.initializeUploader === 'function',
                    details: 'initializeUploader() method exists'
                });
            }
            
        } catch (error) {
            tests.push({
                name: 'FileUploadManager error',
                result: false,
                details: error.message
            });
        }
        
        this.results.fileUploads = tests;
    },
    
    /**
     * Display test results
     */
    displayResults: function() {
        console.log('\n📊 Component System Test Results:');
        console.log('=====================================');
        
        let totalTests = 0;
        let passedTests = 0;
        
        Object.keys(this.results).forEach(category => {
            console.log(`\n🔧 ${category.toUpperCase()}:`);
            this.results[category].forEach(test => {
                totalTests++;
                if (test.result) {
                    passedTests++;
                    console.log(`  ✅ ${test.name} - ${test.details}`);
                } else {
                    console.log(`  ❌ ${test.name} - ${test.details}`);
                }
            });
        });
        
        console.log('\n=====================================');
        console.log(`📈 Overall Results: ${passedTests}/${totalTests} tests passed (${((passedTests/totalTests)*100).toFixed(1)}%)`);
        
        if (passedTests === totalTests) {
            console.log('🎉 All component tests passed! System is ready.');
        } else {
            console.log('⚠️  Some tests failed. Please check the errors above.');
        }
        
        // Return results for programmatic use
        return {
            total: totalTests,
            passed: passedTests,
            percentage: (passedTests/totalTests)*100,
            details: this.results
        };
    },
    
    /**
     * Test specific component by name
     */
    testComponent: function(componentName) {
        console.log(`🔍 Testing component: ${componentName}`);
        
        try {
            const hasComponent = CoreManager && CoreManager.has(componentName);
            if (hasComponent) {
                const component = CoreManager.get(componentName);
                console.log(`✅ ${componentName} is available and initialized`);
                console.log('Component instance:', component);
                return true;
            } else {
                console.log(`❌ ${componentName} is not available or not initialized`);
                return false;
            }
        } catch (error) {
            console.log(`❌ Error testing ${componentName}:`, error.message);
            return false;
        }
    }
};

// Make available globally for console testing
window.ComponentTests = ComponentTests;

// Auto-run tests when DOM is ready (after a short delay to allow initialization)
$(document).ready(function() {
    setTimeout(() => {
        // Only run tests if we're in debug mode
        if (window.CoreManager && window.CoreManager.config && window.CoreManager.config.debug) {
            ComponentTests.runAllTests();
        }
    }, 1000);
});

console.log('🧪 Component Test Suite loaded. Run ComponentTests.runAllTests() to test manually.');