/**
 * Clean Admin Portal JavaScript Bundle
 * Pure Bootstrap 5 - No SB Admin 2 conflicts
 */

// Import shared bootstrap configuration
require('../bootstrap');

// Clean admin-specific JavaScript
$(document).ready(function() {
    
    // Initialize Bootstrap 5 tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap 5 popovers  
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Initialize Select2 dropdowns (Bootstrap 5 compatible)
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // Initialize date pickers
    if (typeof $.fn.datepicker !== 'undefined') {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    }

    // Scroll to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    $('.scroll-to-top').click(function() {
        $('html, body').animate({scrollTop: 0}, 600);
        return false;
    });

    // Form validation enhancement with proper error handling
    $('form').on('submit', function(e) {
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalBtnText = $submitBtn.html();
        
        // Store original button text if not already stored
        if (!$submitBtn.data('original-text')) {
            $submitBtn.data('original-text', originalBtnText);
        }
        
        // Check if form has HTML5 validation and if it's valid
        var formElement = $form[0];
        if (formElement.checkValidity && !formElement.checkValidity()) {
            // Form is invalid, don't disable button
            return;
        }
        
        // Check for client-side validation errors
        var hasValidationErrors = $form.find('.is-invalid').length > 0 || 
                                 $form.find('.error').length > 0 ||
                                 $form.find('.has-error').length > 0;
        
        if (hasValidationErrors) {
            // Don't disable button if there are validation errors
            return;
        }
        
        // Only disable if validation passes
        $submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        // Re-enable button after 10 seconds as fallback
        setTimeout(function() {
            if ($submitBtn.prop('disabled')) {
                $submitBtn.prop('disabled', false)
                         .html($submitBtn.data('original-text') || originalBtnText);
            }
        }, 10000);
    });
    
    // Re-enable submit buttons when validation errors are detected
    $(document).on('invalid', 'input, select, textarea', function() {
        var $form = $(this).closest('form');
        var $submitBtn = $form.find('button[type="submit"]');
        if ($submitBtn.prop('disabled')) {
            $submitBtn.prop('disabled', false)
                     .html($submitBtn.data('original-text') || $submitBtn.html().replace('<i class="fas fa-spinner fa-spin"></i> Processing...', '<i class="fas fa-save me-1"></i>Save'));
        }
    });
    
    console.log('Clean Bootstrap 5 admin portal initialized');
});