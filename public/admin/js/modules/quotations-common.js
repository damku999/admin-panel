/**
 * Quotations Module - Shared JavaScript Functions
 * Used across quotation create, edit, and show pages
 */

// Global variables
window.currentQuotation = null;
window.currentQuotationId = null;
window.quoteIndex = 0;

/**
 * Initialize quotation form behavior
 */
function initializeQuotationForm() {
    // Initialize Select2 for customer dropdown
    initializeCustomerSelect2();
    
    // Setup IDV calculations
    initializeIdvCalculations();
    
    // Setup form formatting
    initializeFormFormatting();
    
    // Setup quote management
    initializeQuoteManagement();
    
    // Initialize tooltips
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
}

/**
 * Initialize Enhanced Select2 for Customer dropdown
 */
function initializeCustomerSelect2() {
    if (!$('#customer_id').length) return;
    
    $('#customer_id').select2({
        placeholder: 'Search and select customer...',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        escapeMarkup: function(markup) {
            return markup; // Allow HTML in results
        },
        templateResult: function(option) {
            if (!option.id || option.loading) {
                return option.text;
            }
            
            const $option = $(option.element);
            const mobile = $option.data('mobile');
            const customerName = option.text.split(' - ')[0];
            
            if (mobile) {
                return '<div style="padding: 5px;"><strong>' + customerName + '</strong><br><small class="text-muted" style="color: #6c757d;">📱 ' + mobile + '</small></div>';
            }
            
            return '<div style="padding: 5px;">' + customerName + '</div>';
        },
        templateSelection: function(option) {
            if (!option.id) {
                return option.text;
            }
            
            const customerName = option.text.split(' - ')[0];
            return customerName;
        }
    });

    // Auto-populate WhatsApp number when customer is selected
    $('#customer_id').on('select2:select', function (e) {
        const selectedOption = e.params.data;
        const $selectedElement = $(selectedOption.element);
        const mobile = $selectedElement.data('mobile');
        
        if (mobile) {
            $('#whatsapp_number').val(mobile);
            console.log('Auto-populated WhatsApp number:', mobile);
        }
    });

    // Clear WhatsApp number when customer is cleared
    $('#customer_id').on('select2:clear', function (e) {
        $('#whatsapp_number').val('');
        console.log('Cleared WhatsApp number');
    });
}

/**
 * Initialize IDV calculations
 */
function initializeIdvCalculations() {
    // Setup main form IDV calculation
    $('#idv_vehicle, #idv_trailer, #idv_cng_lpg_kit, #idv_electrical_accessories, #idv_non_electrical_accessories')
        .on('input change blur', function() {
            console.log('IDV field changed, calculating total IDV');
            calculateTotalIdv();
        });

    // Setup dynamic quote card IDV calculations
    $(document).on('input change blur', '[name*="[idv_vehicle]"], [name*="[idv_trailer]"], [name*="[idv_cng_lpg_kit]"], [name*="[idv_electrical_accessories]"], [name*="[idv_non_electrical_accessories]"], [name*="[total_idv]"]', function() {
        console.log('Quote card IDV field changed');
        const quoteCard = $(this).closest('.quote-entry');
        if (quoteCard.length > 0) {
            calculateIdvTotal(quoteCard);
        } else {
            console.log('Warning: Could not find quote-entry parent for IDV field');
        }
    });

    // Initial calculation for existing quote cards
    $('.quote-entry').each(function() {
        calculateIdvTotal($(this));
    });
}

/**
 * Calculate total IDV for main form
 */
function calculateTotalIdv() {
    let idvVehicle = parseFloat($('#idv_vehicle').val()) || 0;
    let idvTrailer = parseFloat($('#idv_trailer').val()) || 0;
    let idvCngLpg = parseFloat($('#idv_cng_lpg_kit').val()) || 0;
    let idvElectrical = parseFloat($('#idv_electrical_accessories').val()) || 0;
    let idvNonElectrical = parseFloat($('#idv_non_electrical_accessories').val()) || 0;

    let totalIdv = idvVehicle + idvTrailer + idvCngLpg + idvElectrical + idvNonElectrical;

    // Update the total IDV field
    $('#total_idv').val(totalIdv.toFixed(2));

    // Add visual highlight
    if (totalIdv > 0) {
        $('#total_idv').css('background-color', '#d4edda');
    } else {
        $('#total_idv').css('background-color', '');
    }
}

/**
 * Calculate IDV total for quote cards
 */
