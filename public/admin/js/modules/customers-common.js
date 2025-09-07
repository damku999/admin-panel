/**
 * Customers Module - Shared JavaScript Functions
 * Used across customer add, edit, and index pages
 */

// Global variables
window.currentCustomer = null;
window.currentCustomerId = null;

/**
 * Initialize customer form behavior
 */
function initializeCustomerForm() {
    // Toggle GST sections based on customer type
    initializeGSTSections();
    
    // Setup text input uppercase conversion
    initializeTextInputFormatting();
    
    // Setup family group functionality if present
    initializeFamilyGroupFeatures();
}

/**
 * Initialize GST sections visibility toggle
 */
function initializeGSTSections() {
    const customerTypeSelect = document.getElementById('customerType');
    const gstNumberSection = document.getElementById('gstNumberSection');
    const gstDocumentSection = document.getElementById('gstDocumentSection');

    if (!customerTypeSelect) return;

    const toggleGSTSections = () => {
        const customerType = customerTypeSelect.value;
        const shouldShow = customerType === 'Corporate';
        
        if (gstNumberSection) {
            gstNumberSection.style.display = shouldShow ? 'block' : 'none';
            
            // Toggle required attribute on GST number input
            const gstInput = gstNumberSection.querySelector('input[name="gst_number"]');
            if (gstInput) {
                gstInput.required = shouldShow;
            }
        }
        
        if (gstDocumentSection) {
            gstDocumentSection.style.display = shouldShow ? 'block' : 'none';
        }
    };

    // Initial setup
    toggleGSTSections();
    
    // Event listener for customer type change
    customerTypeSelect.addEventListener('change', toggleGSTSections);
}

/**
 * Initialize text input formatting (uppercase conversion)
 */
function initializeTextInputFormatting() {
    const textInputs = document.querySelectorAll('input[type="text"]');
    
    textInputs.forEach(input => {
        // Skip certain fields that shouldn't be uppercase
        const skipFields = ['email', 'path', 'website', 'password'];
        const shouldSkip = skipFields.some(field => input.name.includes(field));
        
        if (!shouldSkip) {
            input.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
        }
    });
}

/**
 * Initialize family group related features
 */
function initializeFamilyGroupFeatures() {
    const familyTypeSelect = document.getElementById('family_type');
    const familyGroupSection = document.getElementById('familyGroupSection');
    
    if (!familyTypeSelect || !familyGroupSection) return;
    
    const toggleFamilyGroupSection = () => {
        const familyType = familyTypeSelect.value;
        const shouldShow = familyType === 'Family Member';
        
        familyGroupSection.style.display = shouldShow ? 'block' : 'none';
        
        // Toggle required attribute on family group select
        const familyGroupSelect = familyGroupSection.querySelector('select[name="family_group_id"]');
        if (familyGroupSelect) {
            familyGroupSelect.required = shouldShow;
        }
    };
    
    // Initial setup
    toggleFamilyGroupSection();
    
    // Event listener for family type change
    familyTypeSelect.addEventListener('change', toggleFamilyGroupSection);
}

/**
 * Show customer WhatsApp modal
 */
function showCustomerWhatsAppModal(customerId, customerData = null) {
    window.currentCustomerId = customerId;
    
    if (customerData) {
        window.currentCustomer = customerData;
        // Populate modal with customer data
        $('#whatsapp-customer-name').text(customerData.name);
        $('#whatsapp-customer-mobile').text(customerData.mobile);
    }
    
    // Clear message field
    const messageField = document.getElementById('whatsapp_message');
    if (messageField) {
        messageField.value = '';
    }
    
    // Show modal
    showModal('customerWhatsAppModal', {
        closeOnBackdrop: false,
        closeOnEscape: false
    });
}

/**
 * Send WhatsApp message to customer
 */
