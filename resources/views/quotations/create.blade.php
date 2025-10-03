@extends('layouts.app')

@section('title', 'Create Insurance Quotation')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Quotation Create Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold text-primary">Create Insurance Quotation</h6>
                    <small class="text-muted">Step 1 of 2 - Customer Information & Vehicle Details</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-info text-white px-2 py-1">Step 1 of 2</span>
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                        <i class="fas fa-list me-2"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('quotations.store') }}" id="quotationForm">
                    @csrf

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
                                                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                                                    value="{{ old('whatsapp_number') }}">
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
                                                    style="text-transform: uppercase" value="{{ old('vehicle_number') }}">
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
                                                    value="{{ old('make_model_variant') }}" required>
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
                                                    placeholder="e.g., Ahmedabad" value="{{ old('rto_location') }}"
                                                    required>
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
                                                            {{ old('manufacturing_year') == $year ? 'selected' : '' }}>
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
                                                        {{ old('fuel_type') == 'Petrol' ? 'selected' : '' }}>Petrol
                                                    </option>
                                                    <option value="Diesel"
                                                        {{ old('fuel_type') == 'Diesel' ? 'selected' : '' }}>Diesel
                                                    </option>
                                                    <option value="CNG"
                                                        {{ old('fuel_type') == 'CNG' ? 'selected' : '' }}>CNG</option>
                                                    <option value="Electric"
                                                        {{ old('fuel_type') == 'Electric' ? 'selected' : '' }}>Electric
                                                    </option>
                                                    <option value="Hybrid"
                                                        {{ old('fuel_type') == 'Hybrid' ? 'selected' : '' }}>Hybrid
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
                                                        value="{{ old('ncb_percentage', 0) }}" min="0"
                                                        max="50" step="1" placeholder="0">
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
                                                    placeholder="e.g., 1200" value="{{ old('cubic_capacity_kw') }}"
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
                                                    placeholder="e.g., 5" value="{{ old('seating_capacity') }}" required>
                                                @error('seating_capacity')
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
                                    <button type="button" class="btn btn-sm btn-light" id="addQuoteBtn">
                                        <i class="fas fa-plus"></i> Add Quote
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <small><i class="fas fa-info-circle"></i> Add quotes manually from different
                                            insurance companies. You can add multiple quotes to compare premiums.</small>
                                    </div>

                                    <div id="quotesContainer">
                                        <!-- Server-side rendered quotes (for validation failures) -->
                                        @if (old('companies'))
                                            @foreach (old('companies') as $index => $company)
                                                <div class="card border-left-info mb-3 quote-entry"
                                                    data-index="{{ $index }}">
                                                    <div
                                                        class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                        <h6 class="m-0"><i class="fas fa-quote-left"></i> Quote
                                                            #{{ $index + 1 }}</h6>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger removeQuoteBtn">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Premium Details Section -->
                                                        <div class="card border-left-primary mb-3">
                                                            <div class="card-header bg-primary text-white py-1">
                                                                <h6 class="m-0 small"><i class="fas fa-money-bill-wave"></i> Premium Details</h6>
                                                            </div>
                                                            <div class="card-body p-3">
                                                                <!-- Row 1: Quote Number, Basic OD Premium, TP Premium -->
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">Quote Number</label>
                                                                            <input type="text" name="companies[{{ $index }}][quote_number]" class="form-control form-control-sm @error("companies.{$index}.quote_number") is-invalid @enderror" placeholder="Company quote reference number" value="{{ old("companies.{$index}.quote_number") }}">
                                                                            @error("companies.{$index}.quote_number")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">Basic OD Premium (₹) <span class="text-danger">*</span></label>
                                                                            <input type="number" name="companies[{{ $index }}][basic_od_premium]" class="form-control form-control-sm premium-field @error("companies.{$index}.basic_od_premium") is-invalid @enderror" step="1" required value="{{ old("companies.{$index}.basic_od_premium") }}">
                                                                            @error("companies.{$index}.basic_od_premium")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
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
                                                                    <div class="col-md-4">
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
                                                                    <div class="col-md-4">
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
                                                                    <div class="col-md-4">
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
                                                                    <div class="col-md-4">
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
                                                                    <div class="col-md-4">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">IDV Vehicle (₹) <span class="text-danger">*</span></label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_vehicle]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_vehicle") is-invalid @enderror" step="1" required placeholder="e.g., 500000" value="{{ old("companies.{$index}.idv_vehicle") }}">
                                                                            @error("companies.{$index}.idv_vehicle")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group mb-3">
                                                                            <label class="small font-weight-bold">IDV Trailer (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_trailer]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_trailer") is-invalid @enderror" step="1" placeholder="0" value="{{ old("companies.{$index}.idv_trailer") }}">
                                                                            @error("companies.{$index}.idv_trailer")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
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
                                                                    <div class="col-md-4">
                                                                        <div class="form-group mb-0">
                                                                            <label class="small font-weight-bold">IDV Electrical Acc. (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_electrical_accessories]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_electrical_accessories") is-invalid @enderror" step="1" placeholder="0" value="{{ old("companies.{$index}.idv_electrical_accessories") }}">
                                                                            @error("companies.{$index}.idv_electrical_accessories")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group mb-0">
                                                                            <label class="small font-weight-bold">IDV Non-Elec. Acc. (₹)</label>
                                                                            <input type="number" name="companies[{{ $index }}][idv_non_electrical_accessories]" class="form-control form-control-sm idv-field @error("companies.{$index}.idv_non_electrical_accessories") is-invalid @enderror" step="1" placeholder="0" value="{{ old("companies.{$index}.idv_non_electrical_accessories") }}">
                                                                            @error("companies.{$index}.idv_non_electrical_accessories")
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
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
                                                            <div class="col-md-12">
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
                                                                                <div class="col-md-4">
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
                                                                            <div class="col-md-12">
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
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Net Premium (₹)</label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][net_premium]"
                                                                        class="form-control net-premium" step="1"
                                                                        readonly
                                                                        value="{{ old("companies.{$index}.net_premium", 0) }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>GST Amount (₹)</label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][gst_amount]"
                                                                        class="form-control gst-amount" step="1"
                                                                        readonly
                                                                        value="{{ old("companies.{$index}.gst_amount", 0) }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
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
                                                            <div class="col-md-12">
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

                                    <div class="text-center" id="noQuotesMessage"
                                        {{ old('companies') ? 'style=display:none;' : '' }}>
                                        <p class="text-muted">No quotes added yet. Click "Add Quote" to start.</p>
                                    </div>
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
                                    placeholder="Any specific requirements or notes for this quotation...">{{ old('notes') }}</textarea>
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
                                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Create Quotation
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Form Validation for Quotation
            const validator = new FormValidator('form');
            
            // Define validation rules for quotation form
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
                
                if (mobile) {
                    $('#whatsapp_number').val(mobile);
                }
            });

            // Clear WhatsApp number when customer is cleared
            $('#customer_id').on('select2:clear', function (e) {
                $('#whatsapp_number').val('');
            });

            // Auto-calculate total IDV function
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

            // Add event listeners to all IDV fields for main form
            $('#idv_vehicle, #idv_trailer, #idv_cng_lpg_kit, #idv_electrical_accessories, #idv_non_electrical_accessories')
                .on('input change blur', function() {
                    calculateTotalIdv();
                });

            // Add event listeners to all IDV fields in quote cards
            $(document).on('input change blur', '[name*="[idv_vehicle]"], [name*="[idv_trailer]"], [name*="[idv_cng_lpg_kit]"], [name*="[idv_electrical_accessories]"], [name*="[idv_non_electrical_accessories]"], [name*="[total_idv]"]', function() {
                const quoteCard = $(this).closest('.quote-entry');
                if (quoteCard.length > 0) {
                    calculateIdvTotal(quoteCard);
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

            // Form validation enhancement
            $('#quotationForm').on('submit', function(e) {
                // Debug: Log all form data before submission
                let debugOutput = '=== FORM SUBMISSION DEBUG ===\n';
                const formData = new FormData(this);

                // Log all companies data
                let companyIndex = 0;
                while (formData.has(`companies[${companyIndex}][insurance_company_id]`)) {
                    debugOutput += `\n--- Company ${companyIndex} ---\n`;

                    // Check for addon fields
                    const addonFields = [];
                    for (let [key, value] of formData.entries()) {
                        if (key.startsWith(`companies[${companyIndex}][addon_`)) {
                            addonFields.push(`${key} = ${value}`);
                        }
                    }

                    debugOutput += 'Addon fields:\n' + (addonFields.length > 0 ? addonFields.join('\n') : 'NONE FOUND') + '\n';
                    companyIndex++;
                }
                debugOutput += '=== END FORM DEBUG ===';

                console.log(debugOutput);
                // alert(debugOutput); // Disabled - uncomment for debugging

                $('#submitBtn').prop('disabled', true);
                $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Creating...');
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
                        show_notification('error', 'Error loading quote form. Please try again.');
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
                const container = $(this).closest('.addon-field-container');
                const fieldsContainer = container.find('.addon-fields');
                const selectedFlag = container.find('.addon-selected-flag');

                if (isChecked) {
                    fieldsContainer.show();
                    selectedFlag.val('1'); // Mark addon as selected
                } else {
                    fieldsContainer.hide();
                    selectedFlag.val('0'); // Mark addon as not selected
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
                    const container = $(this).closest('.addon-field-container');
                    const fieldsContainer = container.find('.addon-fields');
                    const selectedFlag = container.find('.addon-selected-flag');

                    if (isChecked) {
                        fieldsContainer.show();
                        selectedFlag.val('1');
                    } else {
                        fieldsContainer.hide();
                        selectedFlag.val('0');
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
                    calculateIdvTotal($(this));
                });
            }, 100);

            // Enhance the Add Quote button click handler to include addon synchronization
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

        });
    </script>
@endsection