function calculateIdvTotal(quoteCard) {
    const idvFields = [
        'idv_vehicle', 'idv_trailer', 'idv_cng_lpg_kit', 
        'idv_electrical_accessories', 'idv_non_electrical_accessories'
    ];
    
    let total = 0;
    
    idvFields.forEach(field => {
        const value = parseFloat(quoteCard.find(`[name*="[${field}]"]`).val()) || 0;
        total += value;
    });
    
    const totalField = quoteCard.find(`[name*="[total_idv]"]`);
    totalField.val(total.toFixed(2));
    
    // Add visual highlight
    if (total > 0) {
        totalField.css('background-color', '#d4edda');
    } else {
        totalField.css('background-color', '');
    }
}

/**
 * Initialize form formatting
 */
function initializeFormFormatting() {
    // Convert vehicle number to uppercase
    $(document).on('input', '#vehicle_number, [name*="vehicle_number"]', function() {
        this.value = this.value.toUpperCase();
    });

    // Form submission enhancement
    $('#quotationForm').on('submit', function() {
        $('#submitBtn').prop('disabled', true);
        $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Creating...');
    });
}

/**
 * Initialize quote management
 */
function initializeQuoteManagement() {
    // Initialize quote index
    if (typeof quotationFormData !== 'undefined' && quotationFormData.existingQuoteCount) {
        window.quoteIndex = quotationFormData.existingQuoteCount;
    }
    
    // Setup premium calculations for dynamic content
    $(document).on('input change blur', '.premium-field', function() {
        const quoteCard = $(this).closest('.quote-entry');
        if (quoteCard.length > 0) {
            calculateQuotePremium(quoteCard);
        }
    });
    
    // Setup addon cover calculations
    $(document).on('change', '.addon-checkbox', function() {
        const quoteCard = $(this).closest('.quote-entry');
        updateAddonPremiums(quoteCard);
    });
}

/**
 * Add new quote form
 */
function addQuoteForm(existingData = {}, existingIndex = null) {
    const currentIndex = existingIndex !== null ? existingIndex : window.quoteIndex;

    // Show loading
    showLoading('Loading quote form...');

    // Make AJAX call to get the quote form HTML
    $.ajax({
        url: window.routes?.quotationForm || '/quotations/get-quote-form',
        type: 'GET',
        data: {
            index: currentIndex,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            hideLoading();
            
            // Append the rendered HTML to the container
            $('#quotesContainer').append(response);
            $('#noQuotesMessage').hide();

            // Set the selected insurance company if restoring data
            if (existingData.insurance_company_id) {
                $(`[data-index="${currentIndex}"] .company-select`).val(existingData.insurance_company_id);
            }

            // Populate existing form data if provided
            if (Object.keys(existingData).length > 0) {
                populateQuoteFormData(currentIndex, existingData);
            }

            // Only increment quoteIndex when adding new forms (not restoring)
            if (existingIndex === null) {
                window.quoteIndex++;
            }

            // Initialize new form elements
            initializeNewQuoteForm(currentIndex);
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Error loading quote form:', error);
            show_notification('error', 'Failed to load quote form');
        }
    });
}

/**
 * Populate quote form with existing data
 */
function populateQuoteFormData(index, data) {
    const quoteCard = $(`.quote-entry[data-index="${index}"]`);
    
    // Populate all form fields
    Object.keys(data).forEach(function(key) {
        const value = data[key];
        const input = quoteCard.find(`[name="companies[${index}][${key}]"]`);
        
        if (input.is('select')) {
            input.val(value);
        } else if (input.is(':checkbox')) {
            input.prop('checked', !!value);
        } else {
            input.val(value || '');
        }
    });

    // Trigger premium calculation if data exists
    if (data.basic_od_premium || data.tp_premium || data.total_addon_premium || data.cng_lpg_premium) {
        calculateQuotePremium(quoteCard);
    }
}

/**
 * Initialize new quote form elements
 */
function initializeNewQuoteForm(index) {
    const quoteCard = $(`.quote-entry[data-index="${index}"]`);
    
    // Initialize any Select2 elements in the new form
    quoteCard.find('.select2-enable').each(function() {
        $(this).select2({
            width: '100%',
            placeholder: $(this).data('placeholder') || 'Select option'
        });
    });
    
    // Initialize tooltips in the new form
    if (typeof $().tooltip === 'function') {
        quoteCard.find('[data-toggle="tooltip"]').tooltip();
    }
}

