@extends('layouts.app')

@section('title', 'Add Customers')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Customer Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Add New Customer</h6>
                <a href="{{ route('customers.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body py-3">
                    <!-- Section 1: Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-user me-2"></i>Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Name</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    name="name" placeholder="Enter full name" value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Email</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" placeholder="Enter email address" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Mobile Number</label>
                                <input type="text" class="form-control form-control-sm @error('mobile_number') is-invalid @enderror"
                                    name="mobile_number" placeholder="Enter mobile number" value="{{ old('mobile_number') }}">
                                @error('mobile_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Customer Configuration -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-cogs me-2"></i>Customer Configuration</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Customer Type</label>
                                <select class="form-select form-select-sm @error('type') is-invalid @enderror" name="type" id="customerType">
                                    <option value="Retail" @if (old('type') == 'Retail') selected @endif>Retail</option>
                                    <option value="Corporate" @if (old('type') == 'Corporate') selected @endif>Corporate</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Status</label>
                                <select class="form-select form-select-sm @error('status') is-invalid @enderror" name="status">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date Of Birth</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('date_of_birth') is-invalid @enderror"
                                    name="date_of_birth" placeholder="DD/MM/YYYY" 
                                    value="{{ old('date_of_birth') ? formatDateForUi(old('date_of_birth')) : '' }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Document Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-id-card me-2"></i>Document Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">PAN Card Number</label>
                                <input type="text" class="form-control form-control-sm @error('pan_card_number') is-invalid @enderror"
                                    name="pan_card_number" placeholder="Enter PAN number" value="{{ old('pan_card_number') }}">
                                @error('pan_card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">PAN Card Document</label>
                                <input type="file" class="form-control form-control-sm @error('pan_card_path') is-invalid @enderror"
                                    name="pan_card_path" accept=".pdf,.jpg,.jpeg,.png">
                                @error('pan_card_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Aadhar Card Number</label>
                                <input type="text" class="form-control form-control-sm @error('aadhar_card_number') is-invalid @enderror"
                                    name="aadhar_card_number" placeholder="Enter Aadhar number" value="{{ old('aadhar_card_number') }}">
                                @error('aadhar_card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Aadhar Card Document</label>
                                <input type="file" class="form-control form-control-sm @error('aadhar_card_path') is-invalid @enderror"
                                    name="aadhar_card_path" accept=".pdf,.jpg,.jpeg,.png">
                                @error('aadhar_card_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- GST Information (Corporate Only) -->
                            <div class="col-md-4" id="gstNumberSection" style="display: none;">
                                <label class="form-label fw-semibold">GST Number</label>
                                <input type="text" class="form-control form-control-sm @error('gst_number') is-invalid @enderror"
                                    name="gst_number" placeholder="Enter GST number" value="{{ old('gst_number') }}">
                                @error('gst_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4" id="gstDocumentSection" style="display: none;">
                                <label class="form-label fw-semibold">GST Document</label>
                                <input type="file" class="form-control form-control-sm @error('gst_path') is-invalid @enderror"
                                    name="gst_path" accept=".pdf,.jpg,.jpeg,.png">
                                @error('gst_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Important Dates -->
                    <div class="mb-3">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-calendar-alt me-2"></i>Important Dates</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Engagement Anniversary</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('engagement_anniversary_date') is-invalid @enderror"
                                    name="engagement_anniversary_date" placeholder="DD/MM/YYYY"
                                    value="{{ old('engagement_anniversary_date') ? formatDateForUi(old('engagement_anniversary_date')) : '' }}">
                                @error('engagement_anniversary_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Wedding Anniversary</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('wedding_anniversary_date') is-invalid @enderror"
                                    name="wedding_anniversary_date" placeholder="DD/MM/YYYY"
                                    value="{{ old('wedding_anniversary_date') ? formatDateForUi(old('wedding_anniversary_date')) : '' }}">
                                @error('wedding_anniversary_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <!-- Empty column for consistent 3-column layout -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('customers.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Save Customer
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        // Get the customer type select element
        const customerTypeSelect = document.getElementById('customerType');

        // Get the GST sections
        const gstNumberSection = document.getElementById('gstNumberSection');
        const gstDocumentSection = document.getElementById('gstDocumentSection');

        // Function to toggle GST section visibility
        const toggleGSTSection = () => {
            const customerType = customerTypeSelect.value;
            if (customerType === 'Corporate') {
                gstNumberSection.style.display = 'block';
                gstDocumentSection.style.display = 'block';
            } else {
                gstNumberSection.style.display = 'none';
                gstDocumentSection.style.display = 'none';
            }
        };

        // Initialize GST section visibility
        toggleGSTSection();

        // Event listener for customer type change
        customerTypeSelect.addEventListener('change', toggleGSTSection);
        const inputElements = document.querySelectorAll('input[type="text"]');

        function convertToUppercase(event) {
            const input = event.target;
            input.value = input.value.toUpperCase();
        }
        inputElements.forEach(input => {
            input.addEventListener('input', convertToUppercase);
        });
    </script>
@endsection
@section('stylesheets')
@endsection
