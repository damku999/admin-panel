/**
 * Advanced Data Table Management System
 * Provides enhanced functionality for all data tables in the admin panel
 */

const DataTableManager = {
    
    // Table instances registry
    tables: new Map(),
    
    // Default configuration
    defaultConfig: {
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        processing: true,
        serverSide: false,
        stateSave: true,
        stateDuration: 60 * 60 * 24, // 24 hours
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries found",
            infoFiltered: "(filtered from _MAX_ total entries)",
            loadingRecords: "Loading...",
            processing: "Processing...",
            zeroRecords: "No matching records found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
              '<"row"<"col-sm-12"tr>>' +
              '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        autoWidth: false,
        columnDefs: [
            { targets: 'no-sort', orderable: false },
            { targets: 'text-center', className: 'text-center' },
            { targets: 'text-right', className: 'text-right' }
        ]
    },
    
    // Initialize the data table manager
    init: function(options = {}) {
        this.config = { ...this.defaultConfig, ...options };
        
        // Auto-discover and initialize tables
        this.discoverTables();
        
        // Set up global event handlers
        this.setupGlobalEvents();
        
        console.log('📊 DataTableManager initialized');
    },
    
    /**
     * Discover and initialize all data tables on the page
     */
    discoverTables: function() {
        // Cache all DOM queries at once for better performance
        const paginationElement = document.querySelector('.pagination');
        const searchFormElement = document.querySelector('form[role="search"]');
        const allTables = document.querySelectorAll('table.data-table, table[data-datatable]');
        
        const hasLaravelPagination = paginationElement !== null;
        const hasCustomSearchForm = searchFormElement !== null;
        
        if (hasLaravelPagination || hasCustomSearchForm) {
            console.log('⚠️ Laravel pagination/search detected - DataTables initialization skipped');
            console.log(`   Laravel pagination: ${hasLaravelPagination ? 'Yes' : 'No'}`);
            console.log(`   Custom search form: ${hasCustomSearchForm ? 'Yes' : 'No'}`);
            console.log('   Applying basic styling only to preserve existing functionality');
            
            // Apply only basic Bootstrap styling without DataTables functionality
            allTables.forEach(table => {
                this.applyBasicStyling(table);
            });
            return;
        }
        
        // Initialize DataTables for all found tables
        allTables.forEach(table => {
            this.initializeTable(table);
        });
    },
    
    /**
     * Initialize a specific table
     */
    initializeTable: function(tableElement, customConfig = {}) {
        const table = typeof tableElement === 'string' 
            ? document.getElementById(tableElement) 
            : tableElement;
            
        if (!table) {
            console.error('Table element not found');
            return null;
        }
        
        const tableId = table.id || this.generateTableId();
        if (!table.id) table.id = tableId;
        
        // Skip if already initialized
        if (this.tables.has(tableId)) {
            console.warn(`Table ${tableId} already initialized`);
            return this.tables.get(tableId);
        }
        
        try {
            // Parse configuration from data attributes
            const dataConfig = this.parseDataAttributes(table);
            
            // Merge configurations
            const config = { ...this.defaultConfig, ...dataConfig, ...customConfig };
            
            // Set up AJAX if server-side processing is enabled
            if (config.serverSide && table.dataset.ajaxUrl) {
                config.ajax = {
                    url: table.dataset.ajaxUrl,
                    type: 'GET',
                    data: function(d) {
                        // Add custom parameters
                        if (table.dataset.ajaxParams) {
                            const params = JSON.parse(table.dataset.ajaxParams);
                            Object.assign(d, params);
                        }
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX Error:', error);
                        show_notification('error', 'Failed to load table data');
                    }
                };
            }
            
            // Initialize DataTable
            const dataTable = $(table).DataTable(config);
            
            // Store instance
            const instance = {
                id: tableId,
                element: table,
                dataTable: dataTable,
                config: config,
                initialized: true
            };
            
            this.tables.set(tableId, instance);
            
            // Set up table-specific event handlers
            this.setupTableEvents(instance);
            
            // Apply enhancements
            this.applyEnhancements(instance);
            
            console.log(`📊 Initialized DataTable: ${tableId}`);
            
            return instance;
            
        } catch (error) {
            console.error(`Failed to initialize table ${tableId}:`, error);
            return null;
        }
    },
    
    /**
     * Parse configuration from data attributes
     */
    parseDataAttributes: function(table) {
        const config = {};
        const dataset = table.dataset;
        
        // Basic configuration
        if (dataset.pageLength) config.pageLength = parseInt(dataset.pageLength);
        if (dataset.serverSide) config.serverSide = dataset.serverSide === 'true';
        if (dataset.stateSave) config.stateSave = dataset.stateSave === 'true';
        if (dataset.responsive) config.responsive = dataset.responsive === 'true';
        
        // Ordering configuration
        if (dataset.order) {
            try {
                config.order = JSON.parse(dataset.order);
            } catch (e) {
                console.warn('Invalid data-order attribute');
            }
        }
        
        // Column definitions
        if (dataset.columnDefs) {
            try {
                config.columnDefs = JSON.parse(dataset.columnDefs);
            } catch (e) {
                console.warn('Invalid data-column-defs attribute');
            }
        }
        
        return config;
    },
    
    /**
     * Set up global event handlers
     */
    setupGlobalEvents: function() {
        // Handle responsive changes
        window.addEventListener('resize', this.debounce(() => {
            this.tables.forEach(instance => {
                if (instance.dataTable) {
                    instance.dataTable.columns.adjust().responsive.recalc();
                }
            });
        }, 250));
        
        // Handle tab switches (for tables in tabs)
        document.addEventListener('shown.bs.tab', (event) => {
            this.tables.forEach(instance => {
                if (instance.dataTable && this.isElementVisible(instance.element)) {
                    instance.dataTable.columns.adjust();
                }
            });
        });
    },
    
    /**
     * Set up table-specific event handlers
     */
    setupTableEvents: function(instance) {
        const { dataTable, element } = instance;
        
        // Handle row selection
        if (element.classList.contains('selectable-rows')) {
            this.setupRowSelection(instance);
        }
        
        // Handle inline editing
        if (element.classList.contains('editable')) {
            this.setupInlineEditing(instance);
        }
        
        // Handle export functionality
        if (element.dataset.exportUrl) {
            this.setupExport(instance);
        }
        
        // Custom row actions
        $(element).on('click', '[data-action]', (event) => {
            const action = event.currentTarget.dataset.action;
            const row = dataTable.row($(event.currentTarget).closest('tr'));
            
            this.handleRowAction(action, row, event.currentTarget, instance);
        });
    },
    
    /**
     * Apply enhancements to the table
     */
    applyEnhancements: function(instance) {
        const { element, dataTable } = instance;
        
        // Add loading overlay
        this.addLoadingOverlay(instance);
        
        // Add column filters if enabled
        if (element.classList.contains('column-filters')) {
            this.addColumnFilters(instance);
        }
        
        // Add bulk actions if enabled
        if (element.classList.contains('bulk-actions')) {
            this.addBulkActions(instance);
        }
        
        // Add custom search if enabled
        if (element.dataset.customSearch) {
            this.addCustomSearch(instance);
        }
        
        // Style enhancements
        this.applyStyleEnhancements(instance);
    },
    
    /**
     * Set up row selection functionality
     */
    setupRowSelection: function(instance) {
        const { dataTable, element } = instance;
        
        // Add checkbox column if not exists
        if (!element.querySelector('th input[type="checkbox"]')) {
            const headerCheckbox = '<input type="checkbox" class="select-all-checkbox">';
            dataTable.column(0).header().innerHTML = headerCheckbox;
        }
        
        // Handle select all
        $(element).on('change', '.select-all-checkbox', function() {
            const isChecked = this.checked;
            $(element).find('tbody .row-checkbox').prop('checked', isChecked).trigger('change');
        });
        
        // Handle individual row selection
        $(element).on('change', '.row-checkbox', function() {
            const totalRows = $(element).find('tbody .row-checkbox').length;
            const checkedRows = $(element).find('tbody .row-checkbox:checked').length;
            
            $('.select-all-checkbox').prop('indeterminate', checkedRows > 0 && checkedRows < totalRows);
            $('.select-all-checkbox').prop('checked', checkedRows === totalRows);
            
            // Update bulk actions visibility
            this.updateBulkActionsVisibility(instance, checkedRows);
        }.bind(this));
    },
    
    /**
     * Set up inline editing
     */
    setupInlineEditing: function(instance) {
        const { dataTable, element } = instance;
        
        $(element).on('click', '.editable-cell', function() {
            const cell = dataTable.cell(this);
            const originalValue = cell.data();
            
            const input = $(`<input type="text" class="form-control form-control-sm" value="${originalValue}">`);
            $(this).html(input);
            input.focus().select();
            
            input.on('blur keypress', function(e) {
                if (e.type === 'blur' || e.keyCode === 13) {
                    const newValue = $(this).val();
                    
                    if (newValue !== originalValue) {
                        // Update cell data
                        cell.data(newValue);
                        
                        // Trigger save event
                        $(element).trigger('cell:updated', {
                            row: cell.index().row,
                            column: cell.index().column,
                            oldValue: originalValue,
                            newValue: newValue
                        });
                    } else {
                        cell.data(originalValue);
                    }
                }
                
                if (e.keyCode === 27) { // Escape key
                    cell.data(originalValue);
                }
            });
        });
    },
    
    /**
     * Set up export functionality
     */
    setupExport: function(instance) {
        const { element } = instance;
        const exportUrl = element.dataset.exportUrl;
        
        // Add export button if container exists
        const exportContainer = element.parentNode.querySelector('.datatable-export');
        if (exportContainer) {
            const exportButton = document.createElement('button');
            exportButton.className = 'btn btn-success btn-sm';
            exportButton.innerHTML = '<i class="fas fa-download"></i> Export';
            
            exportButton.addEventListener('click', () => {
                this.exportTable(instance);
            });
            
            exportContainer.appendChild(exportButton);
        }
    },
    
    /**
     * Handle row actions
     */
    handleRowAction: function(action, row, element, instance) {
        const data = row.data();
        const rowElement = row.node();
        
        switch (action) {
            case 'edit':
                this.handleEdit(data, rowElement, instance);
                break;
            case 'delete':
                this.handleDelete(data, rowElement, instance);
                break;
            case 'view':
                this.handleView(data, rowElement, instance);
                break;
            case 'toggle-status':
                this.handleToggleStatus(data, rowElement, instance);
                break;
            default:
                // Custom action
                $(instance.element).trigger('row:action', {
                    action,
                    data,
                    element: rowElement,
                    instance
                });
        }
    },
    
    /**
     * Add loading overlay
     */
    addLoadingOverlay: function(instance) {
        const wrapper = instance.element.closest('.dataTables_wrapper');
        if (wrapper) {
            const overlay = document.createElement('div');
            overlay.className = 'datatable-loading-overlay d-none';
            overlay.innerHTML = `
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-2">Loading data...</div>
                </div>
            `;
            
            wrapper.style.position = 'relative';
            wrapper.appendChild(overlay);
        }
    },
    
    /**
     * Show/hide loading overlay
     */
    showLoading: function(tableId) {
        const instance = this.tables.get(tableId);
        if (instance) {
            const overlay = instance.element.closest('.dataTables_wrapper')
                ?.querySelector('.datatable-loading-overlay');
            if (overlay) {
                overlay.classList.remove('d-none');
            }
        }
    },
    
    hideLoading: function(tableId) {
        const instance = this.tables.get(tableId);
        if (instance) {
            const overlay = instance.element.closest('.dataTables_wrapper')
                ?.querySelector('.datatable-loading-overlay');
            if (overlay) {
                overlay.classList.add('d-none');
            }
        }
    },
    
    /**
     * Refresh table data
     */
    refresh: function(tableId) {
        const instance = this.tables.get(tableId);
        if (instance && instance.dataTable) {
            if (instance.config.serverSide) {
                instance.dataTable.ajax.reload(null, false);
            } else {
                instance.dataTable.draw(false);
            }
        }
    },
    
    /**
     * Get selected rows
     */
    getSelectedRows: function(tableId) {
        const instance = this.tables.get(tableId);
        if (!instance) return [];
        
        const selected = [];
        $(instance.element).find('.row-checkbox:checked').each(function() {
            const row = instance.dataTable.row($(this).closest('tr'));
            selected.push(row.data());
        });
        
        return selected;
    },
    
    /**
     * Export table data
     */
    exportTable: function(instance) {
        const exportUrl = instance.element.dataset.exportUrl;
        if (!exportUrl) {
            console.error('Export URL not configured');
            return;
        }
        
        // Get current filters and search
        const searchValue = instance.dataTable.search();
        const orderData = instance.dataTable.order();
        
        // Build export URL with parameters
        const params = new URLSearchParams({
            search: searchValue,
            order: JSON.stringify(orderData)
        });
        
        // Trigger download
        window.location.href = `${exportUrl}?${params.toString()}`;
    },
    
    /**
     * Utility functions
     */
    generateTableId: function() {
        return 'table_' + Math.random().toString(36).substr(2, 9);
    },
    
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    isElementVisible: function(element) {
        return !!(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
    },
    
    /**
     * Apply basic styling to table without DataTables functionality
     * Used when Laravel pagination is detected
     */
    applyBasicStyling: function(table) {
        // Add responsive wrapper if not already present
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
        
        // Ensure Bootstrap table classes are present
        if (!table.classList.contains('table')) {
            table.classList.add('table');
        }
        
        // Add hover effect if specified
        if (table.classList.contains('table-hover') || table.dataset.hover === 'true') {
            table.classList.add('table-hover');
        }
        
        // Add striped effect if specified
        if (table.classList.contains('table-striped') || table.dataset.striped === 'true') {
            table.classList.add('table-striped');
        }
        
        // Add bordered effect if specified
        if (table.classList.contains('table-bordered') || table.dataset.bordered === 'true') {
            table.classList.add('table-bordered');
        }
        
        console.log(`🎨 Applied basic styling to table: ${table.id || 'unnamed'}`);
    },
    
    /**
     * Apply style enhancements to table
     */
    applyStyleEnhancements: function(instance) {
        const { dataTable, element } = instance;
        const wrapper = element.closest('.dataTables_wrapper');
        
        if (!wrapper) return;
        
        // Add custom CSS classes for styling
        wrapper.classList.add('enhanced-datatable');
        
        // Style the search input
        const searchInput = wrapper.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.classList.add('form-control', 'form-control-sm');
            searchInput.placeholder = 'Search records...';
        }
        
        // Style the length selector
        const lengthSelect = wrapper.querySelector('select');
        if (lengthSelect) {
            lengthSelect.classList.add('form-select', 'form-select-sm');
        }
        
        // Style pagination buttons
        const paginationBtns = wrapper.querySelectorAll('.paginate_button');
        paginationBtns.forEach(btn => {
            if (!btn.classList.contains('previous') && !btn.classList.contains('next')) {
                btn.classList.add('btn', 'btn-sm', 'btn-outline-primary');
            }
        });
        
        // Style info text
        const info = wrapper.querySelector('.dataTables_info');
        if (info) {
            info.classList.add('text-muted', 'small');
        }
        
        // Add responsive wrapper if needed
        if (element.classList.contains('table-responsive-enable')) {
            element.closest('.dataTables_scroll')?.classList.add('table-responsive');
        }
        
        console.log(`🎨 Applied style enhancements to table: ${instance.id}`);
    },
    
    /**
     * Destroy table instance
     */
    destroy: function(tableId) {
        const instance = this.tables.get(tableId);
        if (instance && instance.dataTable) {
            instance.dataTable.destroy();
            this.tables.delete(tableId);
            console.log(`🗑️ Destroyed DataTable: ${tableId}`);
        }
    },
    
    /**
     * Get all table instances
     */
    getAllTables: function() {
        return Array.from(this.tables.values());
    },
    
    /**
     * Get table instance
     */
    getTable: function(tableId) {
        return this.tables.get(tableId);
    }
};

// Export for use with CoreManager
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DataTableManager;
}

// Make available globally
window.DataTableManager = DataTableManager;

// CoreManager will auto-detect and register this component