@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Customer Edit Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Edit Customer</h6>
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
            <form method="POST" action="{{ route('customers.update', ['customer' => $customer->id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body py-3">
                    <!-- Section 1: Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-user me-2"></i>Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Name</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    name="name" placeholder="Enter full name" value="{{ old('name', $customer->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Email</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" placeholder="Enter email address" value="{{ old('email', $customer->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Mobile Number</label>
                                <input type="text" class="form-control form-control-sm @error('mobile_number') is-invalid @enderror"
                                    name="mobile_number" placeholder="Enter mobile number" value="{{ old('mobile_number', $customer->mobile_number) }}">
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
                                    <option value="Retail" @if (old('type', $customer->type) == 'Retail') selected @endif>Retail</option>
                                    <option value="Corporate" @if (old('type', $customer->type) == 'Corporate') selected @endif>Corporate</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Status</label>
                                <select class="form-select form-select-sm @error('status') is-invalid @enderror" name="status">
                                    <option value="1" @if (old('status', $customer->status) == 1) selected @endif>Active</option>
                                    <option value="0" @if (old('status', $customer->status) == 0) selected @endif>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date Of Birth</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('date_of_birth') is-invalid @enderror"
                                    name="date_of_birth" placeholder="DD/MM/YYYY" 
                                    value="{{ old('date_of_birth') ? formatDateForUi(old('date_of_birth')) : $customer->date_of_birth_formatted }}">
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
                                    name="pan_card_number" placeholder="Enter PAN number" value="{{ old('pan_card_number', $customer->pan_card_number) }}">
                                @error('pan_card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">PAN Card Document</label>
                                <div class="input-group input-group-sm">
                                    <input type="file" class="form-control form-control-sm @error('pan_card_path') is-invalid @enderror"
                                        name="pan_card_path" accept=".pdf,.jpg,.jpeg,.png">
                                    @if ($customer->pan_card_path)
                                        <a href="{{ asset('storage/' . $customer->pan_card_path) }}" 
                                           class="btn btn-outline-primary btn-sm" target="_blank" title="Download current document">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                                @error('pan_card_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Aadhar Card Number</label>
                                <input type="text" class="form-control form-control-sm @error('aadhar_card_number') is-invalid @enderror"
                                    name="aadhar_card_number" placeholder="Enter Aadhar number" value="{{ old('aadhar_card_number', $customer->aadhar_card_number) }}">
                                @error('aadhar_card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Aadhar Card Document</label>
                                <div class="input-group input-group-sm">
                                    <input type="file" class="form-control form-control-sm @error('aadhar_card_path') is-invalid @enderror"
                                        name="aadhar_card_path" accept=".pdf,.jpg,.jpeg,.png">
                                    @if ($customer->aadhar_card_path)
                                        <a href="{{ asset('storage/' . $customer->aadhar_card_path) }}" 
                                           class="btn btn-outline-primary btn-sm" target="_blank" title="Download current document">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                                @error('aadhar_card_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- GST Information (Corporate Only) -->
                            <div class="col-md-4" id="gstNumberSection" style="display: none;">
                                <label class="form-label fw-semibold">GST Number</label>
                                <input type="text" class="form-control form-control-sm @error('gst_number') is-invalid @enderror"
                                    name="gst_number" placeholder="Enter GST number" value="{{ old('gst_number', $customer->gst_number) }}">
                                @error('gst_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4" id="gstDocumentSection" style="display: none;">
                                <label class="form-label fw-semibold">GST Document</label>
                                <div class="input-group input-group-sm">
                                    <input type="file" class="form-control form-control-sm @error('gst_path') is-invalid @enderror"
                                        name="gst_path" accept=".pdf,.jpg,.jpeg,.png">
                                    @if ($customer->gst_path)
                                        <a href="{{ asset('storage/' . $customer->gst_path) }}" 
                                           class="btn btn-outline-primary btn-sm" target="_blank" title="Download current document">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
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
                                    value="{{ old('engagement_anniversary_date') ? formatDateForUi(old('engagement_anniversary_date')) : $customer->engagement_anniversary_date_formatted }}">
                                @error('engagement_anniversary_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Wedding Anniversary</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('wedding_anniversary_date') is-invalid @enderror"
                                    name="wedding_anniversary_date" placeholder="DD/MM/YYYY"
                                    value="{{ old('wedding_anniversary_date') ? formatDateForUi(old('wedding_anniversary_date')) : $customer->wedding_anniversary_date_formatted }}">
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
                            <i class="fas fa-save me-1"></i>Update Customer
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @if (auth()->user()->hasPermissionTo('customer-insurance-list'))
            <div class="card-body table-responsive p-0">
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th>Added Date</th>
                            <th>Issue Date</th>
                            <th>Expired Date</th>
                            <th>Policy Number</th>
                            <th>Registration Number</th>
                            <th>Premium Type</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($customer_insurances))
                            @foreach ($customer_insurances as $customer_insurance)
                                <tr>
                                    <td>{{ formatDateForUi($customer_insurance->created_at) }}</td>
                                    <td>{{ formatDateForUi($customer_insurance->issue_date) }}</td>
                                    <td>{{ formatDateForUi($customer_insurance->expired_date) }}
                                    </td>
                                    <td>{{ $customer_insurance->policy_no }}</td>
                                    <td>{{ $customer_insurance->registration_no }}</td>
                                    <td>{{ $customer_insurance->premiumType->name }}</td>
                                    <td class="text-center">
                                        @if (auth()->user()->hasPermissionTo('customer-insurance-edit'))
                                            <a href="{{ route('customer_insurances.edit', ['customer_insurance' => $customer_insurance->id]) }}"
                                                class="btn btn-primary m-2">
                                                <i class="fa fa-pen"></i>
                                            </a> &nbsp;
                                        @endif
                                        @if (auth()->user()->hasPermissionTo('customer-insurance-delete'))
                                            @if ($customer_insurance->status == 0)
                                                <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 1]) }}"
                                                    class="btn btn-success m-2">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @elseif ($customer_insurance->status == 1)
                                                <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 0]) }}"
                                                    class="btn btn-danger m-2">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if ($customer_insurance->policy_document_path)
                                            <a href="{{ asset('storage/' . $customer_insurance->policy_document_path) }}"
                                                class="btn btn-info m-2" target="__blank"><i
                                                    class="fa fa-download"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="center">No Record found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
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
