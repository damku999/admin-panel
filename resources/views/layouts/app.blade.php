<!DOCTYPE html>
<html lang="en">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
{{-- Include Head --}}
@include('common.head')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('common.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('common.header')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                @yield('content')
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('common.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    @include('common.logout-modal')

    <!-- Global Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cancel">Cancel</button>
                    <button type="button" class="btn btn-danger btn-confirm">Yes, Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('js/app.js') }}"></script>

<!-- DataTables Plugin (Load only when needed) -->
<script>
// Load DataTables asynchronously only if tables exist
if (document.querySelector('table.data-table, table[data-datatable]')) {
    const script1 = document.createElement('script');
    script1.src = "{{ asset('admin/vendor/datatables/jquery.dataTables.min.js') }}";
    script1.async = true;
    document.head.appendChild(script1);
    
    script1.onload = function() {
        const script2 = document.createElement('script');
        script2.src = "{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}";
        script2.async = true;
        document.head.appendChild(script2);
    };
}
</script>

<!-- Component Management System -->
<script src="{{ asset('admin/js/utils/helpers.js') }}"></script>
<script src="{{ asset('admin/js/utils/formatters.js') }}"></script>
<script src="{{ asset('admin/js/utils/validators.js') }}"></script>

<!-- Advanced Component Managers -->
<script src="{{ asset('admin/js/components/core-manager.js') }}"></script>
<script src="{{ asset('admin/js/components/notification-manager.js') }}"></script>
<script src="{{ asset('admin/js/components/modal-manager.js') }}"></script>
<script src="{{ asset('admin/js/components/data-table-manager.js') }}"></script>
<script src="{{ asset('admin/js/components/file-upload-manager.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('admin/toastr/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('admin/js/components.js') }}"></script>
    
    <!-- Component Testing Suite (Development Only) -->
    @if(config('app.debug'))
        <script src="{{ asset('admin/js/test-components.js') }}"></script>
    @endif

    @yield('scripts')
    
    <!-- Initialize Component Management System -->
    <script>
        // Initialize core component management
        $(document).ready(function() {
            // Initialize the component manager with debug mode
            CoreManager.init({
                debug: {{ config('app.debug', false) ? 'true' : 'false' }},
                performance: true,
                autoInit: true
            });
            
            // Component managers are now auto-detected and registered by CoreManager
            console.log('🚀 Admin Panel Component System Initialized');
            
            // Initialize global utilities
            Formatters.init();
            Validators.init();
            
            console.log('🚀 Admin Panel Component System Initialized');
        });
    </script>
    
    <!-- Legacy Compatibility Layer -->
    <script>
        function filterDataAjax(url, search_serialized = null) {
            performAjaxOperation({
                async: true,
                type: "GET",
                url: "{{ config('app.url') }}/" + url,
                data: search_serialized,
                loaderMessage: 'Filtering data...',
                showSuccessNotification: false,
                success: function(res) {
                    $("#list_load").html(res);
                },
                complete: function(result) {
                    // Handle session expiration
                    if (result.responseText == '{"error":"Unauthenticated."}') {
                        show_notification('warning', 'Session expired. Redirecting to login...');
                        setTimeout(() => window.location.href = "login", 2000);
                        return;
                    }

                    // Handle form reset
                    if (search_serialized == '&reset=yes') {
                        if ($('#search_form select[name=product_type]').length) {
                            $('#search_form select[name=product_type]').val('');
                        }
                        if ($('#search_form select[name=packaging_type]').length) {
                            $('#search_form select[name=packaging_type]').val('');
                        }
                        $('.select2').select2().on('select2:close', function name(e) {
                            $(this).valid();
                        });
                    }
                }
            });
        }

        function delete_conf_common(record_id, model, display_title, table_id_or_url = '') {
            $('.module_action').html('Delete');
            $('#module_title').html(" " + display_title);
            table_id_or_url = window.location.href;
            $('#delete-btn').attr('onclick', 'delete_common("' + record_id + '","' + model + '","' + table_id_or_url +
                '","' + display_title + '")');
            showModal('delete_confirm');
            return true;
        }

        function delete_common(record_id, model, table_id_or_url = '', display_title = '') {
            hideModal('delete_confirm');
            
            performAjaxOperation({
                type: "POST",
                url: "{{ route('delete_common') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    record_id: record_id,
                    model: model,
                    table_id_or_url: table_id_or_url,
                    display_title: display_title
                },
                dataType: "json",
                loaderMessage: 'Deleting ' + display_title + '...',
                showSuccessNotification: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 'success') {
                        show_notification(data.status, data.message);
                        setTimeout(function() {
                            window.location.href = table_id_or_url;
                        }, 1000);
                    } else {
                        show_notification(data.status, data.message);
                    }
                }
            });
        }

        // Enhanced notification function that uses new NotificationManager if available
        function show_notification(type, message, options = {}) {
            if (window.CoreManager && CoreManager.has('notifications')) {
                const notificationManager = CoreManager.get('notifications');
                notificationManager.show(type, message, options);
            } else {
                // Fallback to toastr
                if (type == 'success') {
                    toastr.success(message);
                } else if (type == 'error') {
                    toastr.error(message);
                } else if (type == 'warning') {
                    toastr.warning(message);
                } else if (type == 'information') {
                    toastr.info(message);
                }
            }
        }
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy', // Adjust the format as per your requirement
                autoclose: true
            });

            // Fix menu collapse functionality (jQuery-only implementation)
            $('[data-toggle="collapse"]').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var target = $this.attr('data-target');
                var $target = $(target);
                
                // Toggle the target element
                if ($target.hasClass('show')) {
                    // Hide the collapse
                    $target.removeClass('show').slideUp(300);
                    $this.addClass('collapsed').attr('aria-expanded', 'false');
                } else {
                    // Show the collapse
                    $target.addClass('show').slideDown(300);
                    $this.removeClass('collapsed').attr('aria-expanded', 'true');
                }
                
                // Close other open dropdowns (accordion behavior)
                $('[data-toggle="collapse"]').not($this).each(function() {
                    var otherTarget = $(this).attr('data-target');
                    var $otherTarget = $(otherTarget);
                    if ($otherTarget.hasClass('show')) {
                        $otherTarget.removeClass('show').slideUp(300);
                        $(this).addClass('collapsed').attr('aria-expanded', 'false');
                    }
                });
            });

            // =======================================================
            // CENTRALIZED MODAL UTILITY FUNCTIONS (jQuery-only)
            // =======================================================
            
            // Enhanced Modal Functions using ModalManager
            window.showModal = function(modalId, options = {}) {
                if (window.CoreManager && CoreManager.has('modals')) {
                    const modalManager = CoreManager.get('modals');
                    modalManager.show(modalId, options);
                } else {
                    // Fallback to legacy implementation
                    const defaults = {
                        backdrop: 'static',
                        keyboard: false,
                        closeOnBackdrop: false,
                        closeOnEscape: false
                    };
                    options = $.extend(defaults, options);
                    
                    const $modal = $('#' + modalId);
                    $modal.css('display', 'block').addClass('show');
                    $('body').addClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').append('<div class="modal-backdrop fade show" data-modal-id="' + modalId + '"></div>');
                    
                    $modal.data('modal-options', options);
                }
            };

            window.hideModal = function(modalId) {
                if (window.CoreManager && CoreManager.has('modals')) {
                    const modalManager = CoreManager.get('modals');
                    modalManager.hide(modalId);
                } else {
                    // Fallback to legacy implementation
                    try {
                        const $modal = $('#' + modalId);
                        if ($modal.length === 0) return;
                        
                        $modal.css('display', 'none').removeClass('show').removeData('modal-options');
                        $('.modal-backdrop[data-modal-id="' + modalId + '"]').remove();
                        
                        const visibleModals = $('.modal.show, .modal[style*="display: block"]').not($modal);
                        if (visibleModals.length === 0) {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                        }
                    } catch (error) {
                        console.error('Error hiding modal:', modalId, error);
                    }
                }
            };

            // WhatsApp Modal Functions 
            window.showSendWhatsAppModal = function(quotationId) {
                var modalId = quotationId ? 'sendWhatsAppModal' + quotationId : 'sendWhatsAppModal';
                showModal(modalId);
            };

            window.showResendWhatsAppModal = function(quotationId) {
                var modalId = quotationId ? 'resendWhatsAppModal' + quotationId : 'resendWhatsAppModal';
                showModal(modalId);
            };

            window.hideWhatsAppModal = function(modalId) {
                hideModal(modalId);
            };

            // Delete Modal Functions
            window.showDeleteQuotationModal = function(quotationId) {
                var modalId = quotationId ? 'deleteQuotationModal' + quotationId : 'deleteQuotationModal';
                showModal(modalId);
            };

            window.hideDeleteModal = function(modalId) {
                hideModal(modalId);
            };

            // Logout Modal Functions
            window.showLogoutModal = function() {
                showModal('logoutModal');
            };

            window.hideLogoutModal = function() {
                hideModal('logoutModal');
            };

            // Global Delete Confirmation Modal Functions
            window.showDeleteConfirmModal = function() {
                showModal('delete_confirm');
            };

            window.hideDeleteConfirmModal = function() {
                hideModal('delete_confirm');
            };

            // Enhanced Confirmation Modal System using ModalManager
            window.showConfirmationModal = function(options = {}) {
                if (window.CoreManager && CoreManager.has('modals')) {
                    const modalManager = CoreManager.get('modals');
                    return modalManager.confirm(options);
                } else {
                    // Fallback to legacy implementation
                    const defaults = {
                        title: 'Confirm Action',
                        message: 'Are you sure you want to proceed?',
                        confirmText: 'Yes, Proceed',
                        cancelText: 'Cancel',
                        confirmClass: 'btn-danger',
                        onConfirm: null,
                        onCancel: null
                    };
                    options = $.extend(defaults, options);
                    
                    $('#confirmationModal .modal-title').text(options.title);
                    $('#confirmationModal .modal-body p').text(options.message);
                    $('#confirmationModal .btn-confirm')
                        .removeClass('btn-danger btn-warning btn-primary btn-success')
                        .addClass(options.confirmClass)
                        .text(options.confirmText);
                    $('#confirmationModal .btn-cancel').text(options.cancelText);
                    
                    window._confirmationCallbacks = {
                        onConfirm: options.onConfirm,
                        onCancel: options.onCancel
                    };
                    
                    $('#confirmationModal').css('display', 'block').addClass('show');
                    $('body').addClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').append('<div class="modal-backdrop fade show"></div>');
                }
            };

            // Simple confirmation modal close function
            window.closeConfirmationModal = function() {
                try {
                    console.log('Closing confirmation modal');
                    $('#confirmationModal').css('display', 'none').removeClass('show');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    console.log('Confirmation modal closed successfully');
                } catch (error) {
                    console.error('Error closing confirmation modal:', error);
                }
            };

            // Handle confirmation modal buttons
            $(document).on('click', '#confirmationModal .btn-confirm', function() {
                closeConfirmationModal();
                if (window._confirmationCallbacks && typeof window._confirmationCallbacks.onConfirm === 'function') {
                    window._confirmationCallbacks.onConfirm();
                }
            });

            $(document).on('click', '#confirmationModal .btn-cancel', function() {
                closeConfirmationModal();
                if (window._confirmationCallbacks && typeof window._confirmationCallbacks.onCancel === 'function') {
                    window._confirmationCallbacks.onCancel();
                }
            });

            // Handle data-dismiss="modal" for all modals
            $(document).on('click', '[data-dismiss="modal"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const $modal = $(this).closest('.modal');
                if ($modal.length > 0) {
                    const modalId = $modal.attr('id');
                    console.log('Closing modal via data-dismiss:', modalId);
                    hideModal(modalId);
                } else {
                    // Fallback: try to close the currently visible modal
                    $('.modal.show').each(function() {
                        hideModal(this.id);
                    });
                }
            });

            // Also handle close buttons with the .close class (except confirmation modal)
            $(document).on('click', '.modal .close', function() {
                const $modal = $(this).closest('.modal');
                if ($modal.length > 0) {
                    const modalId = $modal.attr('id');
                    // Special handling for confirmation modal
                    if (modalId === 'confirmationModal') {
                        closeConfirmationModal();
                        if (window._confirmationCallbacks && typeof window._confirmationCallbacks.onCancel === 'function') {
                            window._confirmationCallbacks.onCancel();
                        }
                    } else {
                        console.log('Closing modal via close button:', modalId);
                        hideModal(modalId);
                    }
                }
            });

            // =======================================================
            // LOADING SPINNER UTILITIES 
            // =======================================================
            
            // Enhanced Loading Functions using NotificationManager
            window.showLoading = function(message = 'Loading...', options = {}) {
                if (window.CoreManager && CoreManager.has('notifications')) {
                    const notificationManager = CoreManager.get('notifications');
                    return notificationManager.loading(message, options);
                } else {
                    // Fallback to legacy implementation
                    $('#cover-spin .sr-only').text(message);
                    $('#cover-spin').show();
                }
            };

            window.hideLoading = function(loadingId = null) {
                if (window.CoreManager && CoreManager.has('notifications') && loadingId) {
                    const notificationManager = CoreManager.get('notifications');
                    notificationManager.hideLoading(loadingId);
                } else {
                    // Fallback to legacy implementation
                    $('#cover-spin').hide();
                }
            };

            // Enhanced AJAX operations with loading states
            window.performAjaxOperation = function(options) {
                const defaults = {
                    showLoader: true,
                    loaderMessage: 'Processing...',
                    showSuccessNotification: true,
                    showErrorNotification: true
                };
                options = $.extend(defaults, options);
                
                if (options.showLoader) showLoading(options.loaderMessage);
                
                return $.ajax(options)
                    .done(function(response) {
                        if (options.showSuccessNotification && response.message) {
                            show_notification('success', response.message);
                        }
                    })
                    .fail(function(xhr) {
                        if (options.showErrorNotification) {
                            const errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                            show_notification('error', errorMessage);
                        }
                    })
                    .always(function() {
                        if (options.showLoader) hideLoading();
                    });
            };

            // Disable Bootstrap's default modal behavior to prevent conflicts
            if (typeof $.fn.modal !== 'undefined') {
                // Remove Bootstrap's modal data-api event handlers
                $(document).off('click.bs.modal.data-api', '[data-toggle="modal"]');
                $(document).off('click.bs.modal.data-api', '[data-dismiss="modal"]');
                console.log('Disabled Bootstrap modal data-api to prevent conflicts');
            }

            // Global Modal Event Handlers with proper controls
            $(document).on('click', '.modal-backdrop', function() {
                const modalId = $(this).attr('data-modal-id');
                const $modal = $('#' + modalId);
                const options = $modal.data('modal-options') || {};
                
                if (options.closeOnBackdrop !== false) {
                    hideModal(modalId);
                }
            });

            // Close modals on Escape key only if allowed
            $(document).keydown(function(e) {
                if (e.keyCode === 27) { // ESC key
                    $('.modal.show').each(function() {
                        const options = $(this).data('modal-options') || {};
                        if (options.closeOnEscape !== false) {
                            hideModal(this.id);
                        }
                    });
                }
            });

            // =======================================================
            // GLOBAL AJAX ERROR HANDLING & SETUP
            // =======================================================
            
            // Global AJAX setup for better error handling
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    // Add CSRF token to all requests
                    if (!settings.crossDomain) {
                        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.status, error);
                    
                    // Handle common HTTP errors
                    switch(xhr.status) {
                        case 401:
                            show_notification('error', 'Unauthorized access. Please log in again.');
                            setTimeout(() => window.location.href = '/login', 2000);
                            break;
                        case 403:
                            show_notification('error', 'You do not have permission to perform this action.');
                            break;
                        case 404:
                            show_notification('error', 'The requested resource was not found.');
                            break;
                        case 419:
                            show_notification('error', 'Session expired. Please refresh the page.');
                            break;
                        case 422:
                            // Validation errors - handle in specific contexts
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                let errorMessages = Object.values(xhr.responseJSON.errors).flat();
                                show_notification('error', errorMessages.join('<br>'));
                            } else {
                                show_notification('error', 'Validation failed. Please check your input.');
                            }
                            break;
                        case 500:
                            show_notification('error', 'Server error occurred. Please try again or contact support.');
                            break;
                        default:
                            if (xhr.status >= 400) {
                                const message = xhr.responseJSON?.message || 'An unexpected error occurred.';
                                show_notification('error', message);
                            }
                    }
                    
                    // Always hide loading spinner on error
                    hideLoading();
                }
            });

            // Enhanced notification function with auto-dismiss and better styling
            window.showEnhancedNotification = function(type, message, options = {}) {
                const defaults = {
                    autoDismiss: true,
                    timeout: type === 'error' ? 8000 : 5000,
                    position: 'top-right',
                    closeButton: true
                };
                options = $.extend(defaults, options);
                
                toastr.options = {
                    "closeButton": options.closeButton,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": `toast-${options.position}`,
                    "preventDuplicates": true,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": options.autoDismiss ? options.timeout : "0",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                
                toastr[type](message);
            };
        });
    </script>
</body>

</html>
