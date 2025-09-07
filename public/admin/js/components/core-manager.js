/**
 * Core Component Manager
 * Unified initialization and management system for all JavaScript components
 */

const CoreManager = {
    
    // Component registry
    components: new Map(),
    
    // Global configuration
    config: {
        debug: false,
        autoInit: true,
        performance: false
    },
    
    // Performance monitoring
    performance: {
        startTime: null,
        components: new Map()
    },
    
    /**
     * Initialize the core management system
     */
    init: function(options = {}) {
        this.config = { ...this.config, ...options };
        
        if (this.config.performance) {
            this.performance.startTime = performance.now();
        }
        
        if (this.config.debug) {
            console.log('🚀 CoreManager: Initializing admin panel components...');
        }
        
        // Initialize core components
        this.initializeCoreComponents();
        
        // Auto-initialize registered components
        if (this.config.autoInit) {
            this.initializeAllComponents();
        }
        
        // Set up global error handling
        this.setupGlobalErrorHandling();
        
        // Set up performance monitoring
        if (this.config.performance) {
            this.setupPerformanceMonitoring();
        }
        
        if (this.config.debug) {
            const totalTime = performance.now() - this.performance.startTime;
            console.log(`✅ CoreManager: All components initialized in ${totalTime.toFixed(2)}ms`);
        }
    },
    
    /**
     * Register a component
     */
    register: function(name, component, options = {}) {
        if (this.components.has(name)) {
            if (this.config.debug) {
                console.log(`ℹ️ Component '${name}' already registered, skipping`);
            }
            return;
        }
        
        this.components.set(name, {
            component: component,
            options: options,
            initialized: false,
            instance: null
        });
        
        if (this.config.debug) {
            console.log(`📝 Registered component: ${name}`);
        }
    },
    
    /**
     * Initialize a specific component
     */
    initializeComponent: function(name, customOptions = {}) {
        const componentData = this.components.get(name);
        if (!componentData) {
            console.error(`❌ Component '${name}' not found`);
            return null;
        }
        
        if (componentData.initialized) {
            if (this.config.debug) {
                console.log(`ℹ️ Component '${name}' already initialized`);
            }
            return componentData.instance;
        }
        
        const startTime = this.config.performance ? performance.now() : null;
        
        try {
            const options = { ...componentData.options, ...customOptions };
            const instance = typeof componentData.component === 'function' 
                ? new componentData.component(options)
                : componentData.component;
                
            if (instance && typeof instance.init === 'function') {
                instance.init(options);
            }
            
            componentData.initialized = true;
            componentData.instance = instance;
            
            if (this.config.performance && startTime) {
                const duration = performance.now() - startTime;
                this.performance.components.set(name, duration);
                
                if (this.config.debug) {
                    console.log(`✅ Initialized ${name} in ${duration.toFixed(2)}ms`);
                }
            }
            
            // Emit initialization event
            this.emit('component:initialized', { name, instance });
            
            return instance;
            
        } catch (error) {
            console.error(`❌ Failed to initialize component '${name}':`, error);
            return null;
        }
    },
    
    /**
     * Initialize all registered components
     */
    initializeAllComponents: function() {
        const componentNames = Array.from(this.components.keys());
        
        for (const name of componentNames) {
            this.initializeComponent(name);
        }
    },
    
    /**
     * Get a component instance
     */
    get: function(name) {
        const componentData = this.components.get(name);
        if (!componentData) {
            console.error(`❌ Component '${name}' not found`);
            return null;
        }
        
        if (!componentData.initialized) {
            return this.initializeComponent(name);
        }
        
        return componentData.instance;
    },
    
    /**
     * Check if component is available and initialized
     */
    has: function(name) {
        const componentData = this.components.get(name);
        return componentData && componentData.initialized;
    },
    
    /**
     * Destroy a component
     */
    destroy: function(name) {
        const componentData = this.components.get(name);
        if (!componentData) {
            console.error(`❌ Component '${name}' not found`);
            return false;
        }
        
        try {
            if (componentData.instance && typeof componentData.instance.destroy === 'function') {
                componentData.instance.destroy();
            }
            
            componentData.initialized = false;
            componentData.instance = null;
            
            this.emit('component:destroyed', { name });
            
            if (this.config.debug) {
                console.log(`🗑️ Destroyed component: ${name}`);
            }
            
            return true;
        } catch (error) {
            console.error(`❌ Failed to destroy component '${name}':`, error);
            return false;
        }
    },
    
    /**
     * Restart a component
     */
    restart: function(name, options = {}) {
        this.destroy(name);
        return this.initializeComponent(name, options);
    },
    
    /**
     * Initialize core components
     */
    initializeCoreComponents: function() {
        // Initialize global utilities first
        if (typeof Validators !== 'undefined') {
            this.register('validators', Validators, { global: true });
        }
        
        if (typeof Formatters !== 'undefined') {
            this.register('formatters', Formatters, { global: true });
        }
        
        if (typeof Helpers !== 'undefined') {
            this.register('helpers', Helpers, { global: true });
        }
        
        // Auto-detect and register module components
        this.autoDetectComponents();
    },
    
    /**
     * Auto-detect available components on the page
     */
    autoDetectComponents: function() {
        // Cache DOM queries to avoid repeated lookups
        const hasDataTable = document.querySelector('.data-table') !== null;
        
        const detections = [
            { name: 'customers', check: () => typeof initializeCustomerForm === 'function' },
            { name: 'quotations', check: () => typeof initializeQuotationForm === 'function' },
            { name: 'claims', check: () => typeof showAssignClaimNumberModal === 'function' },
            { name: 'datatables', check: () => hasDataTable || typeof window.DataTableManager !== 'undefined' },
            { name: 'select2', check: () => typeof $.fn.select2 === 'function' },
            { name: 'tooltips', check: () => typeof $.fn.tooltip === 'function' },
            { name: 'notifications', check: () => typeof window.NotificationManager !== 'undefined' },
            { name: 'modals', check: () => typeof window.ModalManager !== 'undefined' },
            { name: 'fileUploads', check: () => typeof window.FileUploadManager !== 'undefined' }
        ];
        
        detections.forEach(({ name, check }) => {
            if (check()) {
                // For component managers, register the actual manager object
                let component;
                switch (name) {
                    case 'notifications':
                        component = window.NotificationManager;
                        break;
                    case 'modals':
                        component = window.ModalManager;
                        break;
                    case 'datatables':
                        component = window.DataTableManager;
                        break;
                    case 'fileUploads':
                        component = window.FileUploadManager;
                        break;
                    default:
                        component = this.createAutoComponent(name);
                }
                
                this.register(name, component, { autoDetected: true });
            }
        });
    },
    
    /**
     * Create auto-detected component wrapper
     */
    createAutoComponent: function(name) {
        return {
            name: name,
            init: function(options) {
                switch (name) {
                    case 'customers':
                        if (typeof initializeCustomerForm === 'function') {
                            initializeCustomerForm();
                        }
                        break;
                    case 'quotations':
                        if (typeof initializeQuotationForm === 'function') {
                            initializeQuotationForm();
                        }
                        break;
                    case 'datatables':
                        if (typeof window.DataTableManager !== 'undefined') {
                            return window.DataTableManager;
                        }
                        this.initializeDataTables(options);
                        break;
                    case 'notifications':
                        if (typeof window.NotificationManager !== 'undefined') {
                            return window.NotificationManager;
                        }
                        break;
                    case 'modals':
                        if (typeof window.ModalManager !== 'undefined') {
                            return window.ModalManager;
                        }
                        break;
                    case 'fileUploads':
                        if (typeof window.FileUploadManager !== 'undefined') {
                            return window.FileUploadManager;
                        }
                        break;
                    case 'select2':
                        this.initializeSelect2(options);
                        break;
                    case 'tooltips':
                        this.initializeTooltips(options);
                        break;
                }
            },
            
            initializeDataTables: function(options) {
                $('.data-table').each(function() {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            responsive: true,
                            pageLength: 25,
                            language: {
                                search: "Search:",
                                lengthMenu: "Show _MENU_ entries",
                                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                                paginate: {
                                    previous: "Previous",
                                    next: "Next"
                                }
                            },
                            ...options
                        });
                    }
                });
            },
            
            initializeSelect2: function(options) {
                $('.select2-enable:not(.select2-hidden-accessible)').each(function() {
                    $(this).select2({
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Select option',
                        ...options
                    });
                });
            },
            
            initializeTooltips: function(options) {
                $('[data-toggle="tooltip"]:not([data-bs-original-title])').tooltip({
                    placement: 'top',
                    trigger: 'hover',
                    ...options
                });
            }
        };
    },
    
    /**
     * Set up global error handling
     */
    setupGlobalErrorHandling: function() {
        // Catch and log JavaScript errors
        window.addEventListener('error', (event) => {
            console.error('Global JavaScript Error:', {
                message: event.message,
                source: event.filename,
                line: event.lineno,
                column: event.colno,
                error: event.error
            });
            
            // Emit error event for components to handle
            this.emit('global:error', event);
        });
        
        // Catch unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled Promise Rejection:', event.reason);
            
            // Emit promise rejection event
            this.emit('global:promise:rejection', event);
        });
    },
    
    /**
     * Set up performance monitoring
     */
    setupPerformanceMonitoring: function() {
        // Monitor page load performance
        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType('navigation')[0];
            const loadTime = perfData.loadEventEnd - perfData.navigationStart;
            
            if (this.config.debug) {
                console.log(`📊 Page load time: ${loadTime}ms`);
                console.log('📊 Component initialization times:', Object.fromEntries(this.performance.components));
            }
            
            this.emit('performance:page:loaded', { loadTime, components: this.performance.components });
        });
    },
    
    /**
     * Simple event system
     */
    events: {},
    
    on: function(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);
    },
    
    off: function(event, callback) {
        if (this.events[event]) {
            this.events[event] = this.events[event].filter(cb => cb !== callback);
        }
    },
    
    emit: function(event, data) {
        if (this.events[event]) {
            this.events[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`Error in event callback for '${event}':`, error);
                }
            });
        }
    },
    
    /**
     * Get performance report
     */
    getPerformanceReport: function() {
        const totalComponents = this.components.size;
        const initializedComponents = Array.from(this.components.values())
            .filter(comp => comp.initialized).length;
        
        return {
            totalComponents,
            initializedComponents,
            initializationRate: totalComponents > 0 ? (initializedComponents / totalComponents) * 100 : 0,
            componentTimes: Object.fromEntries(this.performance.components),
            totalInitTime: Array.from(this.performance.components.values())
                .reduce((sum, time) => sum + time, 0)
        };
    },
    
    /**
     * Get component status
     */
    getStatus: function() {
        const components = {};
        
        for (const [name, data] of this.components.entries()) {
            components[name] = {
                initialized: data.initialized,
                hasInstance: data.instance !== null,
                autoDetected: data.options.autoDetected || false,
                initTime: this.performance.components.get(name) || null
            };
        }
        
        return {
            config: this.config,
            components: components,
            performance: this.getPerformanceReport()
        };
    }
};

// Auto-initialize when DOM is ready with performance optimization
document.addEventListener('DOMContentLoaded', function() {
    // Check if manual initialization is disabled
    if (window.adminPanelConfig?.autoInit !== false) {
        // Use requestAnimationFrame to defer initialization until after DOM painting
        requestAnimationFrame(() => {
            CoreManager.init(window.adminPanelConfig || {});
        });
    }
});

// Expose globally
window.CoreManager = CoreManager;

// Also expose as AdminPanel for easier access
window.AdminPanel = {
    core: CoreManager,
    get: (name) => CoreManager.get(name),
    has: (name) => CoreManager.has(name),
    init: (options) => CoreManager.init(options),
    status: () => CoreManager.getStatus()
};