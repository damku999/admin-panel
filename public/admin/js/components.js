// Component JavaScript functions

// Search Field Component Functions
function clearSearchField(fieldId) {
    const input = document.getElementById(fieldId);
    const clearBtn = input.parentElement.querySelector('.clear-search-btn');
    
    input.value = '';
    clearBtn.style.display = 'none';
    
    // Trigger change event to update any listeners
    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
    
    // Focus back to input
    input.focus();
}

// Date Range Picker Component Functions
function clearDateRange(startId, endId) {
    const startInput = document.getElementById(startId);
    const endInput = document.getElementById(endId);
    const clearBtn = startInput.closest('.date-range-picker-container').querySelector('.clear-date-range-btn');
    
    startInput.value = '';
    endInput.value = '';
    clearBtn.style.display = 'none';
    
    // Trigger change events
    [startInput, endInput].forEach(input => {
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    });
}

// Auto-validation: end date should be >= start date
function validateDateRange(startId, endId) {
    const startInput = document.getElementById(startId);
    const endInput = document.getElementById(endId);
    
    if (startInput.value && endInput.value) {
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);
        
        if (endDate < startDate) {
            endInput.value = startInput.value;
            show_notification('warning', 'End date cannot be before start date');
        }
    }
}

// Export Button Component Functions
function initiateExport(url, format, customParams = {}) {
    if (!url) {
        show_notification('error', 'Export URL not configured');
        return;
    }
    
    // Show progress modal for AJAX exports
    const isAjax = document.querySelector('.export-btn[data-ajax="true"]');
    if (isAjax) {
        showExportProgress();
        performAjaxExport(url, format, customParams);
    } else {
        performDirectExport(url, format, customParams);
    }
}

function performDirectExport(url, format, customParams = {}) {
    // Build URL with parameters
    const params = new URLSearchParams({
        format: format,
        ...customParams
    });
    
    // Get current page filters if any
    const currentFilters = getCurrentPageFilters();
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key]) {
            params.append(key, currentFilters[key]);
        }
    });
    
    const exportUrl = `${url}?${params.toString()}`;
    
    // Direct download
    window.location.href = exportUrl;
}

function performAjaxExport(url, format, customParams = {}) {
    const params = {
        format: format,
        ...customParams,
        ...getCurrentPageFilters()
    };
    
    $.ajax({
        url: url,
        method: 'GET',
        data: params,
        xhrFields: {
            responseType: 'blob'
        },
        headers: {
            'Accept': 'application/json'
        },
        success: function(blob, status, xhr) {
            hideExportProgress();
            
            // Get filename from content-disposition header
            const disposition = xhr.getResponseHeader('Content-Disposition');
            let filename = `export_${new Date().toISOString().slice(0, 10)}.${format}`;
            
            if (disposition) {
                const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            
            show_notification('success', 'Export completed successfully');
        },
        error: function(xhr) {
            hideExportProgress();
            
            let errorMessage = 'Export failed';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            show_notification('error', errorMessage);
        },
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.addEventListener('progress', function(evt) {
                if (evt.lengthComputable) {
                    const percentComplete = (evt.loaded / evt.total) * 100;
                    updateExportProgress(percentComplete);
                }
            }, false);
            return xhr;
        }
    });
}

function getCurrentPageFilters() {
    const filters = {};
    
    // Get search input value
    const searchInput = document.querySelector('input[name="search"], #search, .search-input');
    if (searchInput && searchInput.value.trim()) {
        filters.search = searchInput.value.trim();
    }
    
    // Get status filter
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect && statusSelect.value) {
        filters.status = statusSelect.value;
    }
    
    // Get date range
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');
    if (startDate && startDate.value) {
        filters.start_date = startDate.value;
    }
    if (endDate && endDate.value) {
        filters.end_date = endDate.value;
    }
    
    // Get custom form data if form ID is specified
    const button = document.querySelector('.export-btn[data-filter-form-id]');
    if (button) {
        const formId = button.dataset.filterFormId;
        const form = document.getElementById(formId);
        if (form) {
            const formData = new FormData(form);
            formData.forEach((value, key) => {
                if (value && value.trim()) {
                    filters[key] = value.trim();
                }
            });
        }
    }
    
    return filters;
}

function showExportFiltersModal() {
    $('#exportFiltersModal').modal('show');
}

function exportWithFilters() {
    const form = document.getElementById('exportFiltersForm');
    const formData = new FormData(form);
    
    const filters = {};
    formData.forEach((value, key) => {
        if (value && value.trim()) {
            filters[key] = value.trim();
        }
    });
    
    const button = document.querySelector('.export-btn');
    const url = button.dataset.exportUrl;
    const format = button.dataset.format || 'xlsx';
    
    // Hide modal
    $('#exportFiltersModal').modal('hide');
    
    // Perform export with filters
    initiateExport(url, format, filters);
}

