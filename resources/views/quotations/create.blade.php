@extends('layouts.app')

@section('title', 'Create Insurance Quotation')

@section('content')
    <div class="container-fluid px-4">
        {{-- Enhanced Alert Messages --}}
        @include('common.alert')

        <!-- Enhanced Quotation Form with Modern Design -->
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-gradient-primary py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-file-invoice-dollar fs-4 text-white opacity-75"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white">Create Insurance Quotation</h5>
                            <small class="text-white-50">Compare multiple insurance quotes and generate recommendations</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-info px-3 py-2 rounded-pill">
                            <i class="fas fa-list-ol me-1"></i>Step 1 of 2
                        </span>
                        <a href="{{ route('quotations.index') }}" 
                           class="btn btn-outline-light btn-sm rounded-pill px-3" 
                           title="Back to Quotations">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                {{-- Enhanced Error Display --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle fs-5 text-danger me-3 mt-1"></i>
                            <div>
                                <h6 class="alert-heading mb-2">Please correct the following issues:</h6>
                                <ul class="mb-0 list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li class="mb-1"><i class="fas fa-dot-circle text-danger me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('quotations.store') }}" id="quotationForm">
                    @csrf

                    {{-- Customer Information Section --}}
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-user-tie me-2"></i>Customer Information
                            </h6>
                            <small class="text-muted">Select the customer for this insurance quotation</small>
                        </div>
                        <div class="card-body pt-2">
                            <div class="row g-4">
                                <div class="col-lg-8 col-md-12">
                                    <label for="customer_id" class="form-label text-sm fw-bold">
                                        <span class="text-danger">*</span> Customer
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <select name="customer_id" id="customer_id"
                                            class="form-select border-start-0 select2 @error('customer_id') is-invalid @enderror"
                                            required>
                                            <option value="">Search and select customer...</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    data-mobile="{{ $customer->mobile_number }}"
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                    @if ($customer->mobile_number)
                                                        - {{ $customer->mobile_number }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('customer_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Start typing to search by name or mobile number</small>
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <label for="whatsapp_number" class="form-label text-sm fw-bold">
                                        WhatsApp Number
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fab fa-whatsapp text-success"></i>
                                        </span>
                                        <input type="text" name="whatsapp_number" id="whatsapp_number"
                                            class="form-control border-start-0 @error('whatsapp_number') is-invalid @enderror"
                                            placeholder="Auto-populated from customer"
                                            value="{{ old('whatsapp_number') }}">
                                    </div>
                                    @error('whatsapp_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">For sending quotation documents</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Information Section --}}
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-car-side me-2"></i>Vehicle Information
                            </h6>
                            <small class="text-muted">Enter vehicle details for insurance quotation</small>
                        </div>
                        <div class="card-body pt-2">
                            {{-- Vehicle Basic Details --}}
                            <div class="row g-4 mb-4">
                                <div class="col-lg-4 col-md-6">
                                    <label for="vehicle_number" class="form-label text-sm fw-bold">
                                        Vehicle Registration Number
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-id-badge text-muted"></i>
                                        </span>
                                        <input type="text" name="vehicle_number" id="vehicle_number"
                                            class="form-control border-start-0 text-uppercase @error('vehicle_number') is-invalid @enderror"
                                            placeholder="GJ05AB1234 (Leave blank for new vehicle)"
                                            value="{{ old('vehicle_number') }}">
                                    </div>
                                    @error('vehicle_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Enter existing vehicle number or leave blank for new vehicle</small>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="make_model_variant" class="form-label text-sm fw-bold">
                                        <span class="text-danger">*</span> Make/Model/Variant
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-car text-muted"></i>
                                        </span>
                                        <input type="text" name="make_model_variant" id="make_model_variant"
                                            class="form-control border-start-0 @error('make_model_variant') is-invalid @enderror"
                                            placeholder="e.g., Maruti Swift VDI"
                                            value="{{ old('make_model_variant') }}" required>
                                    </div>
                                    @error('make_model_variant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="rto_location" class="form-label text-sm fw-bold">
                                        <span class="text-danger">*</span> RTO Location
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                        </span>
                                        <input type="text" name="rto_location" id="rto_location"
                                            class="form-control border-start-0 @error('rto_location') is-invalid @enderror"
                                            placeholder="e.g., Ahmedabad" value="{{ old('rto_location') }}" required>
                                    </div>
                                    @error('rto_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Vehicle Technical Details --}}
                            <div class="row g-4 mb-4">
                                <div class="col-lg-4 col-md-6">
                                    <label for="manufacturing_year" class="form-label text-sm fw-bold">
                                        <span class="text-danger">*</span> Manufacturing Year
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                        </span>
                                        <select name="manufacturing_year" id="manufacturing_year"
                                            class="form-select border-start-0 @error('manufacturing_year') is-invalid @enderror" required>
                                            <option value="">Select Year</option>
                                            @for ($year = date('Y'); $year >= 1990; $year--)
                                                <option value="{{ $year }}"
                                                    {{ old('manufacturing_year') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    @error('manufacturing_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="fuel_type" class="form-label text-sm fw-bold">
                                        <span class="text-danger">*</span> Fuel Type
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-gas-pump text-muted"></i>
                                        </span>
                                        <select name="fuel_type" id="fuel_type"
                                            class="form-select border-start-0 @error('fuel_type') is-invalid @enderror" required>
                                            <option value="">Select Fuel Type</option>
                                            <option value="Petrol" {{ old('fuel_type') == 'Petrol' ? 'selected' : '' }}>Petrol</option>
                                            <option value="Diesel" {{ old('fuel_type') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                            <option value="CNG" {{ old('fuel_type') == 'CNG' ? 'selected' : '' }}>CNG</option>
                                            <option value="Electric" {{ old('fuel_type') == 'Electric' ? 'selected' : '' }}>Electric</option>
                                            <option value="Hybrid" {{ old('fuel_type') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                        </select>
                                    </div>
                                    @error('fuel_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Additional Vehicle Details --}}
                            <div class="row g-4">
                                <div class="col-lg-4 col-md-6">
                                    <label for="ncb_percentage" class="form-label text-sm fw-bold">
                                        NCB Percentage
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-percentage text-muted"></i>
                                        </span>
                                        <input type="number" name="ncb_percentage" id="ncb_percentage"
                                            class="form-control border-start-0 @error('ncb_percentage') is-invalid @enderror"
                                            value="{{ old('ncb_percentage', 0) }}" min="0" max="50" step="1" placeholder="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('ncb_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">No Claim Bonus (0-50%)</small>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="cubic_capacity_kw" class="form-label text-sm fw-bold">
                                        <span class="text-danger">*</span> Cubic Capacity (CC/KW)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-cogs text-muted"></i>
                                        </span>
                                        <input type="number" name="cubic_capacity_kw" id="cubic_capacity_kw"
                                            class="form-control border-start-0 @error('cubic_capacity_kw') is-invalid @enderror"
                                            placeholder="e.g., 1200" value="{{ old('cubic_capacity_kw') }}" required>
                                    </div>
                                    @error('cubic_capacity_kw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="seating_capacity" class="form-label text-sm fw-bold">
                                        <span class="text-danger">*</span> Seating Capacity
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-users text-muted"></i>
                                        </span>
                                        <input type="number" name="seating_capacity" id="seating_capacity"
                                            class="form-control border-start-0 @error('seating_capacity') is-invalid @enderror"
                                            placeholder="e.g., 5" value="{{ old('seating_capacity') }}" required>
                                    </div>
                                    @error('seating_capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Insurance Company Quotes Section --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-primary py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-building fs-5 text-white opacity-75"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-white">Insurance Company Quotes</h6>
                                        <small class="text-white-50">Add and compare quotes from different insurers</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" id="addQuoteBtn">
                                    <i class="fas fa-plus-circle me-2"></i>Add New Quote
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle fs-5 text-info me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-2">Quote Comparison System</h6>
                                        <p class="mb-0">Add quotes manually from different insurance companies. You can add multiple quotes to compare premiums, coverage options, and recommend the best option for your customer.</p>
                                    </div>
                                </div>
                            </div>

                                    <div id="quotesContainer">
                                        <!-- Server-side rendered quotes (for validation failures) -->
                                        @if (old('companies'))
                                            @foreach (old('companies') as $index => $company)
                                                <div class="card border-left-info mb-3 quote-entry"
                                                    data-index="{{ $index }}">
                                                    <div
                                                        class="card-header bg-light py-1 d-flex justify-content-between align-items-center">
                                                        <h6 class="m-0"><i class="fas fa-quote-left"></i> Quote
                                                            #{{ $index + 1 }}</h6>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger removeQuoteBtn">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <!-- Premium Details Section -->
                                                        <div class="card border-left-primary mb-3">
                                                            <div class="card-header bg-primary text-white py-1">
                                                                <h6 class="m-0 small"><i class="fas fa-money-bill-wave"></i> Premium Details</h6>
                                                            </div>
                                                            <div class="card-body p-3">
                                                                <!-- Row 1: Quote Number, Basic OD Premium, TP Premium -->
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">Quote Number</label>
                                                                            <input type="text" name="companies[{{ $index }}][quote_number]" class="form-control form-control-sm @error("companies.{$index}.quote_number") is-invalid @enderror" placeholder="Company quote reference number" value="{{ old("companies.{$index}.quote_number") }}">
                                                                            @error("companies.{$index}.quote_number")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">Basic OD Premium (₹) <span class="text-danger">*</span></label>
                                                                            <input type="number" name="companies[{{ $index }}][basic_od_premium]" class="form-control form-control-sm premium-field @error("companies.{$index}.basic_od_premium") is-invalid @enderror" step="1" required value="{{ old("companies.{$index}.basic_od_premium") }}">
                                                                            @error("companies.{$index}.basic_od_premium")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">TP Premium (₹) <span class="text-danger">*</span></label>
                                                                            <input type="number" name="companies[{{ $index }}][tp_premium]" class="form-control form-control-sm premium-field @error("companies.{$index}.tp_premium") is-invalid @enderror" step="1" required value="{{ old("companies.{$index}.tp_premium") }}">
                                                                            @error("companies.{$index}.tp_premium")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Row 2: CNG/LPG Premium -->
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">CNG/LPG Premium (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][cng_lpg_premium]" class="form-control form-control-sm premium-field @error("companies.{$index}.cng_lpg_premium") is-invalid @enderror" step="1" value="{{ old("companies.{$index}.cng_lpg_premium") }}" placeholder="0">
                                                                            @error("companies.{$index}.cng_lpg_premium")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Coverage Details Section -->
                                                        <div class="card border-left-success mb-3">
                                                            <div class="card-header bg-success text-white py-1">
                                                                <h6 class="m-0 small"><i class="fas fa-shield-alt"></i> Coverage Details</h6>
                                                            </div>
                                                            <div class="card-body p-3">
                                                                <!-- Row 1: Insurance Company, Policy Type, Policy Tenure -->
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">Insurance Company <span class="text-danger">*</span></label>
                                                                            <select name="companies[{{ $index }}][insurance_company_id]" class="form-control form-control-sm company-select @error("companies.{$index}.insurance_company_id") is-invalid @enderror" required>
                                                                                <option value="">Select Company</option>
                                                                                @foreach ($insuranceCompanies as $insuranceCompany)
                                                                                    <option value="{{ $insuranceCompany->id }}"
                                                                                        {{ old("companies.{$index}.insurance_company_id") == $insuranceCompany->id ? 'selected' : '' }}>
                                                                                        {{ $insuranceCompany->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error("companies.{$index}.insurance_company_id")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">Policy Type <span class="text-danger">*</span></label>
                                                                            <select name="companies[{{ $index }}][policy_type]" class="form-control form-control-sm @error("companies.{$index}.policy_type") is-invalid @enderror" required>
                                                                                <option value="">Select Policy Type</option>
                                                                                <option value="Comprehensive" {{ old("companies.{$index}.policy_type") == 'Comprehensive' ? 'selected' : '' }}>Comprehensive</option>
                                                                                <option value="Own Damage" {{ old("companies.{$index}.policy_type") == 'Own Damage' ? 'selected' : '' }}>Own Damage</option>
                                                                                <option value="Third Party" {{ old("companies.{$index}.policy_type") == 'Third Party' ? 'selected' : '' }}>Third Party</option>
                                                                            </select>
                                                                            @error("companies.{$index}.policy_type")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">Policy Tenure <span class="text-danger">*</span></label>
                                                                            <select name="companies[{{ $index }}][policy_tenure_years]" class="form-control form-control-sm @error("companies.{$index}.policy_tenure_years") is-invalid @enderror" required>
                                                                                <option value="">Select Tenure</option>
                                                                                <option value="1" {{ old("companies.{$index}.policy_tenure_years") == '1' ? 'selected' : '' }}>1 Year</option>
                                                                                <option value="2" {{ old("companies.{$index}.policy_tenure_years") == '2' ? 'selected' : '' }}>2 Years</option>
                                                                                <option value="3" {{ old("companies.{$index}.policy_tenure_years") == '3' ? 'selected' : '' }}>3 Years</option>
                                                                            </select>
                                                                            @error("companies.{$index}.policy_tenure_years")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Row 2: IDV Vehicle, IDV Trailer, IDV CNG/LPG Kit -->
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">IDV Vehicle (₹) <span class="text-danger">*</span></label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_vehicle]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_vehicle") is-invalid @enderror" step="1" required placeholder="e.g., 500000" value="{{ old("companies.{$index}.idv_vehicle") }}">
                                                                            @error("companies.{$index}.idv_vehicle")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">IDV Trailer (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_trailer]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_trailer") is-invalid @enderror" step="1" placeholder="0" value="{{ old("companies.{$index}.idv_trailer") }}">
                                                                            @error("companies.{$index}.idv_trailer")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">IDV CNG/LPG Kit (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_cng_lpg_kit]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_cng_lpg_kit") is-invalid @enderror" step="1" placeholder="0" value="{{ old("companies.{$index}.idv_cng_lpg_kit") }}">
                                                                            @error("companies.{$index}.idv_cng_lpg_kit")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Row 3: IDV Electrical Acc., IDV Non-Elec. Acc., Total IDV -->
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-0">
                                                                            <label class="small font-weight-bold">IDV Electrical Acc. (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_electrical_accessories]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_electrical_accessories") is-invalid @enderror" step="1" placeholder="0" value="{{ old("companies.{$index}.idv_electrical_accessories") }}">
                                                                            @error("companies.{$index}.idv_electrical_accessories")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-0">
                                                                            <label class="small font-weight-bold">IDV Non-Elec. Acc. (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_non_electrical_accessories]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_non_electrical_accessories") is-invalid @enderror" step="1" placeholder="0" value="{{ old("companies.{$index}.idv_non_electrical_accessories") }}">
                                                                            @error("companies.{$index}.idv_non_electrical_accessories")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6 mb-1">
                                                                        <div class="form-group mb-0">
                                                                            <label class="small font-weight-bold text-success">Total IDV (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][total_idv]" class="form-control form-control-sm total-idv font-weight-bold text-success @error("companies.{$index}.total_idv") is-invalid @enderror" step="1" readonly style="background: #d1ecf1; border-color: #28a745;" value="{{ old("companies.{$index}.total_idv") }}">
                                                                            @error("companies.{$index}.total_idv")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-12 mb-1">
                                                                <div class="card border-left-success mb-3">
                                                                    <div class="card-header bg-success text-white py-1">
                                                                        <h6 class="m-0 small"><i
                                                                                class="fas fa-plus-circle"></i> Add-on
                                                                            Covers Breakdown</h6>
                                                                    </div>
                                                                    <div class="card-body p-2">
                                                                        <div class="row">
                                                                            @php
                                                                                $columns = $addonCovers->chunk(ceil($addonCovers->count() / 3));
                                                                            @endphp
                                                                            @foreach ($columns as $columnCovers)
                                                                                <div class="col-md-4 col-sm-6 mb-1">
                                                                                    @foreach ($columnCovers as $addonCover)
                                                                                        @php
                                                                                            $slug = \Str::slug($addonCover->name, '_');
                                                                                        @endphp
                                                                                        <div class="form-group mb-2 addon-field-container"
                                                                                            data-addon="{{ $slug }}">
                                                                                            <div class="form-check">
                                                                                                <input class="form-check-input addon-checkbox"
                                                                                                    type="checkbox"
                                                                                                    id="addon_{{ $slug }}_{{ $index }}"
                                                                                                    data-slug="{{ $slug }}"
                                                                                                    {{ old("companies.{$index}.addon_{$slug}") ? 'checked' : '' }}>
                                                                                                <label class="form-check-label small"
                                                                                                    for="addon_{{ $slug }}_{{ $index }}">
                                                                                                    <strong>{{ $addonCover->name }}</strong>
                                                                                                    @if ($addonCover->description)
                                                                                                        <br><small class="text-muted">{{ $addonCover->description }}</small>
                                                                                                    @endif
                                                                                                </label>
                                                                                            </div>
                                                                                            <div class="addon-fields"
                                                                                                id="fields_{{ $slug }}_{{ $index }}"
                                                                                                style="{{ old("companies.{$index}.addon_{$slug}") ? 'display: block;' : 'display: none;' }}">
                                                                                                <label class="small">{{ $addonCover->name }} (₹)</label>
                                                                                                <input type="number"
                                                                                                    name="companies[{{ $index }}][addon_{{ $slug }}]"
                                                                                                    class="form-control form-control-sm addon-field @error("companies.{$index}.addon_{$slug}") is-invalid @enderror"
                                                                                                    step="1"
                                                                                                    value="{{ old("companies.{$index}.addon_{$slug}") }}"
                                                                                                    placeholder="Enter premium">
                                                                                                @error("companies.{$index}.addon_{$slug}")
                                                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                                                @enderror
                                                                                                <input type="text"
                                                                                                    name="companies[{{ $index }}][addon_{{ $slug }}_note]"
                                                                                                    class="form-control form-control-sm mt-1 addon-note @error("companies.{$index}.addon_{$slug}_note") is-invalid @enderror"
                                                                                                    maxlength="100"
                                                                                                    placeholder="Add note (coverage details, limits etc.)"
                                                                                                    value="{{ old("companies.{$index}.addon_{$slug}_note") }}">
                                                                                                @error("companies.{$index}.addon_{$slug}_note")
                                                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                                                @enderror
                                                                                                <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <hr class="my-2">
                                                                        <div class="row">
                                                                            <div class="col-md-12 mb-1">
                                                                                <div class="form-group mb-0">
                                                                                    <label
                                                                                        class="font-weight-bold text-success">Total
                                                                                        Add-on Premium (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][total_addon_premium]"
                                                                                        class="form-control form-control-sm total-addon-premium font-weight-bold @error("companies.{$index}.total_addon_premium") is-invalid @enderror"
                                                                                        step="1" readonly
                                                                                        style="background: #d1ecf1;"
                                                                                        value="{{ old("companies.{$index}.total_addon_premium") }}" placeholder="0">
                                                                                @error("companies.{$index}.total_addon_premium")
                                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                                @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-6 mb-1">
                                                                <div class="form-group">
                                                                    <label>Net Premium (₹)</label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][net_premium]"
                                                                        class="form-control net-premium" step="1"
                                                                        readonly
                                                                        value="{{ old("companies.{$index}.net_premium", 0) }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-6 mb-1">
                                                                <div class="form-group">
                                                                    <label>GST Amount (₹)</label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][gst_amount]"
                                                                        class="form-control gst-amount" step="1"
                                                                        readonly
                                                                        value="{{ old("companies.{$index}.gst_amount", 0) }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-6 mb-1">
                                                                <div class="form-group">
                                                                    <label><strong>Final Premium (₹)</strong></label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][final_premium]"
                                                                        class="form-control final-premium font-weight-bold"
                                                                        step="1" readonly
                                                                        style="background: #d4edda;"
                                                                        value="{{ old("companies.{$index}.final_premium", 0) }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 mb-1">
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                        name="companies[{{ $index }}][is_recommended]"
                                                                        value="1" class="form-check-input recommendation-checkbox"
                                                                        data-index="{{ $index }}"
                                                                        {{ old("companies.{$index}.is_recommended") ? 'checked' : '' }}>
                                                                    <label class="form-check-label font-weight-bold text-success">
                                                                        <i class="fas fa-star"></i> Mark as Recommended
                                                                    </label>
                                                                </div>
                                                                
                                                                <!-- Recommendation Note Field (initially hidden) -->
                                                                <div class="recommendation-note-field mt-3" id="recommendation_note_field_{{ $index }}" style="display: {{ old("companies.{$index}.is_recommended") ? 'block' : 'none' }};">
                                                                    <label class="small font-weight-bold text-success">
                                                                        <i class="fas fa-edit"></i> Recommendation Note <span class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea name="companies[{{ $index }}][recommendation_note]" 
                                                                              class="form-control form-control-sm @error("companies.{$index}.recommendation_note") is-invalid @enderror" 
                                                                              rows="2" maxlength="500"
                                                                              placeholder="Explain why this quote is recommended (e.g., best price, good coverage, trusted insurer...)"
                                                                              >{{ old("companies.{$index}.recommendation_note") }}</textarea>
                                                                    @error("companies.{$index}.recommendation_note")
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                    <small class="text-muted">Explain why you recommend this quote (Max 500 characters)</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Hidden fields for backend processing -->
                                                        <input type="hidden"
                                                            name="companies[{{ $index }}][sgst_amount]"
                                                            value="{{ old("companies.{$index}.sgst_amount", 0) }}">
                                                        <input type="hidden"
                                                            name="companies[{{ $index }}][cgst_amount]"
                                                            value="{{ old("companies.{$index}.cgst_amount", 0) }}">
                                                        <input type="hidden"
                                                            name="companies[{{ $index }}][total_od_premium]"
                                                            value="{{ old("companies.{$index}.total_od_premium", 0) }}">
                                                        <input type="hidden"
                                                            name="companies[{{ $index }}][total_premium]"
                                                            value="{{ old("companies.{$index}.total_premium", 0) }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <!-- Dynamic quote entries will be added here by JavaScript -->
                                    </div>

                            <div class="text-center py-5" id="noQuotesMessage" {{ old('companies') ? 'style=display:none;' : '' }}>
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3">
                                        <i class="fas fa-clipboard-list fs-1 text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">No Quotes Added Yet</h6>
                                    <p class="text-muted small mb-3">Start by adding your first insurance company quote using the button above</p>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" onclick="$('#addQuoteBtn').click()">
                                        <i class="fas fa-plus me-2"></i>Add First Quote
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Notes Section --}}
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-sticky-note me-2"></i>Additional Notes
                            </h6>
                            <small class="text-muted">Add any specific requirements or important details</small>
                        </div>
                        <div class="card-body pt-2">
                            <div class="row">
                                <div class="col-12">
                                    <label for="notes" class="form-label text-sm fw-bold">Notes & Special Requirements</label>
                                    <textarea name="notes" id="notes" rows="4" 
                                        class="form-control @error('notes') is-invalid @enderror"
                                        placeholder="Enter any specific requirements, customer preferences, or important notes for this quotation...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">These notes will be included in the quotation document</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            
            {{-- Enhanced Form Footer --}}
            <div class="card-footer bg-light border-0 py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Make sure all required fields are filled before creating the quotation
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('quotations.index') }}" 
                           class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" form="quotationForm" 
                                class="btn btn-primary rounded-pill px-4" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Create Quotation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // ===============================================
            // Enhanced Quotations Create Form JavaScript
            // ===============================================
            
            console.log('🚀 Initializing Enhanced Quotations Create Form');

            // Initialize enhanced notifications for better UX
            if (typeof window.NotificationManager !== 'undefined') {
                window.notificationManager = new NotificationManager();
            }

            // Initialize Bootstrap tooltips and popovers for better UX
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('[data-bs-toggle="popover"]').popover();
            
            // Enhanced Select2 initialization with modern styling
            $('#customer_id').select2({
                placeholder: 'Search customers by name or mobile number...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                theme: 'bootstrap-5',
                selectionCssClass: 'select2-selection--enhanced',
                dropdownCssClass: 'select2-dropdown--enhanced',
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
                        return `
                            <div class="d-flex align-items-center p-2">
                                <div class="me-3">
                                    <i class="fas fa-user-circle fs-5 text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${customerName}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-mobile-alt me-1"></i>${mobile}
                                    </small>
                                </div>
                            </div>
                        `;
                    }
                    
                    return `
                        <div class="d-flex align-items-center p-2">
                            <div class="me-3">
                                <i class="fas fa-user-circle fs-5 text-primary"></i>
                            </div>
                            <div class="fw-bold">${customerName}</div>
                        </div>
                    `;
                },
                templateSelection: function(option) {
                    if (!option.id) {
                        return option.text;
                    }
                    
                    const customerName = option.text.split(' - ')[0];
                    return customerName;
                }
            });

            // Enhanced dropdown styling for other selects
            $('#manufacturing_year, #fuel_type').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumResultsForSearch: Infinity // Hide search for simple dropdowns
            });

            // ===============================================
            // Smart Auto-Population Features
            // ===============================================
            
            // Enhanced customer selection with smart WhatsApp population
            $('#customer_id').on('select2:select', function (e) {
                const selectedOption = e.params.data;
                const $selectedElement = $(selectedOption.element);
                const mobile = $selectedElement.data('mobile');
                
                if (mobile) {
                    $('#whatsapp_number').val(mobile);
                    
                    // Show success notification with modern styling
                    if (window.notificationManager) {
                        window.notificationManager.show('WhatsApp number auto-populated from customer data', 'success', 2000);
                    }
                    
                    // Add visual feedback
                    $('#whatsapp_number').addClass('border-success').removeClass('border-warning');
                    
                    console.log('✅ Auto-populated WhatsApp number:', mobile);
                } else {
                    $('#whatsapp_number').addClass('border-warning').removeClass('border-success');
                    console.log('⚠️ No mobile number found for selected customer');
                }
            });

            // Enhanced customer clearing with feedback
            $('#customer_id').on('select2:clear', function (e) {
                $('#whatsapp_number').val('').removeClass('border-success border-warning');
                console.log('🧹 Cleared customer selection and WhatsApp number');
            });

            // ===============================================
            // Enhanced Vehicle Input Management
            // ===============================================
            
            // Smart vehicle number formatting
            $('#vehicle_number').on('input', function() {
                let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
                $(this).val(value);
                
                // Real-time validation feedback
                if (value.length > 0 && value.length < 8) {
                    $(this).addClass('border-warning').removeClass('border-success');
                } else if (value.length >= 8) {
                    $(this).addClass('border-success').removeClass('border-warning');
                } else {
                    $(this).removeClass('border-warning border-success');
                }
            });

            // ===============================================
            // Enhanced IDV Calculation System
            // ===============================================
            
            function calculateTotalIdv() {
                try {
                    const idvVehicle = parseFloat($('#idv_vehicle').val()) || 0;
                    const idvTrailer = parseFloat($('#idv_trailer').val()) || 0;
                    const idvCngLpg = parseFloat($('#idv_cng_lpg_kit').val()) || 0;
                    const idvElectrical = parseFloat($('#idv_electrical_accessories').val()) || 0;
                    const idvNonElectrical = parseFloat($('#idv_non_electrical_accessories').val()) || 0;

                    const totalIdv = idvVehicle + idvTrailer + idvCngLpg + idvElectrical + idvNonElectrical;

                    // Update the total IDV field with animation
                    const $totalField = $('#total_idv');
                    $totalField.val(totalIdv.toFixed(2));

                    // Enhanced visual feedback
                    if (totalIdv > 0) {
                        $totalField.addClass('bg-success bg-opacity-10 border-success');
                        
                        // Show calculation breakdown in console for debugging
                        console.log('💰 IDV Calculated:', {
                            vehicle: idvVehicle,
                            trailer: idvTrailer,
                            cngLpg: idvCngLpg,
                            electrical: idvElectrical,
                            nonElectrical: idvNonElectrical,
                            total: totalIdv
                        });
                    } else {
                        $totalField.removeClass('bg-success bg-opacity-10 border-success');
                    }

                    return totalIdv;
                } catch (error) {
                    console.error('❌ Error calculating IDV:', error);
                    return 0;
                }
            }

            // Add event listeners to all IDV fields for main form
            $('#idv_vehicle, #idv_trailer, #idv_cng_lpg_kit, #idv_electrical_accessories, #idv_non_electrical_accessories')
                .on('input change blur', function() {
                    console.log('IDV field changed, calculating total IDV');
                    calculateTotalIdv();
                });

            // Add event listeners to all IDV fields in quote cards
            $(document).on('input change blur', '[name*="[idv_vehicle]"], [name*="[idv_trailer]"], [name*="[idv_cng_lpg_kit]"], [name*="[idv_electrical_accessories]"], [name*="[idv_non_electrical_accessories]"], [name*="[total_idv]"]', function() {
                console.log('Quote card IDV field changed');
                const quoteCard = $(this).closest('.quote-entry');
                if (quoteCard.length > 0) {
                    calculateIdvTotal(quoteCard);
                } else {
                    console.log('Warning: Could not find quote-entry parent for IDV field');
                }
            });

            // Initial calculation for main form
            calculateTotalIdv();
            
            // Initial calculation for any existing quote cards
            $('.quote-entry').each(function() {
                calculateIdvTotal($(this));
            });

            // Convert vehicle number to uppercase
            $('#vehicle_number').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            // ===============================================
            // Enhanced Form Submission & Validation
            // ===============================================
            
            // Smart form validation with real-time feedback
            function validateForm() {
                let isValid = true;
                const errors = [];
                
                // Check required fields
                const requiredFields = {
                    'customer_id': 'Customer selection',
                    'make_model_variant': 'Vehicle make/model/variant',
                    'rto_location': 'RTO location',
                    'manufacturing_year': 'Manufacturing year',
                    'fuel_type': 'Fuel type',
                    'cubic_capacity_kw': 'Cubic capacity',
                    'seating_capacity': 'Seating capacity'
                };
                
                Object.keys(requiredFields).forEach(fieldId => {
                    const $field = $(`#${fieldId}`);
                    const value = $field.val();
                    
                    if (!value || value.trim() === '') {
                        isValid = false;
                        errors.push(requiredFields[fieldId]);
                        $field.addClass('is-invalid');
                    } else {
                        $field.removeClass('is-invalid');
                    }
                });
                
                // Check if at least one quote is added
                if ($('.quote-entry').length === 0) {
                    isValid = false;
                    errors.push('At least one insurance company quote');
                }
                
                return { isValid, errors };
            }

            // Enhanced form submission with loading states and validation
            $('#quotationForm').on('submit', function(e) {
                const validation = validateForm();
                
                if (!validation.isValid) {
                    e.preventDefault();
                    
                    // Show validation errors with modern styling
                    if (window.notificationManager) {
                        const errorMsg = `Please provide: ${validation.errors.join(', ')}`;
                        window.notificationManager.show(errorMsg, 'error', 5000);
                    }
                    
                    // Scroll to first invalid field
                    $('.is-invalid').first().focus();
                    
                    console.log('❌ Form validation failed:', validation.errors);
                    return false;
                }
                
                // Show loading state with enhanced UX
                const $submitBtn = $('#submitBtn');
                const originalText = $submitBtn.html();
                
                $submitBtn
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin me-2"></i>Creating Quotation...')
                    .addClass('btn-loading');
                
                // Show progress notification
                if (window.notificationManager) {
                    window.notificationManager.show('Processing quotation data...', 'info', 3000);
                }
                
                console.log('✅ Submitting quotation form with validation passed');
                
                // Restore button state if form submission fails (after 10 seconds timeout)
                setTimeout(() => {
                    $submitBtn
                        .prop('disabled', false)
                        .html(originalText)
                        .removeClass('btn-loading');
                }, 10000);
            });

            // Real-time validation feedback for required fields
            $('input[required], select[required]').on('blur change', function() {
                const $field = $(this);
                const value = $field.val();
                
                if (!value || value.trim() === '') {
                    $field.addClass('is-invalid');
                } else {
                    $field.removeClass('is-invalid');
                }
            });

            // Auto-populate WhatsApp number from customer selection
            $('#customer_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const mobileNumber = selectedOption.data('mobile');

                if (mobileNumber) {
                    $('#whatsapp_number').val(mobileNumber);
                } else {
                    $('#whatsapp_number').val('');
                }
            });

            // Manual Quote Entry System
            let quoteIndex = {{ old('companies') ? count(old('companies')) : 0 }};

            // Initialize premium calculations for server-rendered quotes
            @if (old('companies'))
                // Trigger premium calculations for existing server-rendered quotes
                setTimeout(function() {
                    $('.quote-entry').each(function() {
                        const quoteCard = $(this);
                        if (quoteCard.find('.premium-field').filter(function() {
                                return $(this).val() > 0;
                            }).length > 0) {
                            calculateQuotePremium(quoteCard);
                        }
                    });
                }, 100); // Small delay to ensure DOM is ready
            @endif


            function addQuoteForm(existingData = {}, existingIndex = null) {
                const currentIndex = existingIndex !== null ? existingIndex : quoteIndex;

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

                        // Set the selected insurance company if restoring data
                        if (existingData.insurance_company_id) {
                            $(`[data-index="${currentIndex}"] .company-select`).val(existingData.insurance_company_id);
                        }

                        // Populate existing form data if provided
                        if (Object.keys(existingData).length > 0) {
                            const quoteCard = $(`.quote-entry[data-index="${currentIndex}"]`);
                            
                            // Populate all form fields
                            Object.keys(existingData).forEach(function(key) {
                                const value = existingData[key];
                                const input = quoteCard.find(`[name="companies[${currentIndex}][${key}]"]`);
                                
                                if (input.is('select')) {
                                    input.val(value);
                                } else if (input.is(':checkbox')) {
                                    input.prop('checked', !!value);
                                } else {
                                    input.val(value || '');
                                }
                            });

                            // Trigger premium calculation if data exists
                            if (existingData.basic_od_premium || existingData.tp_premium || existingData.total_addon_premium || existingData.cng_lpg_premium) {
                                calculateQuotePremium(quoteCard);
                            }
                        }

                        // Only increment quoteIndex when adding new forms (not restoring)
                        if (existingIndex === null) {
                            quoteIndex++;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading quote form:', error);
                        alert('Error loading quote form. Please try again.');
                    }
                });
            }

            // Remove quote functionality
            $(document).on('click', '.removeQuoteBtn', function() {
                $(this).closest('.quote-entry').remove();
                if ($('.quote-entry').length === 0) {
                    $('#noQuotesMessage').show();
                }
            });

            // Auto-calculate IDV total for each company (multiple events to ensure it triggers)
            $(document).on('input change keyup blur', '.idv-field', function() {
                const quoteCard = $(this).closest('.quote-entry');
                if (quoteCard.length > 0) {
                    console.log('IDV field changed, calculating total...'); // Debug log
                    calculateIdvTotal(quoteCard);
                } else {
                    console.log('Warning: Could not find quote-entry parent for IDV field'); // Debug log
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
                console.log('calculateIdvTotal called for quote card:', quoteCard); // Debug log
                
                // Calculate total of all IDV fields for each company
                let idvTotal = 0;
                
                const idvVehicle = parseFloat(quoteCard.find('[name*="[idv_vehicle]"]').val()) || 0;
                const idvTrailer = parseFloat(quoteCard.find('[name*="[idv_trailer]"]').val()) || 0;
                const idvCngLpg = parseFloat(quoteCard.find('[name*="[idv_cng_lpg_kit]"]').val()) || 0;
                const idvElectrical = parseFloat(quoteCard.find('[name*="[idv_electrical_accessories]"]').val()) || 0;
                const idvNonElectrical = parseFloat(quoteCard.find('[name*="[idv_non_electrical_accessories]"]').val()) || 0;
                
                console.log('IDV Values:', {idvVehicle, idvTrailer, idvCngLpg, idvElectrical, idvNonElectrical}); // Debug log
                
                idvTotal = idvVehicle + idvTrailer + idvCngLpg + idvElectrical + idvNonElectrical;
                
                console.log('Calculated IDV Total:', idvTotal); // Debug log
                
                // Update the total IDV field
                const totalIdvField = quoteCard.find('.total-idv');
                console.log('Total IDV field found:', totalIdvField.length); // Debug log
                
                totalIdvField.val(idvTotal.toFixed(2));
                
                // Add visual highlight
                if (idvTotal > 0) {
                    totalIdvField.css('background-color', '#d1ecf1');
                } else {
                    totalIdvField.css('background-color', '');
                }
                
                console.log('IDV total updated to:', idvTotal.toFixed(2)); // Debug log
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

            // Initialize on page load (handles server-side rendered forms)
            // Use setTimeout to ensure DOM is fully loaded and server-side values are properly set
            setTimeout(function() {
                // Initialize addon field visibility on page load
                initializeAddonVisibility();
                
                // Calculate IDV for all existing quote cards on page load
                $('.quote-entry').each(function() {
                    console.log('Initializing IDV calculation for quote card on page load');
                    calculateIdvTotal($(this));
                });
            }, 100);

            // Enhanced Add Quote button with better UX feedback
            $('#addQuoteBtn').off('click').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                
                // Show loading state
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
                
                addQuoteForm();

                // Apply addon synchronization after the form is added
                setTimeout(function() {
                    initializeAddonVisibility();
                    
                    // Calculate IDV for the newly added quote card
                    const newQuoteCard = $('.quote-entry').last();
                    console.log('🔧 Initializing IDV calculation for newly added quote card');
                    calculateIdvTotal(newQuoteCard);
                    
                    // Restore button state
                    $btn.prop('disabled', false).html(originalText);
                    
                    // Show success notification
                    if (window.notificationManager) {
                        window.notificationManager.show('New quote form added successfully', 'success', 2000);
                    }
                }, 50);
            });

            // ===============================================
            // Initialization Complete
            // ===============================================
            
            console.log('✅ Enhanced Quotations Create Form initialized successfully');
            console.log('🎯 Features active: Smart validation, Auto-population, Real-time calculations, Enhanced UX');
            
            // Show welcome notification
            setTimeout(() => {
                if (window.notificationManager) {
                    window.notificationManager.show('Quotation form ready! Start by selecting a customer.', 'info', 3000);
                }
            }, 500);

        });
    </script>
@endsection
