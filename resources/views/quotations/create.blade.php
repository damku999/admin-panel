@extends('layouts.app')

@section('title', 'Create Insurance Quotation')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt"></i> Create Insurance Quotation
            </h1>
            <a href="{{ route('quotations.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Quotation Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus"></i> New Quotation Details
                </h6>
                <span class="badge badge-info">Step 1 of 2</span>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
                                                    class="form-control @error('customer_id') is-invalid @enderror"
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
                                        <div class="col-md-6">
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
                                        <div class="col-md-6">
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
                                    </div>

                                    <div class="row">
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
                                                <label for="date_of_registration"><span class="text-danger">*</span> Date
                                                    of Registration</label>
                                                <input type="date" name="date_of_registration"
                                                    id="date_of_registration"
                                                    class="form-control @error('date_of_registration') is-invalid @enderror"
                                                    value="{{ old('date_of_registration') }}" required>
                                                @error('date_of_registration')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="policy_tenure_years"><span class="text-danger">*</span> Policy
                                                    Tenure</label>
                                                <select name="policy_tenure_years" id="policy_tenure_years"
                                                    class="form-control @error('policy_tenure_years') is-invalid @enderror"
                                                    required>
                                                    <option value="1"
                                                        {{ old('policy_tenure_years') == '1' ? 'selected' : '' }}>1 Year
                                                    </option>
                                                    <option value="2"
                                                        {{ old('policy_tenure_years') == '2' ? 'selected' : '' }}>2 Years
                                                    </option>
                                                    <option value="3"
                                                        {{ old('policy_tenure_years') == '3' ? 'selected' : '' }}>3 Years
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

                    <!-- Insurance Details -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-success mb-4">
                                <div class="card-header bg-success text-white py-2">
                                    <h6 class="m-0"><i class="fas fa-shield-alt"></i> Insurance Coverage Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="policy_type"><span class="text-danger">*</span> Policy
                                                    Type</label>
                                                <select name="policy_type" id="policy_type"
                                                    class="form-control @error('policy_type') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Policy Type</option>
                                                    <option value="Comprehensive"
                                                        {{ old('policy_type') == 'Comprehensive' ? 'selected' : '' }}>
                                                        Comprehensive</option>
                                                    <option value="Own Damage"
                                                        {{ old('policy_type') == 'Own Damage' ? 'selected' : '' }}>Own
                                                        Damage</option>
                                                    <option value="Third Party"
                                                        {{ old('policy_type') == 'Third Party' ? 'selected' : '' }}>Third
                                                        Party</option>
                                                </select>
                                                @error('policy_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="idv_vehicle">
                                                    <span class="text-danger">*</span> IDV Vehicle (₹)
                                                </label>
                                                <input type="number" name="idv_vehicle" id="idv_vehicle" step="0.01"
                                                    class="form-control @error('idv_vehicle') is-invalid @enderror"
                                                    placeholder="e.g., 500000" value="{{ old('idv_vehicle') }}" required>
                                                @error('idv_vehicle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="idv_trailer">
                                                    IDV Trailer (₹)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of trailer attached to your vehicle (if any). Usually applies to commercial vehicles, trucks, or vehicles with detachable trailers."></i>
                                                </label>
                                                <input type="number" name="idv_trailer" id="idv_trailer" step="0.01"
                                                    class="form-control @error('idv_trailer') is-invalid @enderror"
                                                    placeholder="0" value="{{ old('idv_trailer', 0) }}">
                                                @error('idv_trailer')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="idv_cng_lpg_kit">
                                                    IDV CNG/LPG Kit (₹)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of CNG or LPG conversion kit installed in your vehicle. This is separate coverage for the fuel conversion system."></i>
                                                </label>
                                                <input type="number" name="idv_cng_lpg_kit" id="idv_cng_lpg_kit"
                                                    step="0.01"
                                                    class="form-control @error('idv_cng_lpg_kit') is-invalid @enderror"
                                                    placeholder="0" value="{{ old('idv_cng_lpg_kit', 0) }}">
                                                @error('idv_cng_lpg_kit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="idv_electrical_accessories">
                                                    IDV Electrical Acc. (₹)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of electrical accessories like music system, GPS, LED lights, etc. that are not part of standard vehicle specifications."></i>
                                                </label>
                                                <input type="number" name="idv_electrical_accessories"
                                                    id="idv_electrical_accessories" step="0.01"
                                                    class="form-control @error('idv_electrical_accessories') is-invalid @enderror"
                                                    placeholder="0" value="{{ old('idv_electrical_accessories', 0) }}">
                                                @error('idv_electrical_accessories')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="idv_non_electrical_accessories">
                                                    IDV Non-Elec. Acc. (₹)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of non-electrical accessories like seat covers, floor mats, roof carriers, etc. that are not part of standard vehicle."></i>
                                                </label>
                                                <input type="number" name="idv_non_electrical_accessories"
                                                    id="idv_non_electrical_accessories" step="0.01"
                                                    class="form-control @error('idv_non_electrical_accessories') is-invalid @enderror"
                                                    placeholder="0"
                                                    value="{{ old('idv_non_electrical_accessories', 0) }}">
                                                @error('idv_non_electrical_accessories')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="total_idv">
                                                    <strong>Total IDV (₹)</strong>
                                                    <i class="fas fa-calculator text-success ml-1" data-toggle="tooltip"
                                                        title="Automatically calculated sum of all IDV components. This is the total insured value of your vehicle."></i>
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-success text-white">
                                                            <i class="fas fa-rupee-sign"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" name="total_idv" id="total_idv" step="0.01"
                                                        class="form-control bg-light font-weight-bold text-success"
                                                        readonly value="{{ old('total_idv') }}">
                                                </div>
                                                <small class="text-muted">Auto-calculated: Vehicle + Trailer + CNG/LPG +
                                                    Electrical + Non-Electrical</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add-on Covers -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-warning mb-4">
                                <div class="card-header bg-warning text-white py-2">
                                    <h6 class="m-0"><i class="fas fa-plus-circle"></i> Add-on Covers (Optional)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Zero Depreciation"
                                                    class="form-check-input addon-checkbox" id="zero_dep"
                                                    data-addon="zero_dep"
                                                    {{ in_array('Zero Depreciation', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="zero_dep">Zero Depreciation</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Engine Protection"
                                                    class="form-check-input addon-checkbox" id="engine_protection"
                                                    data-addon="engine_protection"
                                                    {{ in_array('Engine Protection', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="engine_protection">Engine
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Road Side Assistance"
                                                    class="form-check-input addon-checkbox" id="rsa"
                                                    data-addon="road_side_assistance"
                                                    {{ in_array('Road Side Assistance', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rsa">Road Side
                                                    Assistance</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="NCB Protection"
                                                    class="form-check-input addon-checkbox" id="ncb_protection"
                                                    data-addon="ncb_protection"
                                                    {{ in_array('NCB Protection', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ncb_protection">NCB
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Invoice Protection"
                                                    class="form-check-input addon-checkbox" id="invoice_protection"
                                                    data-addon="invoice_protection"
                                                    {{ in_array('Invoice Protection', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="invoice_protection">Invoice
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Key Replacement"
                                                    class="form-check-input addon-checkbox" id="key_replacement"
                                                    data-addon="key_replacement"
                                                    {{ in_array('Key Replacement', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="key_replacement">Key
                                                    Replacement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Personal Accident"
                                                    class="form-check-input addon-checkbox" id="personal_accident"
                                                    data-addon="personal_accident"
                                                    {{ in_array('Personal Accident', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="personal_accident">Personal
                                                    Accident</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Tyre Protection"
                                                    class="form-check-input addon-checkbox" id="tyre_protection"
                                                    data-addon="tyre_protection"
                                                    {{ in_array('Tyre Protection', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tyre_protection">Tyre
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Consumables"
                                                    class="form-check-input addon-checkbox" id="consumables"
                                                    data-addon="consumables"
                                                    {{ in_array('Consumables', old('addon_covers', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="consumables">Consumables</label>
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
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Insurance Company <span
                                                                            class="text-danger">*</span></label>
                                                                    <select
                                                                        name="companies[{{ $index }}][insurance_company_id]"
                                                                        class="form-control company-select" required>
                                                                        <option value="">Select Company</option>
                                                                        @foreach ($insuranceCompanies as $insuranceCompany)
                                                                            <option value="{{ $insuranceCompany->id }}"
                                                                                {{ old("companies.{$index}.insurance_company_id") == $insuranceCompany->id ? 'selected' : '' }}>
                                                                                {{ $insuranceCompany->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Plan Name</label>
                                                                    <input type="text"
                                                                        name="companies[{{ $index }}][plan_name]"
                                                                        class="form-control"
                                                                        placeholder="e.g., Comprehensive Plus"
                                                                        value="{{ old("companies.{$index}.plan_name") }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Quote Number</label>
                                                                    <input type="text"
                                                                        name="companies[{{ $index }}][quote_number]"
                                                                        class="form-control"
                                                                        placeholder="Company quote reference number"
                                                                        value="{{ old("companies.{$index}.quote_number") }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Basic OD Premium (₹) <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][basic_od_premium]"
                                                                        class="form-control premium-field" step="0.01"
                                                                        required
                                                                        value="{{ old("companies.{$index}.basic_od_premium") }}">
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
                                                                            <div class="col-md-4">
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="zero_dep"
                                                                                    style="{{ in_array('Zero Depreciation', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Zero Depreciation (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_zero_dep]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_zero_dep", 0) }}"
                                                                                        placeholder="e.g., 4064">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_zero_dep_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Depreciation Reimbursement - Count of Claim 2)"
                                                                                        value="{{ old("companies.{$index}.addon_zero_dep_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="engine_protection"
                                                                                    style="{{ in_array('Engine Protection', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Engine Protection (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_engine_protection]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_engine_protection", 0) }}"
                                                                                        placeholder="e.g., 1016">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_engine_protection_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Engine Secure TA 16 - Protects against engine damage)"
                                                                                        value="{{ old("companies.{$index}.addon_engine_protection_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="road_side_assistance"
                                                                                    style="{{ in_array('Road Side Assistance', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Road Side Assistance (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_rsa]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_rsa", 0) }}"
                                                                                        placeholder="e.g., 180">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_rsa_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Emergency transport and hotel expenses - Any One Accident: 5000)"
                                                                                        value="{{ old("companies.{$index}.addon_rsa_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="ncb_protection"
                                                                                    style="{{ in_array('NCB Protection', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">NCB Protection (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_ncb_protection]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_ncb_protection", 0) }}"
                                                                                        placeholder="e.g., 500">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_ncb_protection_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Protects No Claim Bonus in case of claim)"
                                                                                        value="{{ old("companies.{$index}.addon_ncb_protection_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="invoice_protection"
                                                                                    style="{{ in_array('Invoice Protection', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Invoice Protection (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_invoice_protection]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_invoice_protection", 0) }}"
                                                                                        placeholder="e.g., 2336">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_invoice_protection_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Return to invoice TA 05 - Covers full invoice value)"
                                                                                        value="{{ old("companies.{$index}.addon_invoice_protection_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="key_replacement"
                                                                                    style="{{ in_array('Key Replacement', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Key Replacement (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_key_replacement]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_key_replacement", 0) }}"
                                                                                        placeholder="e.g., 425">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_key_replacement_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Key Replacement TA 15 - SI: ₹25,000 per occurrence limit 50% of SI)"
                                                                                        value="{{ old("companies.{$index}.addon_key_replacement_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="personal_accident"
                                                                                    style="{{ in_array('Personal Accident', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Personal Accident (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_personal_accident]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_personal_accident", 0) }}"
                                                                                        placeholder="e.g., 450">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_personal_accident_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Emergency Medical Expenses TA 22 - Sum Insured: ₹25,000)"
                                                                                        value="{{ old("companies.{$index}.addon_personal_accident_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="tyre_protection"
                                                                                    style="{{ in_array('Tyre Protection', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Tyre Protection (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_tyre_protection]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_tyre_protection", 0) }}"
                                                                                        placeholder="e.g., 1828">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_tyre_protection_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Tyre Secure TA 17 - Covers tyre and rim damage)"
                                                                                        value="{{ old("companies.{$index}.addon_tyre_protection_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="consumables"
                                                                                    style="{{ in_array('Consumables', old('addon_covers', [])) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label class="small">Consumables (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_consumables]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_consumables", 0) }}"
                                                                                        placeholder="e.g., 609">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $index }}][addon_consumables_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        placeholder="Add note (e.g., Consumables Expenses TA 18 - Nuts, bolts, oils, lubricants etc.)"
                                                                                        value="{{ old("companies.{$index}.addon_consumables_note") }}">
                                                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                                                </div>
                                                                                <div class="form-group mb-2">
                                                                                    <label class="small">Others (₹)</label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $index }}][addon_others]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ old("companies.{$index}.addon_others", 0) }}"
                                                                                        placeholder="Additional covers">
                                                                                    <small class="text-muted">Other addon covers</small>
                                                                                </div>
                                                                            </div>
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
                                                                                        class="form-control form-control-sm total-addon-premium font-weight-bold"
                                                                                        step="0.01" readonly
                                                                                        style="background: #d1ecf1;"
                                                                                        value="{{ old("companies.{$index}.total_addon_premium", 0) }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>CNG/LPG Premium (₹)</label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][cng_lpg_premium]"
                                                                        class="form-control premium-field" step="0.01"
                                                                        value="{{ old("companies.{$index}.cng_lpg_premium", 0) }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Net Premium (₹)</label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][net_premium]"
                                                                        class="form-control net-premium" step="0.01"
                                                                        readonly
                                                                        value="{{ old("companies.{$index}.net_premium", 0) }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>GST Amount (₹)</label>
                                                                    <input type="number"
                                                                        name="companies[{{ $index }}][gst_amount]"
                                                                        class="form-control gst-amount" step="0.01"
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
                                                                        step="0.01" readonly
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
                                                                        value="1" class="form-check-input"
                                                                        {{ old("companies.{$index}.is_recommended") ? 'checked' : '' }}>
                                                                    <label class="form-check-label">Mark as
                                                                        Recommended</label>
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
            // Initialize Bootstrap tooltips
            $('[data-toggle="tooltip"]').tooltip();

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

            // Add event listeners to all IDV fields
            $('#idv_vehicle, #idv_trailer, #idv_cng_lpg_kit, #idv_electrical_accessories, #idv_non_electrical_accessories')
                .on('input change blur', function() {
                    calculateTotalIdv();
                });

            // Initial calculation
            calculateTotalIdv();

            // Convert vehicle number to uppercase
            $('#vehicle_number').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Form validation enhancement
            $('#quotationForm').on('submit', function() {
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

                const quoteHtml = `
                <div class="card border-left-info mb-3 quote-entry" data-index="${currentIndex}">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="m-0"><i class="fas fa-quote-left"></i> Quote #${currentIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger removeQuoteBtn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Insurance Company <span class="text-danger">*</span></label>
                                    <select name="companies[${currentIndex}][insurance_company_id]" class="form-control company-select" required>
                                        <option value="">Select Company</option>
                                        @foreach ($insuranceCompanies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Plan Name</label>
                                    <input type="text" name="companies[${currentIndex}][plan_name]" class="form-control" placeholder="e.g., Comprehensive Plus" value="${existingData.plan_name || ''}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Quote Number</label>
                                    <input type="text" name="companies[${currentIndex}][quote_number]" class="form-control" placeholder="Company quote reference number" value="${existingData.quote_number || ''}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group">
                                    <label>Basic OD Premium (₹) <span class="text-danger">*</span></label>
                                    <input type="number" name="companies[${currentIndex}][basic_od_premium]" class="form-control premium-field" step="0.01" required value="${existingData.basic_od_premium || ''}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border-left-success mb-3">
                                    <div class="card-header bg-success text-white py-1">
                                        <h6 class="m-0 small"><i class="fas fa-plus-circle"></i> Add-on Covers Breakdown</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-2 addon-field-container" data-addon="zero_dep" style="display: none;">
                                                    <label class="small">Zero Depreciation (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_zero_dep]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_zero_dep || 0}" placeholder="e.g., 4064">
                                                    <input type="text" name="companies[${currentIndex}][addon_zero_dep_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Depreciation Reimbursement - Count of Claim 2)" value="${existingData.addon_zero_dep_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="engine_protection" style="display: none;">
                                                    <label class="small">Engine Protection (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_engine_protection]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_engine_protection || 0}" placeholder="e.g., 1016">
                                                    <input type="text" name="companies[${currentIndex}][addon_engine_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Engine Secure TA 16 - Protects against engine damage)" value="${existingData.addon_engine_protection_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="road_side_assistance" style="display: none;">
                                                    <label class="small">Road Side Assistance (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_rsa]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_rsa || 0}" placeholder="e.g., 180">
                                                    <input type="text" name="companies[${currentIndex}][addon_rsa_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Emergency transport and hotel expenses - Any One Accident: 5000)" value="${existingData.addon_rsa_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-2 addon-field-container" data-addon="ncb_protection" style="display: none;">
                                                    <label class="small">NCB Protection (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_ncb_protection]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_ncb_protection || 0}" placeholder="e.g., 500">
                                                    <input type="text" name="companies[${currentIndex}][addon_ncb_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Protects No Claim Bonus in case of claim)" value="${existingData.addon_ncb_protection_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="invoice_protection" style="display: none;">
                                                    <label class="small">Invoice Protection (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_invoice_protection]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_invoice_protection || 0}" placeholder="e.g., 2336">
                                                    <input type="text" name="companies[${currentIndex}][addon_invoice_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Return to invoice TA 05 - Covers full invoice value)" value="${existingData.addon_invoice_protection_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="key_replacement" style="display: none;">
                                                    <label class="small">Key Replacement (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_key_replacement]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_key_replacement || 0}" placeholder="e.g., 425">
                                                    <input type="text" name="companies[${currentIndex}][addon_key_replacement_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Key Replacement TA 15 - SI: ₹25,000 per occurrence limit 50% of SI)" value="${existingData.addon_key_replacement_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-2 addon-field-container" data-addon="personal_accident" style="display: none;">
                                                    <label class="small">Personal Accident (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_personal_accident]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_personal_accident || 0}" placeholder="e.g., 450">
                                                    <input type="text" name="companies[${currentIndex}][addon_personal_accident_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Emergency Medical Expenses TA 22 - Sum Insured: ₹25,000)" value="${existingData.addon_personal_accident_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="tyre_protection" style="display: none;">
                                                    <label class="small">Tyre Protection (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_tyre_protection]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_tyre_protection || 0}" placeholder="e.g., 1828">
                                                    <input type="text" name="companies[${currentIndex}][addon_tyre_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Tyre Secure TA 17 - Covers tyre and rim damage)" value="${existingData.addon_tyre_protection_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="consumables" style="display: none;">
                                                    <label class="small">Consumables (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_consumables]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_consumables || 0}" placeholder="e.g., 609">
                                                    <input type="text" name="companies[${currentIndex}][addon_consumables_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note (e.g., Consumables Expenses TA 18 - Nuts, bolts, oils, lubricants etc.)" value="${existingData.addon_consumables_note || ''}">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="small">Others (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_others]" class="form-control form-control-sm addon-field" step="0.01" value="${existingData.addon_others || 0}" placeholder="Additional covers">
                                                    <small class="text-muted">Other addon covers</small>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold text-success">Total Add-on Premium (₹)</label>
                                                    <input type="number" name="companies[${currentIndex}][total_addon_premium]" class="form-control form-control-sm total-addon-premium font-weight-bold" step="0.01" readonly style="background: #d1ecf1;" value="${existingData.total_addon_premium || 0}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>CNG/LPG Premium (₹)</label>
                                    <input type="number" name="companies[${currentIndex}][cng_lpg_premium]" class="form-control premium-field" step="0.01" value="${existingData.cng_lpg_premium || 0}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Net Premium (₹)</label>
                                    <input type="number" name="companies[${currentIndex}][net_premium]" class="form-control net-premium" step="0.01" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>GST Amount (₹)</label>
                                    <input type="number" name="companies[${currentIndex}][gst_amount]" class="form-control gst-amount" step="0.01" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Final Premium (₹)</strong></label>
                                    <input type="number" name="companies[${currentIndex}][final_premium]" class="form-control final-premium font-weight-bold" step="0.01" readonly style="background: #d4edda;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="companies[${currentIndex}][is_recommended]" value="1" class="form-check-input" ${existingData.is_recommended ? 'checked' : ''}>
                                    <label class="form-check-label">Mark as Recommended</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

                $('#quotesContainer').append(quoteHtml);
                $('#noQuotesMessage').hide();

                // Set the selected insurance company if restoring data
                if (existingData.insurance_company_id) {
                    $(`[data-index="${currentIndex}"] .company-select`).val(existingData.insurance_company_id);
                }

                // Trigger premium calculation if data exists
                if (existingData.basic_od_premium || existingData.total_addon_premium || existingData
                    .cng_lpg_premium) {
                    const quoteCard = $(`.quote-entry[data-index="${currentIndex}"]`);
                    calculateQuotePremium(quoteCard);
                }

                // Only increment quoteIndex when adding new forms (not restoring)
                if (existingIndex === null) {
                    quoteIndex++;
                }
            }

            // Remove quote functionality
            $(document).on('click', '.removeQuoteBtn', function() {
                $(this).closest('.quote-entry').remove();
                if ($('.quote-entry').length === 0) {
                    $('#noQuotesMessage').show();
                }
            });

            // Auto-calculate addon total
            $(document).on('input', '.addon-field', function() {
                const quoteCard = $(this).closest('.quote-entry');
                calculateAddonTotal(quoteCard);
            });

            // Auto-calculate premium fields
            $(document).on('input', '.premium-field, .total-addon-premium', function() {
                const quoteCard = $(this).closest('.quote-entry');
                calculateQuotePremium(quoteCard);
            });

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
                const addonPremium = parseFloat(quoteCard.find('[name*="[total_addon_premium]"]').val()) || 0;
                const cngLpg = parseFloat(quoteCard.find('[name*="[cng_lpg_premium]"]').val()) || 0;

                const netPremium = basicOd + addonPremium + cngLpg;
                const gstAmount = netPremium * 0.18; // 18% GST
                const finalPremium = netPremium + gstAmount;

                quoteCard.find('.net-premium').val(netPremium.toFixed(2));
                quoteCard.find('.gst-amount').val(gstAmount.toFixed(2));
                quoteCard.find('.final-premium').val(finalPremium.toFixed(2));

                // Update hidden fields for backend
                const index = quoteCard.data('index');
                quoteCard.find('[name*="[sgst_amount]"]').remove();
                quoteCard.find('[name*="[cgst_amount]"]').remove();
                quoteCard.find('[name*="[total_od_premium]"]').remove();

                quoteCard.append(`
                    <input type="hidden" name="companies[${index}][sgst_amount]" value="${(gstAmount/2).toFixed(2)}">
                    <input type="hidden" name="companies[${index}][cgst_amount]" value="${(gstAmount/2).toFixed(2)}">
                    <input type="hidden" name="companies[${index}][total_od_premium]" value="${(basicOd + cngLpg).toFixed(2)}">
                `);
            }

            // Dynamic addon checkbox functionality
            $('.addon-checkbox').on('change', function() {
                const addonType = $(this).data('addon');
                const isChecked = $(this).is(':checked');

                // Show/hide addon fields in all company quotes
                $('.quote-entry').each(function() {
                    const addonContainer = $(this).find(
                        `.addon-field-container[data-addon="${addonType}"]`);
                    if (isChecked) {
                        addonContainer.show();
                    } else {
                        addonContainer.hide();
                        // Clear values when user unchecks (not during initialization)
                        addonContainer.find('.addon-field, .addon-note').val(0);
                        // Recalculate addon total
                        calculateAddonTotal($(this));
                    }
                });
            });

            // ✨ UNIFIED ADDON SYNCHRONIZATION SYSTEM ✨
            // This function ensures both server-side and client-side forms are properly synchronized
            function initializeAddonVisibility(shouldClearValues = false) {
                // Sync visibility for all addon fields based on checked checkboxes
                $('.addon-checkbox').each(function() {
                    const addonType = $(this).data('addon');
                    const isChecked = $(this).is(':checked');
                    const addonContainers = $(`.addon-field-container[data-addon="${addonType}"]`);

                    if (isChecked) {
                        addonContainers.show();
                    } else {
                        addonContainers.hide();
                        // Only reset values when explicitly requested (user action, not initialization)
                        if (shouldClearValues) {
                            addonContainers.find('.addon-field, .addon-note').val(0);
                        }
                    }
                });
            }

            // Initialize on page load (handles server-side rendered forms)
            // Use setTimeout to ensure DOM is fully loaded and server-side values are properly set
            setTimeout(function() {
                // First, ensure checkboxes reflect server-side state (for validation failures)
                @if (old('addon_covers'))
                    var serverAddons = @json(old('addon_covers'));
                    $('.addon-checkbox').each(function() {
                        var addonValue = $(this).val();
                        if (serverAddons.includes(addonValue)) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                @endif

                // Then initialize visibility
                initializeAddonVisibility();
            }, 100);

            // Enhance the Add Quote button click handler to include addon synchronization
            $('#addQuoteBtn').off('click').on('click', function() {
                addQuoteForm();

                // Apply addon synchronization after the form is added
                setTimeout(function() {
                    initializeAddonVisibility();
                }, 50);
            });

        });
    </script>
@endsection