function sendCustomerWhatsApp() {
    const message = document.getElementById('whatsapp_message').value.trim();
    
    if (!message) {
        show_notification('error', 'Please enter a message');
        return;
    }
    
    if (!window.currentCustomerId) {
        show_notification('error', 'Customer ID not found');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('sendWhatsAppBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    // Submit via AJAX
    $.ajax({
        url: `/customers/send-whatsapp/${window.currentCustomerId}`,
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            message: message
        },
        success: function(response) {
            if (response.success) {
                show_notification('success', response.message || 'WhatsApp message sent successfully');
                hideModal('customerWhatsAppModal');
            } else {
                show_notification('error', response.message || 'Failed to send WhatsApp message');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while sending the message';
            
            if (xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON?.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join(', ');
            }
            
            show_notification('error', errorMessage);
        },
        complete: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

/**
 * Toggle customer status (enable/disable)
 */
function toggleCustomerStatus(customerId, currentStatus) {
    const newStatus = currentStatus === 1 ? 0 : 1;
    const statusText = newStatus === 1 ? 'enable' : 'disable';
    
    showConfirmationModal({
        title: `${statusText.charAt(0).toUpperCase() + statusText.slice(1)} Customer`,
        message: `Are you sure you want to ${statusText} this customer?`,
        confirmText: `Yes, ${statusText.charAt(0).toUpperCase() + statusText.slice(1)}`,
        confirmClass: newStatus === 1 ? 'btn-success' : 'btn-warning',
        onConfirm: function() {
            showLoading(`${statusText.charAt(0).toUpperCase() + statusText.slice(1)}ing customer...`);
            
            $.ajax({
                url: `/customers/update/status/${customerId}/${newStatus}`,
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        show_notification('success', response.message);
                        // Reload the page to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        show_notification('error', response.message || `Failed to ${statusText} customer`);
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const errorMessage = xhr.responseJSON?.message || `An error occurred while ${statusText}ing the customer`;
                    show_notification('error', errorMessage);
                }
            });
        }
    });
}

/**
 * Delete customer with confirmation
 */
function deleteCustomer(customerId, customerName) {
    showConfirmationModal({
        title: 'Delete Customer',
        message: `Are you sure you want to delete "${customerName}"? This action cannot be undone.`,
        confirmText: 'Yes, Delete',
        confirmClass: 'btn-danger',
        onConfirm: function() {
            showLoading('Deleting customer...');
            
            $.ajax({
                url: `/customers/${customerId}`,
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        show_notification('success', response.message);
                        // Reload the page to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        show_notification('error', response.message || 'Failed to delete customer');
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while deleting the customer';
                    show_notification('error', errorMessage);
                }
            });
        }
    });
}

/**
 * Initialize customer search functionality
 */
function initializeCustomerSearch() {
    const searchInput = document.getElementById('customerSearch');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                performCustomerSearch(query);
            }, 300);
        } else {
            // Clear search results or reset to default view
            resetCustomerList();
        }
    });
}

/**
 * Perform customer search via AJAX
 */
function performCustomerSearch(query) {
    $.ajax({
        url: '/customers/search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            if (response.success) {
                updateCustomerList(response.data);
            }
        },
        error: function(xhr) {
            console.error('Customer search failed:', xhr);
        }
    });
}

/**
 * Update customer list with search results
 */
function updateCustomerList(customers) {
    const tbody = document.querySelector('#customersTable tbody');
    if (!tbody) return;
    
    if (customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="100%" class="text-center">No customers found</td></tr>';
        return;
    }
    
    let html = '';
    customers.forEach(customer => {
        html += generateCustomerRow(customer);
    });
    
    tbody.innerHTML = html;
}

/**
 * Generate HTML for customer table row
 */
