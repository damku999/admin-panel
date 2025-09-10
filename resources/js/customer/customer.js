/**
 * Customer Portal JavaScript Bundle
 * Modern Bootstrap 5 implementation for customer interface
 */

// Import shared bootstrap configuration
require('../bootstrap');

// Customer portal specific functionality
(function($) {
    "use strict";

    // Mobile navigation toggle
    $('.navbar-toggler').on('click', function() {
        $(this).toggleClass('active');
        $('.navbar-collapse').slideToggle(300);
    });

    // Smooth scrolling for anchor links
    $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event) {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && 
            location.hostname === this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 1000);
            }
        }
    });

    // Enhanced form interactions
    $('.form-floating input, .form-floating textarea').on('focus blur', function() {
        $(this).closest('.form-floating').toggleClass('focused');
    });

    // Auto-hide alerts after 5 seconds
    $('.alert:not(.alert-permanent)').delay(5000).fadeOut(300);

    // Loading states for buttons - handle form submission properly
    $('form').on('submit', function(e) {
        var $form = $(this);
        var $btn = $form.find('button[type="submit"], .btn-submit');
        
        if ($btn.length > 0) {
            var originalText = $btn.html();
            
            // Show loading state only after form validation passes
            $btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...');
            
            // Re-enable after 10 seconds as fallback
            setTimeout(function() {
                $btn.prop('disabled', false).html(originalText);
            }, 10000);
        }
    });

    // Enhanced table responsiveness
    $('.table-responsive').on('scroll', function() {
        if ($(this).scrollLeft() > 0) {
            $(this).addClass('scrolled');
        } else {
            $(this).removeClass('scrolled');
        }
    });

})(jQuery);

// Initialize customer portal components
$(document).ready(function() {
    
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap modals with focus management
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
    });

    // Initialize Select2 for customer portal
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select an option...',
            allowClear: true
        });
    }

    // Initialize date pickers for customer forms
    if (typeof $.fn.datepicker !== 'undefined') {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto'
        });
    }

    // Animate counters/statistics
    $('.counter').each(function() {
        var $this = $(this);
        var countTo = $this.attr('data-count');
        
        $({ countNum: $this.text() }).animate({
            countNum: countTo
        }, {
            duration: 2000,
            easing: 'linear',
            step: function() {
                $this.text(Math.floor(this.countNum));
            },
            complete: function() {
                $this.text(this.countNum.toLocaleString());
            }
        });
    });

    // Enhanced file upload feedback
    $('.form-control[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        var $feedback = $(this).siblings('.file-feedback');
        
        if (fileName) {
            if ($feedback.length === 0) {
                $feedback = $('<small class="file-feedback text-muted"></small>');
                $(this).after($feedback);
            }
            $feedback.text('Selected: ' + fileName).addClass('text-success').removeClass('text-muted');
        }
    });

    // Add loading overlay for page transitions (exclude downloads)
    $('a:not([href^="#"], [href^="javascript:"], [target="_blank"], [href*="download"], [href*=".pdf"], [href*=".doc"], [href*=".xls"])').on('click', function() {
        if (!$(this).hasClass('no-loading')) {
            $('body').append('<div class="page-loading"><div class="spinner-border text-primary" role="status"></div></div>');
        }
    });

    // Handle download links with temporary loading state
    $('a[href*="download"], a[href*=".pdf"], a[href*=".doc"], a[href*=".xls"]').on('click', function() {
        var $btn = $(this);
        var originalHtml = $btn.html();
        
        // Show loading state
        $btn.addClass('disabled').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Downloading...');
        
        // Remove loading state after 3 seconds
        setTimeout(function() {
            $btn.removeClass('disabled').html(originalHtml);
        }, 3000);
    });

    console.log('Customer portal initialized successfully');
});