/**
 * Remove quote form
 */
function removeQuoteForm(index) {
    showConfirmationModal({
        title: 'Remove Quote',
        message: 'Are you sure you want to remove this quote?',
        confirmText: 'Yes, Remove',
        confirmClass: 'btn-danger',
        onConfirm: function() {
            $(`.quote-entry[data-index="${index}"]`).fadeOut(300, function() {
                $(this).remove();
                
                // Show "no quotes" message if no quotes remain
                if ($('.quote-entry').length === 0) {
                    $('#noQuotesMessage').show();
                }
            });
        }
    });
}

/**
 * Calculate quote premium
 */
function calculateQuotePremium(quoteCard) {
    const basicOd = parseFloat(quoteCard.find('[name*="[basic_od_premium]"]').val()) || 0;
    const tp = parseFloat(quoteCard.find('[name*="[tp_premium]"]').val()) || 0;
    const addon = parseFloat(quoteCard.find('[name*="[total_addon_premium]"]').val()) || 0;
    const cngLpg = parseFloat(quoteCard.find('[name*="[cng_lpg_premium]"]').val()) || 0;
    
    const subtotal = basicOd + tp + addon + cngLpg;
    const gst = subtotal * 0.18; // 18% GST
    const total = subtotal + gst;
    
    // Update fields
    quoteCard.find('[name*="[gst_amount]"]').val(gst.toFixed(2));
    quoteCard.find('[name*="[total_premium]"]').val(total.toFixed(2));
    
    // Visual feedback
    if (total > 0) {
        quoteCard.find('[name*="[total_premium]"]').css('background-color', '#d1ecf1');
    }
}

/**
 * Update addon premiums
 */
function updateAddonPremiums(quoteCard) {
    let totalAddon = 0;
    
    quoteCard.find('.addon-checkbox:checked').each(function() {
        const premium = parseFloat($(this).data('premium')) || 0;
        totalAddon += premium;
    });
    
    quoteCard.find('[name*="[total_addon_premium]"]').val(totalAddon.toFixed(2));
    
    // Recalculate total premium
    calculateQuotePremium(quoteCard);
}

/**
 * Send quotation via WhatsApp
 */
function sendQuotationWhatsApp(quotationId, quotationData = null) {
    window.currentQuotationId = quotationId;
    
    if (quotationData) {
        window.currentQuotation = quotationData;
    }
    
    // Show WhatsApp modal
    showModal('quotationWhatsAppModal', {
        closeOnBackdrop: false,
        closeOnEscape: false
    });
}

/**
 * Submit WhatsApp quotation
 */
function submitQuotationWhatsApp() {
    const customMessage = document.getElementById('whatsapp_custom_message')?.value.trim() || '';
    
    if (!window.currentQuotationId) {
        show_notification('error', 'Quotation ID not found');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('sendQuotationWhatsAppBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    // Submit via AJAX
    $.ajax({
        url: `/quotations/send-whatsapp/${window.currentQuotationId}`,
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            custom_message: customMessage
        },
        success: function(response) {
            if (response.success) {
                show_notification('success', response.message || 'Quotation sent via WhatsApp successfully');
                hideModal('quotationWhatsAppModal');
            } else {
                show_notification('error', response.message || 'Failed to send quotation');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while sending the quotation';
            
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
 * Toggle quotation recommendation
 */
function toggleRecommendation(quotationId, companyIndex) {
    const checkbox = $(`#recommend_${companyIndex}`);
    const noteSection = $(`#recommendation_note_section_${companyIndex}`);
    const noteInput = $(`#recommendation_note_${companyIndex}`);
    
    if (checkbox.is(':checked')) {
        noteSection.show();
        noteInput.prop('required', true);
    } else {
        noteSection.hide();
        noteInput.prop('required', false);
        noteInput.val('');
    }
}

/**
 * Initialize quotation module on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form behavior if on create/edit pages
    if (document.getElementById('quotationForm') || $('.quote-entry').length > 0) {
        initializeQuotationForm();
    }
    
    // Setup recommendation toggles
    $(document).on('change', '.recommendation-checkbox', function() {
        const companyIndex = $(this).data('company-index');
        toggleRecommendation(null, companyIndex);
    });
    
    // Initialize existing recommendation states
    $('.recommendation-checkbox').each(function() {
        const companyIndex = $(this).data('company-index');
        toggleRecommendation(null, companyIndex);
    });
});