function generateCustomerRow(customer) {
    const statusBadge = customer.status === 1 ? 
        '<span class="badge badge-success">Active</span>' : 
        '<span class="badge badge-secondary">Inactive</span>';
    
    return `
        <tr>
            <td>${customer.name}</td>
            <td>${customer.email || 'N/A'}</td>
            <td>${customer.mobile_number || 'N/A'}</td>
            <td>${customer.customer_type}</td>
            <td class="text-center">${statusBadge}</td>
            <td class="text-center">
                <div class="d-flex flex-wrap justify-content-center" style="gap: 6px;">
                    <!-- Action buttons would go here -->
                </div>
            </td>
        </tr>
    `;
}

/**
 * Reset customer list to default view
 */
function resetCustomerList() {
    // This would typically reload the original customer list
    window.location.reload();
}

/**
 * Initialize customer index page functionality
 */
function initializeCustomerIndex() {
    // Apply custom dropdown styling
    $('.form-select').each(function() {
        $(this).addClass('custom-dropdown');
    });
    
    // Initialize table sorting functionality
    initializeTableSorting();
    
    // Enhanced confirmation for status changes
    $('a[href*="customers.status"]').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const isActivating = href.includes('status=1');
        const action = isActivating ? 'activate' : 'deactivate';
        const customerName = $(this).closest('tr').find('td:first strong').text();

        if (window.CoreManager && CoreManager.has('modals')) {
            const modalManager = CoreManager.get('modals');
            modalManager.confirm({
                title: `Confirm Customer ${action.charAt(0).toUpperCase() + action.slice(1)}`,
                message: `Are you sure you want to ${action} customer "${customerName}"?`,
                confirmText: `Yes, ${action.charAt(0).toUpperCase() + action.slice(1)}`,
                confirmClass: isActivating ? 'btn-success' : 'btn-warning',
                onConfirm: function() {
                    // Show loading state
                    if (window.CoreManager && CoreManager.has('notifications')) {
                        const notificationManager = CoreManager.get('notifications');
                        notificationManager.loading(
                            `${action.charAt(0).toUpperCase() + action.slice(1)}ing customer...`
                        );
                    }
                    window.location.href = href;
                }
            });
        } else {
            // Fallback confirmation
            if (confirm(`Are you sure you want to ${action} customer "${customerName}"?`)) {
                window.location.href = href;
            }
        }
    });
}

/**
 * Initialize table sorting functionality for Laravel pagination
 */
function initializeTableSorting() {
    // Get current sort state from window object (passed from Laravel)
    const currentSort = window.currentSort || { column: 'name', direction: 'asc' };
    
    console.log('Current sort state:', currentSort);
    
    // Update header icons based on current sort state
    updateSortIcons(currentSort.column, currentSort.direction);
    
    // Add click handlers to sortable headers
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function(e) {
            e.preventDefault();
            
            const sortColumn = this.dataset.sort; // Get column from data-sort attribute
            console.log('Clicked column:', sortColumn);
            
            if (!sortColumn) return;
            
            // Determine sort direction
            let direction = 'asc';
            if (currentSort.column === sortColumn && currentSort.direction === 'asc') {
                direction = 'desc';
            }
            
            console.log('New direction:', direction);
            
            // Build new URL with sort parameters
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('sort', sortColumn);
            newUrl.searchParams.set('direction', direction);
            
            console.log('Navigating to:', newUrl.toString());
            
            // Navigate to sorted page
            window.location.href = newUrl.toString();
        });
    });
}

/**
 * Update sort icons based on current sort state
 */
