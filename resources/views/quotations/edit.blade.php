@extends('layouts.app')

@section('title', 'Edit Insurance Quotation')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Quotation Edit Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold text-primary">Edit Insurance Quotation</h6>
                    <small class="text-muted">{{ $quotation->quotation_number }} | {{ $quotation->customer->name ?? 'N/A' }} | {{ ucfirst($quotation->status) }} | {{ $quotation->quotationCompanies->count() }} Companies</small>
                </div>
                <div class="d-flex gap-2">
                    @if($quotation->quotationCompanies->count() > 0)
                        @can('quotation-download-pdf')
                            <a href="{{ route('quotations.download-pdf', $quotation) }}"
                               class="btn btn-primary btn-sm d-flex align-items-center">
                                <i class="fas fa-download me-2"></i>
                                <span>Download PDF</span>
                            </a>
                        @endcan

                        @can('quotation-send-whatsapp')
                            @if($quotation->status === 'Sent')
                                <button type="button" class="btn btn-warning btn-sm d-flex align-items-center"
                                        onclick="showResendWhatsAppModal()">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    <span>Resend via WhatsApp</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-success btn-sm d-flex align-items-center"
                                        onclick="showSendWhatsAppModal()">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    <span>Send via WhatsApp</span>
                                </button>
                            @endif
                        @endcan
                    @endif

                    <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-info btn-sm d-flex align-items-center">
                        <i class="fas fa-eye me-2"></i>
                        <span>View Details</span>
                    </a>
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                        <i class="fas fa-list me-2"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('quotations.update', $quotation) }}" id="quotationForm">
                    @csrf
                    @method('PUT')

                    <!-- Customer Selection -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-primary mb-4">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="m-0"><i class="fas fa-user"></i> Customer Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="customer_id"><span class="text-danger">*</span> Customer</label>
                                                <select name="customer_id" id="customer_id"
                                                    class="form-control select2 @error('customer_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Customer</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}"
                                                            data-mobile="{{ $customer->mobile_number }}"
                                                            {{ (old('customer_id') ?? $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                                            {{ $customer->name }}
                                                            @if ($customer->mobile_number)
                                                                - {{ $customer->mobile_number }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('customer_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="whatsapp_number">WhatsApp Number</label>
                                                <input type="text" name="whatsapp_number" id="whatsapp_number"
                                                    class="form-control @error('whatsapp_number') is-invalid @enderror"
                                                    placeholder="For sending quotation"
                                                    value="{{ old('whatsapp_number') ?? $quotation->whatsapp_number }}">
                                                @error('whatsapp_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-info mb-4">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="m-0"><i class="fas fa-car"></i> Vehicle Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="vehicle_number">Vehicle Number</label>
                                                <input type="text" name="vehicle_number" id="vehicle_number"
                                                    class="form-control @error('vehicle_number') is-invalid @enderror"
                                                    placeholder="e.g., GJ05AB1234 (Leave blank if new vehicle)"
                                                    style="text-transform: uppercase"
                                                    value="{{ old('vehicle_number') ?? $quotation->vehicle_number }}">
                                                @error('vehicle_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="make_model_variant"><span class="text-danger">*</span>
                                                    Make/Model/Variant</label>
                                                <input type="text" name="make_model_variant" id="make_model_variant"
                                                    class="form-control @error('make_model_variant') is-invalid @enderror"
                                                    placeholder="e.g., Maruti Swift VDI"
                                                    value="{{ old('make_model_variant') ?? $quotation->make_model_variant }}"
                                                    required>
                                                @error('make_model_variant')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rto_location"><span class="text-danger">*</span> RTO
                                                    Location</label>
                                                <input type="text" name="rto_location" id="rto_location"
                                                    class="form-control @error('rto_location') is-invalid @enderror"
                                                    placeholder="e.g., Ahmedabad"
                                                    value="{{ old('rto_location') ?? $quotation->rto_location }}" required>
                                                @error('rto_location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="manufacturing_year"><span class="text-danger">*</span>
                                                    Manufacturing Year</label>
                                                <select name="manufacturing_year" id="manufacturing_year"
                                                    class="form-control @error('manufacturing_year') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Year</option>
                                                    @for ($year = date('Y'); $year >= 1990; $year--)
                                                        <option value="{{ $year }}"
                                                            {{ (old('manufacturing_year') ?? $quotation->manufacturing_year) == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                @error('manufacturing_year')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fuel_type"><span class="text-danger">*</span> Fuel
                                                    Type</label>
                                                <select name="fuel_type" id="fuel_type"
                                                    class="form-control @error('fuel_type') is-invalid @enderror" required>
                                                    <option value="">Select Fuel Type</option>
                                                    <option value="Petrol"
                                                        {{ (old('fuel_type') ?? $quotation->fuel_type) == 'Petrol' ? 'selected' : '' }}>
                                                        Petrol
                                                    </option>
                                                    <option value="Diesel"
                                                        {{ (old('fuel_type') ?? $quotation->fuel_type) == 'Diesel' ? 'selected' : '' }}>
                                                        Diesel
                                                    </option>
                                                    <option value="CNG"
                                                        {{ (old('fuel_type') ?? $quotation->fuel_type) == 'CNG' ? 'selected' : '' }}>
                                                        CNG</option>
                                                    <option value="Electric"
                                                        {{ (old('fuel_type') ?? $quotation->fuel_type) == 'Electric' ? 'selected' : '' }}>
                                                        Electric
                                                    </option>
                                                    <option value="Hybrid"
                                                        {{ (old('fuel_type') ?? $quotation->fuel_type) == 'Hybrid' ? 'selected' : '' }}>
                                                        Hybrid
                                                    </option>
                                                </select>
                                                @error('fuel_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ncb_percentage">NCB Percentage</label>
                                                <div class="input-group">
                                                    <input type="number" name="ncb_percentage" id="ncb_percentage"
                                                        class="form-control @error('ncb_percentage') is-invalid @enderror"
                                                        value="{{ old('ncb_percentage', $quotation->ncb_percentage ?? 0) }}"
                                                        min="0" max="50" step="1" placeholder="0">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="form-text text-muted">No Claim Bonus (0-50%)</small>
                                                @error('ncb_percentage')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="cubic_capacity_kw"><span class="text-danger">*</span> Cubic
                                                    Capacity (CC/KW)</label>
                                                <input type="number" name="cubic_capacity_kw" id="cubic_capacity_kw"
                                                    class="form-control @error('cubic_capacity_kw') is-invalid @enderror"
                                                    placeholder="e.g., 1200"
                                                    value="{{ old('cubic_capacity_kw') ?? $quotation->cubic_capacity_kw }}"
                                                    required>
                                                @error('cubic_capacity_kw')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="seating_capacity"><span class="text-danger">*</span> Seating
                                                    Capacity</label>
                                                <input type="number" name="seating_capacity" id="seating_capacity"
                                                    class="form-control @error('seating_capacity') is-invalid @enderror"
                                                    placeholder="e.g., 5"
                                                    value="{{ old('seating_capacity') ?? $quotation->seating_capacity }}"
                                                    required>
                                                @error('seating_capacity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="policy_type"><span class="text-danger">*</span> Policy Type</label>
                                                <select name="policy_type" id="policy_type"
                                                    class="form-control @error('policy_type') is-invalid @enderror" required>
                                                    <option value="">Select Policy Type</option>
                                                    <option value="Comprehensive"
                                                        {{ (old('policy_type') ?? $quotation->policy_type) == 'Comprehensive' ? 'selected' : '' }}>
                                                        Comprehensive
                                                    </option>
                                                    <option value="Own Damage"
                                                        {{ (old('policy_type') ?? $quotation->policy_type) == 'Own Damage' ? 'selected' : '' }}>
                                                        Own Damage
                                                    </option>
                                                    <option value="Third Party"
                                                        {{ (old('policy_type') ?? $quotation->policy_type) == 'Third Party' ? 'selected' : '' }}>
                                                        Third Party
                                                    </option>
                                                </select>
                                                @error('policy_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="policy_tenure_years"><span class="text-danger">*</span> Policy Tenure</label>
                                                <select name="policy_tenure_years" id="policy_tenure_years"
                                                    class="form-control @error('policy_tenure_years') is-invalid @enderror" required>
                                                    <option value="">Select Tenure</option>
                                                    <option value="1"
                                                        {{ (old('policy_tenure_years') ?? $quotation->policy_tenure_years) == '1' ? 'selected' : '' }}>
                                                        1 Year
                                                    </option>
                                                    <option value="2"
                                                        {{ (old('policy_tenure_years') ?? $quotation->policy_tenure_years) == '2' ? 'selected' : '' }}>
                                                        2 Years
                                                    </option>
                                                    <option value="3"
                                                        {{ (old('policy_tenure_years') ?? $quotation->policy_tenure_years) == '3' ? 'selected' : '' }}>
                                                        3 Years
                                                    </option>
                                                </select>
                                                @error('policy_tenure_years')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Insurance Company Quotes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-primary mb-4">
                                <div
                                    class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0"><i class="fas fa-building"></i> Insurance Company Quotes</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light mr-2">{{ $quotation->quotationCompanies->count() }}
                                            Existing</span>
                                        <button type="button" class="btn btn-sm btn-light" id="addQuoteBtn">
                                            <i class="fas fa-plus"></i> Add Quote
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <small><i class="fas fa-info-circle"></i> You can edit existing company quotes or
                                            add new ones. All quotes use the same addon covers selection from create mode.</small>
                                    </div>

                                    <div id="quotesContainer">
                                        @php
                                            $quoteIndex = 0;
                                        @endphp

                                        <!-- Existing company quotes -->
                                        @foreach ($quotation->quotationCompanies as $company)
                                            @include('quotations.partials.edit-quote-form', [
                                                'company' => $company,
                                                'quoteIndex' => $quoteIndex,
                                                'insuranceCompanies' => $insuranceCompanies,
                                                'addonCovers' => $addonCovers ?? collect()
                                            ])
                                            @php $quoteIndex++; @endphp
                                        @endforeach

                                        <!-- Dynamic quote entries will be added here by JavaScript -->
                                    </div>

                                    <div class="text-center" id="noQuotesMessage"
                                        {{ $quotation->quotationCompanies->count() > 0 ? 'style=display:none;' : '' }}>
                                        <p class="text-muted">No quotes available. Click "Add Quote" to start.</p>
                                    </div>
                                    
                                    <!-- Add-on Coverage Breakdown -->
                                    @if($quotation->quotationCompanies->where('total_addon_premium', '>', 0)->count() > 0)
                                        <div class="mt-4" id="addonBreakdownSection">
                                            <h6 class="font-weight-bold text-success mb-3">
                                                <i class="fas fa-plus-circle"></i> Add-on Coverage Breakdown
                                                <small class="text-muted ml-2">(Current quotes with add-on covers)</small>
                                            </h6>
                                            <div class="row">
                                                @foreach($quotation->quotationCompanies->where('total_addon_premium', '>', 0) as $company)
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card border-left-success">
                                                            <div class="card-header bg-success text-white py-2">
                                                                <h6 class="m-0 font-weight-bold">
                                                                    {{ $company->insuranceCompany->name }}
                                                                    @if($company->quote_number)
                                                                        <small class="ml-2">({{ $company->quote_number }})</small>
                                                                    @endif
                                                                    <span class="float-right">Total: ₹{{ number_format($company->total_addon_premium) }}</span>
                                                                </h6>
                                                            </div>
                                                            <div class="card-body py-2">
                                                                @if($company->addon_covers_breakdown)
                                                                    <div class="row">
                                                                        @php
                                                                            $addonsWithPrice = collect($company->addon_covers_breakdown)->filter(function($data) {
                                                                                return (is_array($data) && isset($data['price']) && $data['price'] > 0) || 
                                                                                       (is_numeric($data) && $data > 0);
                                                                            });
                                                                        @endphp
                                                                        @foreach($company->addon_covers_breakdown as $addon => $data)
                                                                            @if(is_array($data) && isset($data['price']) && $data['price'] > 0)
                                                                                <div class="col-12 mb-2">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <strong class="small text-primary">{{ $addon }}</strong>
                                                                                        <strong class="small text-success">₹{{ number_format($data['price']) }}</strong>
                                                                                    </div>
                                                                                    @if(!empty($data['note']))
                                                                                        <div class="text-muted small mt-1">
                                                                                            <em>{{ $data['note'] }}</em>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @elseif(is_numeric($data) && $data > 0)
                                                                                <div class="col-12 mb-2">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <strong class="small text-primary">{{ $addon }}</strong>
                                                                                        <strong class="small text-success">₹{{ number_format($data) }}</strong>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <div class="text-center text-muted">
                                                                        <small>No addon breakdown details available</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> <strong>Note:</strong> This breakdown shows current saved add-on covers. 
                                                Any changes made in the quote forms above will be reflected after saving the quotation.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Any specific requirements or notes for this quotation...">{{ old('notes') ?? $quotation->notes }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save"></i> Update Quotation
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<!-- Company Deletion Confirmation Modal -->
<div class="modal fade" id="deleteCompanyModal" tabindex="-1" aria-labelledby="deleteCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold" id="deleteCompanyModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Company Removal
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="hideCustomModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="fas fa-building text-danger fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Remove Insurance Company</h6>
                        <p class="mb-0 text-muted small">This action will affect the quotation</p>
                    </div>
                </div>
                
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10" role="alert">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    <span id="modalConfirmationMessage" class="fw-medium">
                        Are you sure you want to remove this company quote?
                    </span>
                </div>
                
                <div class="bg-light rounded p-3 mt-3">
                    <div class="row text-center">
                        <div class="col-6">
                            <i class="fas fa-building text-primary mb-2"></i>
                            <div class="small text-muted">Company</div>
                            <div class="fw-bold" id="modalCompanyName">-</div>
                        </div>
                        <div class="col-6">
                            <i class="fas fa-tag text-success mb-2"></i>
                            <div class="small text-muted">Type</div>
                            <div class="fw-bold">
                                <span class="badge" id="modalCompanyType">New Quote</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary px-4" onclick="hideCustomModal()">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span id="confirmDeleteText">Remove Quote</span>
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <style>
        /* Company removal animations */
        .quote-entry.removing {
            background-color: #f8d7da !important;
            border-color: #dc3545 !important;
            transition: all 0.3s ease;
        }
        
        .quote-entry.company-removed {
            opacity: 0.5;
            background-color: #f8f9fa;
        }
        
        /* Confirmation dialog enhancements */
        .removing .card-header {
            background-color: #dc3545 !important;
        }
        
        .removing .btn {
            opacity: 0.7;
        }
    </style>
    <script>
        $(document).ready(function() {
            // Initialize Form Validation for Quotation Edit
            const validator = new FormValidator('form');
            
            // Define validation rules for quotation edit form
            validator.addRules({
                customer_id: { 
                    rules: { required: true },
                    displayName: 'Customer'
                },
                branch_id: { 
                    rules: { required: true },
                    displayName: 'Branch'
                },
                insurance_company_id: { 
                    rules: { required: true },
                    displayName: 'Insurance Company'
                },
                policy_type_id: { 
                    rules: { required: true },
                    displayName: 'Policy Type'
                },
                vehicle_no: { 
                    rules: { 
                        required: true,
                        pattern: '^[A-Z]{2}[0-9]{2}[A-Z]{1,2}[0-9]{4}$',
                        patternMessage: 'Vehicle number must be in format: XX00XX0000'
                    },
                    displayName: 'Vehicle Number'
                },
                make: { 
                    rules: { required: true, minLength: 2 },
                    displayName: 'Vehicle Make'
                },
                model: { 
                    rules: { required: true, minLength: 2 },
                    displayName: 'Vehicle Model'
                },
                mfg_year: { 
                    rules: { required: true, numeric: true, min: 1980, max: new Date().getFullYear() + 1 },
                    displayName: 'Manufacturing Year'
                },
                reg_date: { 
                    rules: { required: true, date: true },
                    displayName: 'Registration Date'
                },
                engine_no: { 
                    rules: { required: true, minLength: 5 },
                    displayName: 'Engine Number'
                },
                chassis_no: { 
                    rules: { required: true, minLength: 10 },
                    displayName: 'Chassis Number'
                },
                fuel_type: { 
                    rules: { required: true },
                    displayName: 'Fuel Type'
                },
                whatsapp_number: { 
                    rules: { phone: true }, // Optional but validated if provided
                    displayName: 'WhatsApp Number'
                }
            });

            // Enable real-time validation
            validator.enableRealTimeValidation();
            
            // Handle deleted companies persistence during validation errors
            restoreDeletedCompaniesState();
            
            // Clear deleted companies on successful form submission
            $('#quotationForm').on('submit', function() {
                sessionStorage.removeItem('deletedCompanies');
            });
            // Initialize Bootstrap tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Initialize Select2 for Customer dropdown with enhanced search
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
                    
                    // Get mobile number from data attribute if available  
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
                    
                    // Show just the customer name in the selection
                    const customerName = option.text.split(' - ')[0];
                    return customerName;
                }
            });

            // Auto-populate WhatsApp number when customer is selected
            $('#customer_id').on('select2:select', function (e) {
                const selectedOption = e.params.data;
                const $selectedElement = $(selectedOption.element);
                const mobile = $selectedElement.data('mobile');
                
                if (mobile && !$('#whatsapp_number').val()) {
                    $('#whatsapp_number').val(mobile);
                }
            });

            // Clear WhatsApp number when customer is cleared
            $('#customer_id').on('select2:clear', function (e) {
                $('#whatsapp_number').val('');
            });

            // Convert vehicle number to uppercase
            $('#vehicle_number').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Form validation enhancement
            $('#quotationForm').on('submit', function() {
                $('#submitBtn').prop('disabled', true);
                $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            });

            // Manual Quote Entry System
            let quoteIndex = {{ $quotation->quotationCompanies->count() }};

            // Initialize premium calculations for existing quotes
            setTimeout(function() {
                $('.quote-entry').each(function() {
                    const quoteCard = $(this);
                    if (quoteCard.find('.premium-field').filter(function() {
                            return $(this).val() > 0;
                        }).length > 0) {
                        calculateQuotePremium(quoteCard);
                    }
                    // Initialize IDV calculations
                    calculateIdvTotal(quoteCard);
                });
            }, 100);

            function addQuoteForm() {
                const currentIndex = quoteIndex;

                // Make AJAX call to get the quote form HTML
                $.ajax({
                    url: '{{ route("quotations.get-quote-form") }}',
                    type: 'GET',
                    data: {
                        index: currentIndex,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Append the rendered HTML to the container
                        $('#quotesContainer').append(response);
                        $('#noQuotesMessage').hide();

                        // Initialize IDV calculation for new form
                        const newQuoteCard = $('.quote-entry').last();
                        calculateIdvTotal(newQuoteCard);

                        quoteIndex++;
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading quote form:', error);
                        alert('Error loading quote form. Please try again.');
                    }
                });
            }

            // Remove quote functionality with modern confirmation modal
            let pendingDeletionEntry = null;
            
            $(document).on('click', '.removeQuoteBtn', function() {
                const quoteEntry = $(this).closest('.quote-entry');
                const isExisting = quoteEntry.hasClass('existing-quote');
                const companyName = quoteEntry.find('select[name*="[insurance_company_id]"] option:selected').text() || 'Unknown Company';
                
                // Store the entry for deletion after confirmation
                pendingDeletionEntry = quoteEntry;
                
                // Update modal content
                $('#modalCompanyName').text(companyName);
                
                if (isExisting) {
                    $('#modalConfirmationMessage').html(
                        `<strong>Permanent Deletion:</strong> This will permanently remove the quote from <strong>${companyName}</strong> from the database. This action cannot be undone.`
                    );
                    $('#modalCompanyType').removeClass('bg-info').addClass('bg-danger').text('Existing Quote');
                    $('#confirmDeleteText').text('Delete Permanently');
                } else {
                    $('#modalConfirmationMessage').html(
                        `<strong>Remove Quote Form:</strong> This will remove the quote form for <strong>${companyName}</strong> from this quotation.`
                    );
                    $('#modalCompanyType').removeClass('bg-danger').addClass('bg-info').text('New Quote');
                    $('#confirmDeleteText').text('Remove Quote');
                }
                
                // Show the modal using custom JavaScript (no Bootstrap JS available)
                showCustomModal();
            });

            // Handle modal confirmation
            $('#confirmDeleteBtn').on('click', function() {
                if (!pendingDeletionEntry) return;
                
                const quoteEntry = pendingDeletionEntry;
                const isExisting = quoteEntry.hasClass('existing-quote');
                
                // Track deletions in session storage to persist during validation errors
                if (isExisting) {
                    const companyId = quoteEntry.data('company-id');
                    if (companyId) {
                        let deletedCompanies = JSON.parse(sessionStorage.getItem('deletedCompanies') || '[]');
                        if (!deletedCompanies.includes(companyId)) {
                            deletedCompanies.push(companyId);
                            sessionStorage.setItem('deletedCompanies', JSON.stringify(deletedCompanies));
                        }
                    }
                }
                
                // Add visual feedback and hide modal
                hideCustomModal();
                quoteEntry.addClass('removing');
                quoteEntry.fadeOut(300, function() {
                    $(this).remove();
                    
                    if ($('.quote-entry').length === 0) {
                        $('#noQuotesMessage').show();
                    }
                });
                
                // Reset pending deletion
                pendingDeletionEntry = null;
            });

            // Custom modal functions (no Bootstrap JS available)
            function showCustomModal() {
                $('#deleteCompanyModal').css('display', 'block').addClass('show');
                $('body').addClass('modal-open');
                $('.modal-backdrop').remove();
                $('body').append('<div class="modal-backdrop fade show"></div>');
            }

            function hideCustomModal() {
                $('#deleteCompanyModal').css('display', 'none').removeClass('show');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                pendingDeletionEntry = null; // Reset on close
            }

            // Handle modal close events
            $(document).on('click', '.modal-backdrop', function() {
                if ($('#deleteCompanyModal').hasClass('show')) {
                    hideCustomModal();
                }
            });

            // Handle ESC key to close modal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#deleteCompanyModal').hasClass('show')) {
                    hideCustomModal();
                }
            });

            // Function to restore deleted companies state after validation errors
            function restoreDeletedCompaniesState() {
                const deletedCompanies = JSON.parse(sessionStorage.getItem('deletedCompanies') || '[]');
                
                deletedCompanies.forEach(companyId => {
                    // Find the company form by data-company-id
                    const companyCard = $(`.quote-entry.existing-quote[data-company-id="${companyId}"]`);
                    
                    if (companyCard.length > 0) {
                        companyCard.addClass('removing').hide();
                        
                        // Remove the form data from submission so it doesn't get recreated
                        companyCard.find('input, select, textarea').each(function() {
                            $(this).prop('disabled', true);
                        });
                    }
                });
                
            }

            // Auto-calculate IDV total for each company (multiple events to ensure it triggers)
            $(document).on('input change keyup blur', '.idv-field', function() {
                const quoteCard = $(this).closest('.quote-entry');
                if (quoteCard.length > 0) {
                    calculateIdvTotal(quoteCard);
                }
            });

            // Auto-calculate addon total
            $(document).on('input', '.addon-field', function() {
                const quoteCard = $(this).closest('.quote-entry');
                calculateAddonTotal(quoteCard);
            });

            // Show/hide recommendation note field
            $(document).on('change', '.recommendation-checkbox', function() {
                const isChecked = $(this).is(':checked');
                const index = $(this).data('index');
                const noteField = $(`#recommendation_note_field_${index}`);
                
                if (isChecked) {
                    noteField.show();
                    // Make note field required when recommendation is checked
                    noteField.find('textarea').prop('required', true);
                } else {
                    noteField.hide();
                    // Clear the note field and make it not required
                    noteField.find('textarea').prop('required', false).val('');
                }
            });

            // Auto-calculate premium fields
            $(document).on('input', '.premium-field, .total-addon-premium', function() {
                const quoteCard = $(this).closest('.quote-entry');
                calculateQuotePremium(quoteCard);
            });

            function calculateIdvTotal(quoteCard) {
                
                // Calculate total of all IDV fields for each company
                let idvTotal = 0;
                
                const idvVehicle = parseFloat(quoteCard.find('[name*="[idv_vehicle]"]').val()) || 0;
                const idvTrailer = parseFloat(quoteCard.find('[name*="[idv_trailer]"]').val()) || 0;
                const idvCngLpg = parseFloat(quoteCard.find('[name*="[idv_cng_lpg_kit]"]').val()) || 0;
                const idvElectrical = parseFloat(quoteCard.find('[name*="[idv_electrical_accessories]"]').val()) || 0;
                const idvNonElectrical = parseFloat(quoteCard.find('[name*="[idv_non_electrical_accessories]"]').val()) || 0;
                
                
                idvTotal = idvVehicle + idvTrailer + idvCngLpg + idvElectrical + idvNonElectrical;
                
                
                // Update the total IDV field
                const totalIdvField = quoteCard.find('.total-idv');
                
                totalIdvField.val(idvTotal.toFixed(2));
                
                // Add visual highlight
                if (idvTotal > 0) {
                    totalIdvField.css('background-color', '#d1ecf1');
                } else {
                    totalIdvField.css('background-color', '');
                }
                
            }

            function calculateAddonTotal(quoteCard) {
                // Calculate total of all addon fields
                let addonTotal = 0;

                quoteCard.find('.addon-field').each(function() {
                    const value = parseFloat($(this).val()) || 0;
                    addonTotal += value;
                });

                // Update the total addon premium field
                quoteCard.find('.total-addon-premium').val(addonTotal.toFixed(2));

                // Trigger overall premium calculation
                calculateQuotePremium(quoteCard);
            }

            function calculateQuotePremium(quoteCard) {
                const basicOd = parseFloat(quoteCard.find('[name*="[basic_od_premium]"]').val()) || 0;
                const tpPremium = parseFloat(quoteCard.find('[name*="[tp_premium]"]').val()) || 0;
                const addonPremium = parseFloat(quoteCard.find('[name*="[total_addon_premium]"]').val()) || 0;
                const cngLpg = parseFloat(quoteCard.find('[name*="[cng_lpg_premium]"]').val()) || 0;

                const netPremium = basicOd + tpPremium + addonPremium + cngLpg;
                const gstAmount = netPremium * 0.18; // 18% GST
                const finalPremium = netPremium + gstAmount;

                quoteCard.find('.net-premium').val(netPremium.toFixed(2));
                
                // Update SGST and CGST fields (half of total GST each)
                const sgstAmount = gstAmount / 2;
                const cgstAmount = gstAmount / 2;
                
                // Check if we have separate SGST/CGST fields (new form) or combined GST field (old form)
                if (quoteCard.find('.sgst-amount').length > 0) {
                    // New form with separate SGST/CGST fields
                    quoteCard.find('.sgst-amount').val(sgstAmount.toFixed(2));
                    quoteCard.find('.cgst-amount').val(cgstAmount.toFixed(2));
                } else if (quoteCard.find('.gst-amount').length > 0) {
                    // Old form with combined GST field
                    quoteCard.find('.gst-amount').val(gstAmount.toFixed(2));
                }
                
                quoteCard.find('.final-premium').val(finalPremium.toFixed(2));

                // Update or add hidden fields for backend (for old server-side rendered forms)
                const index = quoteCard.data('index');
                if (quoteCard.find('[name*="[sgst_amount]"]').length > 0 && quoteCard.find('[name*="[sgst_amount]"]').attr('type') === 'hidden') {
                    // Only update hidden fields, not visible ones
                    quoteCard.find('input[type="hidden"][name*="[sgst_amount]"]').remove();
                    quoteCard.find('input[type="hidden"][name*="[cgst_amount]"]').remove();
                    quoteCard.find('input[type="hidden"][name*="[total_od_premium]"]').remove();

                    quoteCard.append(`
                        <input type="hidden" name="companies[${index}][sgst_amount]" value="${sgstAmount.toFixed(2)}">
                        <input type="hidden" name="companies[${index}][cgst_amount]" value="${cgstAmount.toFixed(2)}">
                        <input type="hidden" name="companies[${index}][total_od_premium]" value="${(basicOd + tpPremium + cngLpg).toFixed(2)}">
                    `);
                }
            }

            // Dynamic addon checkbox functionality
            $(document).on('change', '.addon-checkbox', function() {
                const slug = $(this).data('slug');
                const isChecked = $(this).is(':checked');
                const fieldsContainer = $(this).closest('.addon-field-container').find('.addon-fields');

                if (isChecked) {
                    fieldsContainer.show();
                } else {
                    fieldsContainer.hide();
                    // Clear values when unchecked - set to empty string (null) not 0
                    fieldsContainer.find('.addon-field, .addon-note').val('');
                }
                
                // Recalculate addon total for the current quote entry
                const quoteEntry = $(this).closest('.quote-entry');
                if (quoteEntry.length) {
                    calculateAddonTotal(quoteEntry);
                }
            });

            // Initialize addon field visibility based on checkbox states
            function initializeAddonVisibility(shouldClearValues = false) {
                $('.addon-checkbox').each(function() {
                    const isChecked = $(this).is(':checked');
                    const fieldsContainer = $(this).closest('.addon-field-container').find('.addon-fields');

                    if (isChecked) {
                        fieldsContainer.show();
                    } else {
                        fieldsContainer.hide();
                        // Only reset values when explicitly requested (user action, not initialization)
                        if (shouldClearValues) {
                            fieldsContainer.find('.addon-field, .addon-note').val('');
                        }
                    }
                });
            }

            // Initialize on page load
            setTimeout(function() {
                // Initialize addon field visibility on page load
                initializeAddonVisibility();
                
                // Calculate IDV for all existing quote cards on page load
                $('.quote-entry').each(function() {
                    calculateIdvTotal($(this));
                });
            }, 100);

            // Add Quote button click handler
            $('#addQuoteBtn').off('click').on('click', function() {
                addQuoteForm();

                // Apply addon synchronization after the form is added
                setTimeout(function() {
                    initializeAddonVisibility();
                    
                    // Calculate IDV for the newly added quote card
                    const newQuoteCard = $('.quote-entry').last();
                    calculateIdvTotal(newQuoteCard);
                }, 50);
            });
            
            // Function to show addon breakdown info message
            function showAddonBreakdownInfo() {
                if ($('#addonBreakdownSection').length > 0) {
                    $('#addonBreakdownSection .alert-info').html(
                        '<i class="fas fa-info-circle"></i> <strong>Note:</strong> This breakdown shows current saved add-on covers. ' +
                        'Any changes made in the quote forms above will be reflected after saving the quotation. ' +
                        '<span class="text-warning">You have unsaved changes to addon covers.</span>'
                    );
                }
            }
            
            // Monitor addon field changes to show info message
            $(document).on('input change', '.addon-field', function() {
                showAddonBreakdownInfo();
            });
            
            $(document).on('change', '.addon-checkbox', function() {
                showAddonBreakdownInfo();
            });

            // Add helpful message about addon breakdown section
            if ($('#addonBreakdownSection').length > 0) {
                $('#addonBreakdownSection').prepend(
                    '<div class="alert alert-success">'+
                    '<i class="fas fa-chart-bar"></i> <strong>Addon Coverage Summary:</strong> ' +
                    'This shows the current add-on covers from saved quotes. Edit individual quotes above to modify add-on covers.' +
                    '</div>'
                );
            }
        });

        // Modal functions are now centralized in layouts/app.blade.php
    </script>
@endsection

<!-- Send WhatsApp Modal -->
<div class="modal fade whatsapp-modal" id="sendWhatsAppModal" tabindex="-1" role="dialog" aria-labelledby="sendWhatsAppModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="sendWhatsAppModalLabel">
                    <i class="fab fa-whatsapp"></i> Send Quotation via WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="hideWhatsAppModal('sendWhatsAppModal')" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fab fa-whatsapp fa-3x text-success"></i>
                </div>
                <p class="text-center">Send quotation with PDF attachment to:</p>
                <div class="alert alert-info">
                    <strong>Quotation:</strong> {{ $quotation->getQuoteReference() }}<br>
                    <strong>Customer:</strong> {{ $quotation->customer->name }}<br>
                    <strong>WhatsApp Number:</strong> {{ $quotation->whatsapp_number ?? $quotation->customer->mobile_number }}
                </div>
                <p class="text-muted small">This will generate and attach a PDF comparison of all quotes.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideWhatsAppModal('sendWhatsAppModal')">Cancel</button>
                <form method="POST" action="{{ route('quotations.send-whatsapp', $quotation) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fab fa-whatsapp"></i> Send Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Resend WhatsApp Modal -->
<div class="modal fade" id="resendWhatsAppModal" tabindex="-1" role="dialog" aria-labelledby="resendWhatsAppModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="resendWhatsAppModalLabel">
                    <i class="fab fa-whatsapp"></i> Resend Quotation via WhatsApp
                </h5>
                <button type="button" class="btn-close" onclick="hideWhatsAppModal('resendWhatsAppModal')" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fab fa-whatsapp fa-3x text-warning"></i>
                </div>
                <p class="text-center">Resend quotation with updated PDF attachment to:</p>
                <div class="alert alert-warning">
                    <strong>Quotation:</strong> {{ $quotation->getQuoteReference() }}<br>
                    <strong>Customer:</strong> {{ $quotation->customer->name }}<br>
                    <strong>WhatsApp Number:</strong> {{ $quotation->whatsapp_number ?? $quotation->customer->mobile_number }}<br>
                    <strong>Last Sent:</strong> {{ $quotation->sent_at ? $quotation->sent_at->format('d M Y, H:i') : 'Not available' }}
                </div>
                <p class="text-muted small">This will generate a fresh PDF with current quotes and send it again.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideWhatsAppModal('resendWhatsAppModal')">Cancel</button>
                <form method="POST" action="{{ route('quotations.send-whatsapp', $quotation) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fab fa-whatsapp"></i> Resend Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>