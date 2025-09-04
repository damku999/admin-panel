@extends('layouts.app')

@section('title', 'Edit Insurance Quotation')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit"></i> Edit Insurance Quotation
            </h1>
            <div class="d-flex">
                @if($quotation->quotationCompanies->count() > 0)
                    @can('quotation-download-pdf')
                        <a href="{{ route('quotations.download-pdf', $quotation) }}" 
                           class="btn btn-sm btn-primary shadow-sm mr-2">
                            <i class="fas fa-download fa-sm text-white-50"></i> Download PDF
                        </a>
                    @endcan

                    @can('quotation-send-whatsapp')
                        @if($quotation->status === 'Sent')
                            <button type="button" class="btn btn-sm btn-warning shadow-sm mr-2" 
                                    data-toggle="modal" data-target="#resendWhatsAppModal">
                                <i class="fab fa-whatsapp fa-sm text-white-50"></i> Resend via WhatsApp
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-success shadow-sm mr-2" 
                                    data-toggle="modal" data-target="#sendWhatsAppModal">
                                <i class="fab fa-whatsapp fa-sm text-white-50"></i> Send via WhatsApp
                            </button>
                        @endif
                    @endcan
                @endif
                
                <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-sm btn-info shadow-sm mr-2">
                    <i class="fas fa-eye fa-sm text-white-50"></i> View Quotation
                </a>
                <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Quotation Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Edit Quotation: {{ $quotation->getQuoteReference() }}
                </h6>
                <div class="d-flex">
                    <span
                        class="badge badge-{{ $quotation->status == 'Draft' ? 'secondary' : ($quotation->status == 'Generated' ? 'info' : ($quotation->status == 'Sent' ? 'warning' : ($quotation->status == 'Accepted' ? 'success' : 'danger'))) }} mr-2">
                        {{ $quotation->status }}
                    </span>
                    <span class="badge badge-info">{{ $quotation->quotationCompanies->count() }} Companies</span>
                </div>
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
                                                        min="0" max="50" step="0.01" placeholder="0">
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="policy_tenure_years"><span class="text-danger">*</span> Policy
                                                    Tenure</label>
                                                <select name="policy_tenure_years" id="policy_tenure_years"
                                                    class="form-control @error('policy_tenure_years') is-invalid @enderror"
                                                    required>
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
                                                        {{ (old('policy_type') ?? $quotation->policy_type) == 'Comprehensive' ? 'selected' : '' }}>
                                                        Comprehensive</option>
                                                    <option value="Own Damage"
                                                        {{ (old('policy_type') ?? $quotation->policy_type) == 'Own Damage' ? 'selected' : '' }}>
                                                        Own
                                                        Damage</option>
                                                    <option value="Third Party"
                                                        {{ (old('policy_type') ?? $quotation->policy_type) == 'Third Party' ? 'selected' : '' }}>
                                                        Third
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
                                                    <span class="text-danger">*</span> IDV Vehicle (Rs.)
                                                </label>
                                                <input type="number" name="idv_vehicle" id="idv_vehicle" step="0.01"
                                                    class="form-control @error('idv_vehicle') is-invalid @enderror"
                                                    placeholder="e.g., 500000"
                                                    value="{{ old('idv_vehicle') ?? $quotation->idv_vehicle }}" required>
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
                                                    IDV Trailer (Rs.)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of trailer attached to your vehicle (if any). Usually applies to commercial vehicles, trucks, or vehicles with detachable trailers."></i>
                                                </label>
                                                <input type="number" name="idv_trailer" id="idv_trailer" step="0.01"
                                                    class="form-control @error('idv_trailer') is-invalid @enderror"
                                                    placeholder="0"
                                                    value="{{ old('idv_trailer') ?? $quotation->idv_trailer }}">
                                                @error('idv_trailer')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="idv_cng_lpg_kit">
                                                    IDV CNG/LPG Kit (Rs.)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of CNG or LPG conversion kit installed in your vehicle. This is separate coverage for the fuel conversion system."></i>
                                                </label>
                                                <input type="number" name="idv_cng_lpg_kit" id="idv_cng_lpg_kit"
                                                    step="0.01"
                                                    class="form-control @error('idv_cng_lpg_kit') is-invalid @enderror"
                                                    placeholder="0"
                                                    value="{{ old('idv_cng_lpg_kit') ?? $quotation->idv_cng_lpg_kit }}">
                                                @error('idv_cng_lpg_kit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="idv_electrical_accessories">
                                                    IDV Electrical Acc. (Rs.)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of electrical accessories like music system, GPS, LED lights, etc. that are not part of standard vehicle specifications."></i>
                                                </label>
                                                <input type="number" name="idv_electrical_accessories"
                                                    id="idv_electrical_accessories" step="0.01"
                                                    class="form-control @error('idv_electrical_accessories') is-invalid @enderror"
                                                    placeholder="0"
                                                    value="{{ old('idv_electrical_accessories') ?? $quotation->idv_electrical_accessories }}">
                                                @error('idv_electrical_accessories')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="idv_non_electrical_accessories">
                                                    IDV Non-Elec. Acc. (Rs.)
                                                    <i class="fas fa-question-circle text-muted ml-1"
                                                        data-toggle="tooltip"
                                                        title="Value of non-electrical accessories like seat covers, floor mats, roof carriers, etc. that are not part of standard vehicle."></i>
                                                </label>
                                                <input type="number" name="idv_non_electrical_accessories"
                                                    id="idv_non_electrical_accessories" step="0.01"
                                                    class="form-control @error('idv_non_electrical_accessories') is-invalid @enderror"
                                                    placeholder="0"
                                                    value="{{ old('idv_non_electrical_accessories') ?? $quotation->idv_non_electrical_accessories }}">
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
                                                    <strong>Total IDV (Rs.)</strong>
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
                                                        readonly value="{{ old('total_idv') ?? $quotation->total_idv }}">
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
                                                    {{ in_array('Zero Depreciation', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="zero_dep">Zero Depreciation</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Engine Protection"
                                                    class="form-check-input addon-checkbox" id="engine_protection"
                                                    data-addon="engine_protection"
                                                    {{ in_array('Engine Protection', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="engine_protection">Engine
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Road Side Assistance"
                                                    class="form-check-input addon-checkbox" id="rsa"
                                                    data-addon="road_side_assistance"
                                                    {{ in_array('Road Side Assistance', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rsa">Road Side
                                                    Assistance</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="NCB Protection"
                                                    class="form-check-input addon-checkbox" id="ncb_protection"
                                                    data-addon="ncb_protection"
                                                    {{ in_array('NCB Protection', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ncb_protection">NCB
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Invoice Protection"
                                                    class="form-check-input addon-checkbox" id="invoice_protection"
                                                    data-addon="invoice_protection"
                                                    {{ in_array('Invoice Protection', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="invoice_protection">Invoice
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Key Replacement"
                                                    class="form-check-input addon-checkbox" id="key_replacement"
                                                    data-addon="key_replacement"
                                                    {{ in_array('Key Replacement', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="key_replacement">Key
                                                    Replacement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Personal Accident"
                                                    class="form-check-input addon-checkbox" id="personal_accident"
                                                    data-addon="personal_accident"
                                                    {{ in_array('Personal Accident', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="personal_accident">Personal
                                                    Accident</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Tyre Protection"
                                                    class="form-check-input addon-checkbox" id="tyre_protection"
                                                    data-addon="tyre_protection"
                                                    {{ in_array('Tyre Protection', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tyre_protection">Tyre
                                                    Protection</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="addon_covers[]" value="Consumables"
                                                    class="form-check-input addon-checkbox" id="consumables"
                                                    data-addon="consumables"
                                                    {{ in_array('Consumables', old('addon_covers', $quotation->addon_covers ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="consumables">Consumables</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Company Quotes -->
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
                                            add new ones. Changes will be saved when you update the quotation.</small>
                                    </div>

                                    <div id="quotesContainer">
                                        @php
                                            $quoteIndex = 0;
                                        @endphp

                                        <!-- Existing company quotes -->
                                        @foreach ($quotation->quotationCompanies as $company)
                                            <div class="card border-left-info mb-3 quote-entry existing-quote"
                                                data-index="{{ $quoteIndex }}" data-company-id="{{ $company->id }}">
                                                <div
                                                    class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                    <h6 class="m-0">
                                                        <i class="fas fa-quote-left"></i> Quote #{{ $quoteIndex + 1 }}
                                                        <span class="badge badge-info ml-2">Existing</span>
                                                    </h6>
                                                    <button type="button" class="btn btn-sm btn-danger removeQuoteBtn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Hidden field to track existing company -->
                                                    <input type="hidden" name="companies[{{ $quoteIndex }}][id]"
                                                        value="{{ $company->id }}">

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Insurance Company <span
                                                                        class="text-danger">*</span></label>
                                                                <select
                                                                    name="companies[{{ $quoteIndex }}][insurance_company_id]"
                                                                    class="form-control company-select" required>
                                                                    <option value="">Select Company</option>
                                                                    @foreach ($insuranceCompanies as $insuranceCompany)
                                                                        <option value="{{ $insuranceCompany->id }}"
                                                                            {{ $company->insurance_company_id == $insuranceCompany->id ? 'selected' : '' }}>
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
                                                                    name="companies[{{ $quoteIndex }}][plan_name]"
                                                                    class="form-control"
                                                                    placeholder="e.g., Comprehensive Plus"
                                                                    value="{{ $company->plan_name }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Quote Number</label>
                                                                <input type="text"
                                                                    name="companies[{{ $quoteIndex }}][quote_number]"
                                                                    class="form-control"
                                                                    placeholder="Company quote reference number"
                                                                    value="{{ $company->quote_number }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Basic OD Premium (Rs.) <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="number"
                                                                    name="companies[{{ $quoteIndex }}][basic_od_premium]"
                                                                    class="form-control premium-field" step="0.01"
                                                                    required value="{{ $company->basic_od_premium }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>TP Premium (Rs.) <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="number"
                                                                    name="companies[{{ $quoteIndex }}][tp_premium]"
                                                                    class="form-control premium-field" step="0.01"
                                                                    required value="{{ $company->tp_premium ?? 0 }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Add-on breakdown section -->
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card border-left-success mb-3">
                                                                <div class="card-header bg-success text-white py-1">
                                                                    <h6 class="m-0 small"><i
                                                                            class="fas fa-plus-circle"></i> Add-on Covers
                                                                        Breakdown</h6>
                                                                </div>
                                                                <div class="card-body p-2">
                                                                    @php
                                                                        $addonBreakdown =
                                                                            $company->addon_covers_breakdown ?? [];
                                                                    @endphp
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            @php
                                                                                $addonMappings = [
                                                                                    'zero_dep' => 'Zero Depreciation',
                                                                                    'engine_protection' =>
                                                                                        'Engine Protection',
                                                                                    'road_side_assistance' =>
                                                                                        'Road Side Assistance',
                                                                                ];
                                                                            @endphp
                                                                            @foreach ($addonMappings as $key => $label)
                                                                                @php
                                                                                    $addonData = $addonBreakdown[
                                                                                        $label
                                                                                    ] ?? ['price' => 0, 'note' => ''];
                                                                                    $price = is_array($addonData)
                                                                                        ? $addonData['price'] ?? 0
                                                                                        : $addonData;
                                                                                    $note = is_array($addonData)
                                                                                        ? $addonData['note'] ?? ''
                                                                                        : '';
                                                                                @endphp
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="{{ $key }}"
                                                                                    style="{{ in_array($label, $quotation->addon_covers ?? []) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label
                                                                                        class="small">{{ $label }}
                                                                                        (Rs.)
                                                                                    </label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $quoteIndex }}][addon_{{ $key }}]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ $price }}"
                                                                                        placeholder="0">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $quoteIndex }}][addon_{{ $key }}_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        value="{{ $note }}"
                                                                                        placeholder="Add coverage details, limits etc.">
                                                                                    <small class="text-muted">Coverage
                                                                                        details, limits etc. (Max 100
                                                                                        chars)</small>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            @php
                                                                                $addonMappings = [
                                                                                    'ncb_protection' =>
                                                                                        'NCB Protection',
                                                                                    'invoice_protection' =>
                                                                                        'Invoice Protection',
                                                                                    'key_replacement' =>
                                                                                        'Key Replacement',
                                                                                ];
                                                                            @endphp
                                                                            @foreach ($addonMappings as $key => $label)
                                                                                @php
                                                                                    $addonData = $addonBreakdown[
                                                                                        $label
                                                                                    ] ?? ['price' => 0, 'note' => ''];
                                                                                    $price = is_array($addonData)
                                                                                        ? $addonData['price'] ?? 0
                                                                                        : $addonData;
                                                                                    $note = is_array($addonData)
                                                                                        ? $addonData['note'] ?? ''
                                                                                        : '';
                                                                                @endphp
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="{{ $key }}"
                                                                                    style="{{ in_array($label, $quotation->addon_covers ?? []) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label
                                                                                        class="small">{{ $label }}
                                                                                        (Rs.)
                                                                                    </label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $quoteIndex }}][addon_{{ $key }}]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ $price }}"
                                                                                        placeholder="0">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $quoteIndex }}][addon_{{ $key }}_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        value="{{ $note }}"
                                                                                        placeholder="Add coverage details, limits etc.">
                                                                                    <small class="text-muted">Coverage
                                                                                        details, limits etc. (Max 100
                                                                                        chars)</small>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            @php
                                                                                $addonMappings = [
                                                                                    'personal_accident' =>
                                                                                        'Personal Accident',
                                                                                    'tyre_protection' =>
                                                                                        'Tyre Protection',
                                                                                    'consumables' => 'Consumables',
                                                                                ];
                                                                            @endphp
                                                                            @foreach ($addonMappings as $key => $label)
                                                                                @php
                                                                                    $addonData = $addonBreakdown[
                                                                                        $label
                                                                                    ] ?? ['price' => 0, 'note' => ''];
                                                                                    $price = is_array($addonData)
                                                                                        ? $addonData['price'] ?? 0
                                                                                        : $addonData;
                                                                                    $note = is_array($addonData)
                                                                                        ? $addonData['note'] ?? ''
                                                                                        : '';
                                                                                @endphp
                                                                                <div class="form-group mb-2 addon-field-container"
                                                                                    data-addon="{{ $key }}"
                                                                                    style="{{ in_array($label, $quotation->addon_covers ?? []) ? 'display: block;' : 'display: none;' }}">
                                                                                    <label
                                                                                        class="small">{{ $label }}
                                                                                        (Rs.)
                                                                                    </label>
                                                                                    <input type="number"
                                                                                        name="companies[{{ $quoteIndex }}][addon_{{ $key }}]"
                                                                                        class="form-control form-control-sm addon-field"
                                                                                        step="0.01"
                                                                                        value="{{ $price }}"
                                                                                        placeholder="0">
                                                                                    <input type="text"
                                                                                        name="companies[{{ $quoteIndex }}][addon_{{ $key }}_note]"
                                                                                        class="form-control form-control-sm mt-1 addon-note"
                                                                                        maxlength="100"
                                                                                        value="{{ $note }}"
                                                                                        placeholder="Add coverage details, limits etc.">
                                                                                    <small class="text-muted">Coverage
                                                                                        details, limits etc. (Max 100
                                                                                        chars)</small>
                                                                                </div>
                                                                            @endforeach
                                                                            <div class="form-group mb-2">
                                                                                <label class="small">Others (Rs.)</label>
                                                                                @php
                                                                                    $othersData =
                                                                                        $addonBreakdown['Others'] ?? 0;
                                                                                    $othersPrice = is_array($othersData)
                                                                                        ? $othersData['price'] ?? 0
                                                                                        : $othersData;
                                                                                @endphp
                                                                                <input type="number"
                                                                                    name="companies[{{ $quoteIndex }}][addon_others]"
                                                                                    class="form-control form-control-sm addon-field"
                                                                                    step="0.01"
                                                                                    value="{{ $othersPrice }}"
                                                                                    placeholder="Additional covers">
                                                                                <small class="text-muted">Other addon
                                                                                    covers</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <hr class="my-2">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group mb-0">
                                                                                <label
                                                                                    class="font-weight-bold text-success">Total
                                                                                    Add-on Premium (Rs.)</label>
                                                                                <input type="number"
                                                                                    name="companies[{{ $quoteIndex }}][total_addon_premium]"
                                                                                    class="form-control form-control-sm total-addon-premium font-weight-bold"
                                                                                    step="0.01" readonly
                                                                                    style="background: #d1ecf1;"
                                                                                    value="{{ $company->total_addon_premium }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>CNG/LPG Premium (Rs.)</label>
                                                                <input type="number"
                                                                    name="companies[{{ $quoteIndex }}][cng_lpg_premium]"
                                                                    class="form-control premium-field" step="0.01"
                                                                    value="{{ $company->cng_lpg_premium }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Net Premium (Rs.)</label>
                                                                <input type="number"
                                                                    name="companies[{{ $quoteIndex }}][net_premium]"
                                                                    class="form-control net-premium" step="0.01"
                                                                    readonly value="{{ $company->net_premium }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>GST Amount (Rs.)</label>
                                                                <input type="number"
                                                                    name="companies[{{ $quoteIndex }}][gst_amount]"
                                                                    class="form-control gst-amount" step="0.01"
                                                                    readonly
                                                                    value="{{ $company->sgst_amount + $company->cgst_amount }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label><strong>Final Premium (Rs.)</strong></label>
                                                                <input type="number"
                                                                    name="companies[{{ $quoteIndex }}][final_premium]"
                                                                    class="form-control final-premium font-weight-bold"
                                                                    step="0.01" readonly style="background: #d4edda;"
                                                                    value="{{ $company->final_premium }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                    name="companies[{{ $quoteIndex }}][is_recommended]"
                                                                    value="1" class="form-check-input"
                                                                    {{ $company->is_recommended ? 'checked' : '' }}>
                                                                <label class="form-check-label">Mark as Recommended</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Hidden fields for backend processing -->
                                                    <input type="hidden"
                                                        name="companies[{{ $quoteIndex }}][sgst_amount]"
                                                        value="{{ $company->sgst_amount }}">
                                                    <input type="hidden"
                                                        name="companies[{{ $quoteIndex }}][cgst_amount]"
                                                        value="{{ $company->cgst_amount }}">
                                                    <input type="hidden"
                                                        name="companies[{{ $quoteIndex }}][total_od_premium]"
                                                        value="{{ $company->total_od_premium }}">
                                                    <input type="hidden"
                                                        name="companies[{{ $quoteIndex }}][total_premium]"
                                                        value="{{ $company->total_premium }}">
                                                </div>
                                            </div>
                                            @php $quoteIndex++; @endphp
                                        @endforeach

                                        <!-- Server-side rendered new quotes (for validation failures) -->
                                        @if (old('companies'))
                                            @foreach (array_slice(old('companies'), $quotation->quotationCompanies->count()) as $index => $company)
                                                @php $currentIndex = $quotation->quotationCompanies->count() + $index; @endphp
                                                <div class="card border-left-info mb-3 quote-entry"
                                                    data-index="{{ $currentIndex }}">
                                                    <!-- Similar structure as create form for new quotes -->
                                                    <!-- This section handles validation failures with new quotes -->
                                                </div>
                                            @endforeach
                                        @endif
                                        <!-- Dynamic quote entries will be added here by JavaScript -->
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
                $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            });

            // Auto-populate WhatsApp number from customer selection
            $('#customer_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const mobileNumber = selectedOption.data('mobile');

                if (mobileNumber && !$('#whatsapp_number').val()) {
                    $('#whatsapp_number').val(mobileNumber);
                }
            });

            // Manual Quote Entry System
            let quoteIndex =
                {{ $quotation->quotationCompanies->count() + (old('companies') ? count(old('companies')) : 0) }};

            // Initialize premium calculations for existing quotes
            setTimeout(function() {
                $('.quote-entry').each(function() {
                    const quoteCard = $(this);
                    if (quoteCard.find('.premium-field').filter(function() {
                            return $(this).val() > 0;
                        }).length > 0) {
                        calculateQuotePremium(quoteCard);
                    }
                });
            }, 100);

            function addQuoteForm() {
                const currentIndex = quoteIndex;

                const quoteHtml = `
                <div class="card border-left-info mb-3 quote-entry" data-index="${currentIndex}">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="m-0">
                            <i class="fas fa-quote-left"></i> Quote #${currentIndex + 1}
                            <span class="badge badge-success ml-2">New</span>
                        </h6>
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
                                    <input type="text" name="companies[${currentIndex}][plan_name]" class="form-control" placeholder="e.g., Comprehensive Plus">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Quote Number</label>
                                    <input type="text" name="companies[${currentIndex}][quote_number]" class="form-control" placeholder="Company quote reference number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group">
                                    <label>Basic OD Premium (Rs.) <span class="text-danger">*</span></label>
                                    <input type="number" name="companies[${currentIndex}][basic_od_premium]" class="form-control premium-field" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TP Premium (Rs.) <span class="text-danger">*</span></label>
                                    <input type="number" name="companies[${currentIndex}][tp_premium]" class="form-control premium-field" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <!-- Add-on Covers Breakdown -->
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
                                                    <label class="small">Zero Depreciation (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_zero_dep]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 4064">
                                                    <input type="text" name="companies[${currentIndex}][addon_zero_dep_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="engine_protection" style="display: none;">
                                                    <label class="small">Engine Protection (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_engine_protection]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 1016">
                                                    <input type="text" name="companies[${currentIndex}][addon_engine_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="road_side_assistance" style="display: none;">
                                                    <label class="small">Road Side Assistance (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_rsa]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 180">
                                                    <input type="text" name="companies[${currentIndex}][addon_rsa_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-2 addon-field-container" data-addon="ncb_protection" style="display: none;">
                                                    <label class="small">NCB Protection (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_ncb_protection]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 500">
                                                    <input type="text" name="companies[${currentIndex}][addon_ncb_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="invoice_protection" style="display: none;">
                                                    <label class="small">Invoice Protection (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_invoice_protection]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 2336">
                                                    <input type="text" name="companies[${currentIndex}][addon_invoice_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="key_replacement" style="display: none;">
                                                    <label class="small">Key Replacement (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_key_replacement]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 425">
                                                    <input type="text" name="companies[${currentIndex}][addon_key_replacement_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-2 addon-field-container" data-addon="personal_accident" style="display: none;">
                                                    <label class="small">Personal Accident (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_personal_accident]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 450">
                                                    <input type="text" name="companies[${currentIndex}][addon_personal_accident_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="tyre_protection" style="display: none;">
                                                    <label class="small">Tyre Protection (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_tyre_protection]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 1828">
                                                    <input type="text" name="companies[${currentIndex}][addon_tyre_protection_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2 addon-field-container" data-addon="consumables" style="display: none;">
                                                    <label class="small">Consumables (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_consumables]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="e.g., 609">
                                                    <input type="text" name="companies[${currentIndex}][addon_consumables_note]" class="form-control form-control-sm mt-1 addon-note" maxlength="100" placeholder="Add note">
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="small">Others (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][addon_others]" class="form-control form-control-sm addon-field" step="0.01" value="0" placeholder="Additional covers">
                                                    <small class="text-muted">Other addon covers</small>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold text-success">Total Add-on Premium (Rs.)</label>
                                                    <input type="number" name="companies[${currentIndex}][total_addon_premium]" class="form-control form-control-sm total-addon-premium font-weight-bold" step="0.01" readonly style="background: #d1ecf1;" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>CNG/LPG Premium (Rs.)</label>
                                    <input type="number" name="companies[${currentIndex}][cng_lpg_premium]" class="form-control premium-field" step="0.01" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Net Premium (Rs.)</label>
                                    <input type="number" name="companies[${currentIndex}][net_premium]" class="form-control net-premium" step="0.01" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>GST Amount (Rs.)</label>
                                    <input type="number" name="companies[${currentIndex}][gst_amount]" class="form-control gst-amount" step="0.01" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Final Premium (Rs.)</strong></label>
                                    <input type="number" name="companies[${currentIndex}][final_premium]" class="form-control final-premium font-weight-bold" step="0.01" readonly style="background: #d4edda;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="companies[${currentIndex}][is_recommended]" value="1" class="form-check-input">
                                    <label class="form-check-label">Mark as Recommended</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

                $('#quotesContainer').append(quoteHtml);
                quoteIndex++;
            }

            // Remove quote functionality
            $(document).on('click', '.removeQuoteBtn', function() {
                $(this).closest('.quote-entry').remove();
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
                const tpPremium = parseFloat(quoteCard.find('[name*="[tp_premium]"]').val()) || 0;
                const addonPremium = parseFloat(quoteCard.find('[name*="[total_addon_premium]"]').val()) || 0;
                const cngLpg = parseFloat(quoteCard.find('[name*="[cng_lpg_premium]"]').val()) || 0;

                const netPremium = basicOd + tpPremium + addonPremium + cngLpg;
                const gstAmount = netPremium * 0.18; // 18% GST
                const finalPremium = netPremium + gstAmount;

                quoteCard.find('.net-premium').val(netPremium.toFixed(2));
                quoteCard.find('.gst-amount').val(gstAmount.toFixed(2));
                quoteCard.find('.final-premium').val(finalPremium.toFixed(2));

                // Update hidden fields for backend
                const index = quoteCard.data('index');
                quoteCard.find('[name*="[sgst_amount]"]').val((gstAmount / 2).toFixed(2));
                quoteCard.find('[name*="[cgst_amount]"]').val((gstAmount / 2).toFixed(2));
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
                        addonContainer.find('.addon-field, .addon-note').val('');
                    }
                });
            });

            // Initialize addon visibility on page load
            setTimeout(function() {
                initializeAddonVisibility();

                // Calculate addon totals for existing quotes
                $('.quote-entry').each(function() {
                    calculateAddonTotal($(this));
                });

                // Recalculate all premiums for existing quotes
                $('.existing-quote').each(function() {
                    calculateQuotePremium($(this));
                });
            }, 100);

            // Add Quote button click handler
            $('#addQuoteBtn').on('click', function() {
                addQuoteForm();

                // Apply addon synchronization after the form is added
                setTimeout(function() {
                    initializeAddonVisibility();
                }, 50);
            });

            // Initialize addon visibility on page load and synchronize with checkboxes
            function initializeAddonVisibility() {
                $('.addon-checkbox').each(function() {
                    const addonType = $(this).data('addon');
                    const isChecked = $(this).is(':checked');
                    const addonContainers = $(`.addon-field-container[data-addon="${addonType}"]`);

                    if (isChecked) {
                        addonContainers.show();
                    } else {
                        addonContainers.hide();
                    }
                });
            }
        });
    </script>
@endsection

<!-- Send WhatsApp Modal -->
<div class="modal fade" id="sendWhatsAppModal" tabindex="-1" role="dialog" aria-labelledby="sendWhatsAppModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="sendWhatsAppModalLabel">
                    <i class="fab fa-whatsapp"></i> Send Quotation via WhatsApp
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
