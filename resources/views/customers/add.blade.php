@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div>
                        <h1 class="h4 mb-0 text-primary font-weight-bold">
                            <i class="fas fa-user-plus me-2"></i>Add New Customer
                        </h1>
                        <small class="text-muted">Create a new customer record</small>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <x-buttons.action-button 
                            variant="outline-secondary" 
                            size="sm" 
                            icon="fas fa-arrow-left"
                            href="{{ route('customers.index') }}"
                            title="Back to Customers List">
                            Back to Customers
                        </x-buttons.action-button>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    {{-- Enhanced Error Display --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                <h6 class="mb-0">Please fix the following errors:</h6>
                            </div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Enhanced Form Layout -->
                    <div class="row g-3">
                        <!-- Basic Information Section -->
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-header bg-transparent border-0 py-2">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-user me-2"></i>Basic Information
                                    </h6>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="row g-3">
                                        {{-- Name --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="name" class="form-label text-sm fw-bold">
                                                <span class="text-danger">*</span> Full Name
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-user text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       id="name" 
                                                       name="name"
                                                       placeholder="Enter full name" 
                                                       value="{{ old('name') }}"
                                                       required
                                                       autocomplete="name">
                                            </div>
                                            @error('name')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Email --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="email" class="form-label text-sm fw-bold">
                                                <span class="text-danger">*</span> Email Address
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-envelope text-muted"></i>
                                                </span>
                                                <input type="email" 
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       id="email" 
                                                       name="email"
                                                       placeholder="Enter email address" 
                                                       value="{{ old('email') }}"
                                                       required
                                                       autocomplete="email">
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Mobile Number --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="mobile_number" class="form-label text-sm fw-bold">
                                                <span class="text-danger">*</span> Mobile Number
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-phone text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control @error('mobile_number') is-invalid @enderror"
                                                       id="mobile_number" 
                                                       name="mobile_number"
                                                       placeholder="Enter 10-digit mobile number" 
                                                       value="{{ old('mobile_number') }}"
                                                       pattern="[0-9]{10}"
                                                       maxlength="10"
                                                       required
                                                       autocomplete="tel">
                                            </div>
                                            @error('mobile_number')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>10-digit Indian mobile number
                                            </small>
                                        </div>

                                        {{-- Customer Type --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="customerType" class="form-label text-sm fw-bold">
                                                <span class="text-danger">*</span> Customer Type
                                            </label>
                                            <select class="form-select form-select-sm select2-enable @error('type') is-invalid @enderror"
                                                    name="type" 
                                                    id="customerType"
                                                    data-placeholder="Select customer type"
                                                    required>
                                                <option value="">Select customer type</option>
                                                <option value="Retail" {{ old('type') == 'Retail' ? 'selected' : '' }}>
                                                    Individual/Retail
                                                </option>
                                                <option value="Corporate" {{ old('type') == 'Corporate' ? 'selected' : '' }}>
                                                    Corporate/Business
                                                </option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Document Information Section -->
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-header bg-transparent border-0 py-2">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-file-alt me-2"></i>Document Information
                                    </h6>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="row g-3">
                                        {{-- Pan Card Number --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="pan_card_number" class="form-label text-sm fw-bold">PAN Card Number</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control @error('pan_card_number') is-invalid @enderror"
                                                       id="pan_card_number" 
                                                       name="pan_card_number"
                                                       placeholder="Enter PAN number (e.g., ABCDE1234F)" 
                                                       value="{{ old('pan_card_number') }}"
                                                       maxlength="10"
                                                       style="text-transform: uppercase;"
                                                       pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                                                       autocomplete="off">
                                            </div>
                                            @error('pan_card_number')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Format: ABCDE1234F (5 letters, 4 digits, 1 letter)
                                            </small>
                                        </div>

                                        {{-- Pan Card Document --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="pan_card_path" class="form-label text-sm fw-bold">PAN Card Document</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-upload text-muted"></i>
                                                </span>
                                                <input type="file" 
                                                       class="form-control @error('pan_card_path') is-invalid @enderror"
                                                       id="pan_card_path" 
                                                       name="pan_card_path" 
                                                       accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                            @error('pan_card_path')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>PDF, JPG, PNG files only
                                            </small>
                                        </div>

                                        {{-- Aadhar Card Number --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="aadhar_card_number" class="form-label text-sm fw-bold">Aadhaar Card Number</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-address-card text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control @error('aadhar_card_number') is-invalid @enderror"
                                                       id="aadhar_card_number" 
                                                       name="aadhar_card_number"
                                                       placeholder="Enter 12-digit Aadhaar number" 
                                                       value="{{ old('aadhar_card_number') }}"
                                                       pattern="[0-9]{12}"
                                                       maxlength="12"
                                                       autocomplete="off">
                                            </div>
                                            @error('aadhar_card_number')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>12-digit Aadhaar number
                                            </small>
                                        </div>

                                        {{-- Aadhar Card Document --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="aadhar_card_path" class="form-label text-sm fw-bold">Aadhaar Card Document</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-upload text-muted"></i>
                                                </span>
                                                <input type="file" 
                                                       class="form-control @error('aadhar_card_path') is-invalid @enderror"
                                                       id="aadhar_card_path" 
                                                       name="aadhar_card_path" 
                                                       accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                            @error('aadhar_card_path')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>PDF, JPG, PNG files only
                                            </small>
                                        </div>

                                        {{-- GST Number --}}
                                        <div class="col-lg-4 col-md-6" id="gstNumberSection">
                                            <label for="gst_number" class="form-label text-sm fw-bold">GST Number</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-receipt text-muted"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control @error('gst_number') is-invalid @enderror"
                                                       id="gst_number" 
                                                       name="gst_number"
                                                       placeholder="Enter GST number" 
                                                       value="{{ old('gst_number') }}"
                                                       maxlength="15"
                                                       style="text-transform: uppercase;"
                                                       autocomplete="off">
                                            </div>
                                            @error('gst_number')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Only for Corporate customers
                                            </small>
                                        </div>

                                        {{-- GST Document --}}
                                        <div class="col-lg-4 col-md-6" id="gstDocumentSection">
                                            <label for="gst_path" class="form-label text-sm fw-bold">GST Certificate</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-upload text-muted"></i>
                                                </span>
                                                <input type="file" 
                                                       class="form-control @error('gst_path') is-invalid @enderror"
                                                       id="gst_path" 
                                                       name="gst_path" 
                                                       accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                            @error('gst_path')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>GST Certificate (PDF, JPG, PNG)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Dates Section -->
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-header bg-transparent border-0 py-2">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-calendar-alt me-2"></i>Important Dates
                                    </h6>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="row g-3">
                                        {{-- Date of Birth --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="date_of_birth" class="form-label text-sm fw-bold">Date of Birth</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-birthday-cake text-muted"></i>
                                                </span>
                                                <input type="date" 
                                                       class="form-control @error('date_of_birth') is-invalid @enderror"
                                                       id="date_of_birth" 
                                                       name="date_of_birth"
                                                       value="{{ old('date_of_birth') }}">
                                            </div>
                                            @error('date_of_birth')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Engagement Anniversary --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="engagement_anniversary_date" class="form-label text-sm fw-bold">Engagement Anniversary</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-ring text-muted"></i>
                                                </span>
                                                <input type="date" 
                                                       class="form-control @error('engagement_anniversary_date') is-invalid @enderror"
                                                       id="engagement_anniversary_date" 
                                                       name="engagement_anniversary_date"
                                                       value="{{ old('engagement_anniversary_date') }}">
                                            </div>
                                            @error('engagement_anniversary_date')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Wedding Anniversary --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label for="wedding_anniversary_date" class="form-label text-sm fw-bold">Wedding Anniversary</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">
                                                    <i class="fas fa-heart text-muted"></i>
                                                </span>
                                                <input type="date" 
                                                       class="form-control @error('wedding_anniversary_date') is-invalid @enderror"
                                                       id="wedding_anniversary_date" 
                                                       name="wedding_anniversary_date"
                                                       value="{{ old('wedding_anniversary_date') }}">
                                            </div>
                                            @error('wedding_anniversary_date')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                    </div>
                </div>

                <div class="card-footer bg-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small><i class="fas fa-info-circle me-1"></i>Fields marked with <span class="text-danger">*</span> are required</small>
                        </div>
                        <div>
                            <x-buttons.action-button 
                                variant="outline-secondary" 
                                size="sm" 
                                icon="fas fa-times"
                                href="{{ route('customers.index') }}"
                                title="Cancel and go back">
                                Cancel
                            </x-buttons.action-button>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-save me-1"></i>Create Customer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('admin/js/modules/customers-common.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize customer form with validation and formatting
            initializeCustomerForm();
            
            // Initialize Select2 for enhanced dropdowns
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2-enable').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: function() {
                        return $(this).data('placeholder');
                    },
                    minimumResultsForSearch: -1
                });
            }
            
            // Enhanced form validation
            const form = $('form');
            const submitBtn = form.find('button[type="submit"]');
            
            form.on('submit', function(e) {
                // Basic client-side validation
                let isValid = true;
                const requiredFields = form.find('input[required], select[required]');
                
                requiredFields.each(function() {
                    const field = $(this);
                    const value = field.val()?.trim();
                    
                    if (!value) {
                        isValid = false;
                        field.addClass('is-invalid');
                        if (!field.siblings('.invalid-feedback').length) {
                            field.after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>This field is required</div>');
                        }
                    } else {
                        field.removeClass('is-invalid');
                        field.siblings('.invalid-feedback').remove();
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // Show notification if available
                    if (window.CoreManager && CoreManager.has('notifications')) {
                        const notificationManager = CoreManager.get('notifications');
                        notificationManager.show('error', 'Please fill in all required fields');
                    }
                    return false;
                }
                
                // Show loading state
                submitBtn.prop('disabled', true);
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Creating Customer...');
                
                // Re-enable button after 10 seconds as fallback
                setTimeout(function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }, 10000);
            });
            
            // Real-time validation feedback
            $('input[required], select[required]').on('blur change', function() {
                const field = $(this);
                const value = field.val()?.trim();
                
                if (value) {
                    field.removeClass('is-invalid').addClass('is-valid');
                    field.siblings('.invalid-feedback').remove();
                } else {
                    field.removeClass('is-valid');
                }
            });
            
            // Mobile number formatting and validation
            $('#mobile_number').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                $(this).val(value);
                
                // Validation feedback
                if (value.length === 10) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else if (value.length > 0) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            });
            
            // Email validation
            $('#email').on('blur', function() {
                const email = $(this).val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    if (!$(this).siblings('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Please enter a valid email address</div>');
                    }
                } else if (email) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            });
            
            // Name formatting - capitalize first letters
            $('#name').on('blur', function() {
                const name = $(this).val();
                if (name) {
                    const formatted = name.replace(/\b\w/g, l => l.toUpperCase());
                    $(this).val(formatted);
                }
            });
            
            // PAN Card validation and formatting
            $('#pan_card_number').on('input', function() {
                let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                $(this).val(value);
                
                // Validation feedback
                const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                if (value.length === 10 && panRegex.test(value)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else if (value.length > 0) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                } else {
                    $(this).removeClass('is-valid is-invalid');
                }
            });
            
            // Aadhaar Card validation and formatting
            $('#aadhar_card_number').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
                if (value.length > 12) {
                    value = value.substring(0, 12);
                }
                $(this).val(value);
                
                // Validation feedback
                if (value.length === 12) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else if (value.length > 0) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                } else {
                    $(this).removeClass('is-valid is-invalid');
                }
            });
            
            // GST Number validation and formatting
            $('#gst_number').on('input', function() {
                let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (value.length > 15) {
                    value = value.substring(0, 15);
                }
                $(this).val(value);
                
                // Validation feedback
                const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
                if (value.length === 15 && gstRegex.test(value)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else if (value.length > 0) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                } else {
                    $(this).removeClass('is-valid is-invalid');
                }
            });
            
            // Customer Type dependent GST field visibility
            $('#customerType').on('change', function() {
                const customerType = $(this).val();
                const gstSection = $('#gstNumberSection, #gstDocumentSection');
                
                if (customerType === 'Corporate') {
                    gstSection.show();
                    gstSection.find('label').each(function() {
                        const label = $(this);
                        if (label.text().includes('GST') && !label.find('.text-warning').length) {
                            label.html('<span class="text-warning">*</span> ' + label.text());
                            label.addClass('fw-bold');
                        }
                    });
                } else {
                    gstSection.hide();
                    // Clear GST fields when hiding
                    $('#gst_number, #gst_path').val('').removeClass('is-valid is-invalid');
                    gstSection.find('label').each(function() {
                        const label = $(this);
                        if (label.text().includes('GST')) {
                            label.html(label.text().replace('* ', ''));
                        }
                    });
                }
            });
            
            // Initialize GST visibility based on current selection
            $('#customerType').trigger('change');
            
            // File upload validation
            $('input[type="file"]').on('change', function() {
                const file = this.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                
                if (file) {
                    // Check file size
                    if (file.size > maxSize) {
                        $(this).addClass('is-invalid');
                        if (!$(this).siblings('.invalid-feedback').length) {
                            $(this).after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>File size must be less than 5MB</div>');
                        }
                        $(this).val(''); // Clear the file
                        return;
                    }
                    
                    // Check file type
                    if (!allowedTypes.includes(file.type)) {
                        $(this).addClass('is-invalid');
                        if (!$(this).siblings('.invalid-feedback').length) {
                            $(this).after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Only PDF, JPG, and PNG files are allowed</div>');
                        }
                        $(this).val(''); // Clear the file
                        return;
                    }
                    
                    // Valid file
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            });
        });
    </script>
@endsection