function showExportProgress() {
    $('#exportProgressModal').modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    
    // Reset progress
    updateExportProgress(0);
    document.querySelector('.export-progress-text').textContent = 'Preparing Export...';
}

function hideExportProgress() {
    $('#exportProgressModal').modal('hide');
}

function updateExportProgress(percentage) {
    const progressBar = document.querySelector('#exportProgressModal .progress-bar');
    const progressContainer = document.querySelector('#exportProgressModal .progress');
    const progressText = document.querySelector('.export-progress-text');
    
    if (percentage > 0) {
        progressContainer.style.display = 'block';
        progressBar.style.width = percentage + '%';
        progressText.textContent = `Exporting... ${Math.round(percentage)}%`;
    }
}

// Bootstrap 4 Dropdown functionality (jQuery implementation)
$(document).ready(function() {
    // Handle dropdown toggle clicks
    $(document).on('click', '[data-toggle="dropdown"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $this = $(this);
        var $dropdown = $this.closest('.btn-group, .dropdown');
        var $menu = $dropdown.find('.dropdown-menu');
        
        // Close other dropdowns first
        $('.dropdown-menu.show').not($menu).removeClass('show').hide();
        $('.btn-group, .dropdown').not($dropdown).removeClass('show');
        
        // Toggle current dropdown
        if ($menu.hasClass('show')) {
            $menu.removeClass('show').hide();
            $dropdown.removeClass('show');
            $this.attr('aria-expanded', 'false');
        } else {
            $menu.addClass('show').show();
            $dropdown.addClass('show'); 
            $this.attr('aria-expanded', 'true');
        }
    });
    
    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.btn-group, .dropdown').length) {
            $('.dropdown-menu.show').removeClass('show').hide();
            $('.btn-group.show, .dropdown.show').removeClass('show');
            $('[data-toggle="dropdown"]').attr('aria-expanded', 'false');
        }
    });
    
    // Prevent dropdown menu clicks from closing dropdown
    $(document).on('click', '.dropdown-menu', function(e) {
        e.stopPropagation();
    });
    
    // Close dropdown when clicking menu items with links
    $(document).on('click', '.dropdown-menu .dropdown-item', function(e) {
        var $dropdown = $(this).closest('.btn-group, .dropdown');
        var $menu = $dropdown.find('.dropdown-menu');
        
        // Only close if it's a real link (not onclick handler)
        if ($(this).attr('href') !== '#') {
            $menu.removeClass('show').hide();
            $dropdown.removeClass('show');
            $dropdown.find('[data-toggle="dropdown"]').attr('aria-expanded', 'false');
        }
    });
});

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search fields
    const searchInputs = document.querySelectorAll('.search-input');
    
    searchInputs.forEach(function(input) {
        const clearBtn = input.parentElement.querySelector('.clear-search-btn');
        if (!clearBtn) return;
        
        // Show/hide on input
        input.addEventListener('input', function() {
            clearBtn.style.display = this.value.trim() ? 'block' : 'none';
        });
        
        // Initial state
        clearBtn.style.display = input.value.trim() ? 'block' : 'none';
    });

    // Initialize date range pickers
    const dateRangeContainers = document.querySelectorAll('.date-range-picker-container');
    
    dateRangeContainers.forEach(function(container) {
        const startInput = container.querySelector('.date-range-start');
        const endInput = container.querySelector('.date-range-end');
        const clearBtn = container.querySelector('.clear-date-range-btn');
        
        if (!startInput || !endInput || !clearBtn) return;
        
        // Show/hide clear button
        function toggleClearButton() {
            const hasValues = startInput.value.trim() || endInput.value.trim();
            clearBtn.style.display = hasValues ? 'block' : 'none';
        }
        
        // Add validation
        function setupValidation() {
            const startId = startInput.id;
            const endId = endInput.id;
            
            startInput.addEventListener('change', function() {
                if (endInput.value) {
                    validateDateRange(startId, endId);
                }
                toggleClearButton();
            });
            
            endInput.addEventListener('change', function() {
                if (startInput.value) {
                    validateDateRange(startId, endId);
                }
                toggleClearButton();
            });
        }
        
        // Initial state
        toggleClearButton();
        setupValidation();
    });

    // Initialize export buttons keyboard support
    document.querySelectorAll('.export-format-option').forEach(option => {
        option.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
});