function updateSortIcons(sortColumn, direction) {
    console.log('🔍 Updating sort icons for column:', sortColumn, 'direction:', direction);
    
    // Find all sortable headers
    const sortableHeaders = document.querySelectorAll('.sortable');
    console.log('📝 Found sortable headers:', sortableHeaders.length);
    
    sortableHeaders.forEach((header, index) => {
        // Try multiple selectors to find the icon
        let icon = header.querySelector('.sort-icon');
        if (!icon) {
            icon = header.querySelector('.fas');
        }
        if (!icon) {
            icon = header.querySelector('i');
        }
        
        const headerColumn = header.dataset.sort;
        console.log(`📋 Header ${index}: column="${headerColumn}", icon found:`, !!icon);
        
        if (!icon) {
            console.warn('⚠️ No icon found in header:', header);
            return;
        }
        
        // Reset all icons to default sort
        icon.className = 'fas fa-sort ms-1 text-muted sort-icon';
        console.log(`🔄 Reset icon for "${headerColumn}" to default`);
        
        // Set active sort icon for current column
        if (sortColumn === headerColumn) {
            const iconDirection = direction === 'asc' ? 'up' : 'down';
            icon.className = `fas fa-sort-${iconDirection} ms-1 text-primary sort-icon`;
            console.log(`✅ Set active icon for "${headerColumn}" to "sort-${iconDirection}" with classes:`, icon.className);
            
            // Force a style recalculation
            icon.style.display = 'none';
            icon.offsetHeight; // Trigger reflow
            icon.style.display = '';
        }
    });
    
    // Additional verification - log all icon states after update
    setTimeout(() => {
        console.log('🔍 Verification - Current icon states:');
        document.querySelectorAll('.sortable').forEach((header, index) => {
            const icon = header.querySelector('i');
            const headerColumn = header.dataset.sort;
            if (icon) {
                console.log(`   ${index}: ${headerColumn} -> ${icon.className}`);
            }
        });
    }, 100);
}

/**
 * Manual test function for browser console debugging
 * Usage: testSortIcons('name', 'desc')
 */
window.testSortIcons = function(column, direction) {
    console.log('🧪 Manual test - Testing sort icons for:', column, direction);
    updateSortIcons(column, direction);
};

/**
 * Inspect current sort state - for browser console debugging
 */
window.inspectSortState = function() {
    console.log('🔍 Current sort state:', window.currentSort);
    console.log('🔍 Sortable headers found:', document.querySelectorAll('.sortable').length);
    
    document.querySelectorAll('.sortable').forEach((header, index) => {
        const icon = header.querySelector('i');
        const column = header.dataset.sort;
        console.log(`   Header ${index}: ${column}`, {
            'data-sort': column,
            'icon': icon ? icon.className : 'NOT FOUND',
            'element': header,
            'innerHTML': header.innerHTML
        });
    });
};

/**
 * Force add icons to headers if they're missing - browser test function
 */
window.forceAddIcons = function() {
    console.log('🔧 Force adding icons to headers...');
    
    document.querySelectorAll('.sortable').forEach((header, index) => {
        let icon = header.querySelector('i');
        const column = header.dataset.sort;
        
        if (!icon) {
            console.log(`➕ Adding missing icon to ${column}`);
            const span = header.querySelector('span');
            if (span) {
                icon = document.createElement('i');
                icon.className = 'fas fa-sort ms-1 text-muted sort-icon';
                span.appendChild(icon);
            }
        } else {
            console.log(`✅ Icon already exists for ${column}: ${icon.className}`);
        }
    });
    
    // Test icon change
    setTimeout(() => {
        console.log('🧪 Testing icon change after force add...');
        updateSortIcons('name', 'desc');
    }, 500);
};

/**
 * Initialize customer module on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form behavior if on add/edit pages
    if (document.getElementById('customerType') || document.querySelector('form[action*="customers"]')) {
        initializeCustomerForm();
    }
    
    // Initialize search if on index page
    if (document.getElementById('customerSearch')) {
        initializeCustomerSearch();
    }
    
    // Initialize index page functionality
    if (document.getElementById('customersDataTable')) {
        initializeCustomerIndex();
        console.log('✅ Using Laravel pagination - DataTables initialization will be skipped');
        
        // Make inspector functions available for debugging
        console.log('🛠️ Debug functions available:');
        console.log('   - inspectSortState() - Check current state and HTML');
        console.log('   - forceAddIcons() - Add missing icons and test');
        console.log('   - testSortIcons("name", "desc") - Test icon changes');
    }
});