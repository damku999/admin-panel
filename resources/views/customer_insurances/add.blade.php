@extends('layouts.app')

@section('title', 'Add Customer Insurance')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Customer Insurance Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Add New Customer Insurance</h6>
                <a href="{{ route('customer_insurances.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            <form method="POST" action="{{ route('customer_insurances.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body py-3">
                    <!-- Section 1: Basic Insurance Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-shield-alt me-2"></i>Basic Insurance Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Customer</label>
                                <select name="customer_id" class="form-select form-select-sm @error('customer_id') is-invalid @enderror" id="customer_id">
                                    <option selected disabled>Select Customer</option>
                                    @foreach ($customers as $item)
                                        <option value="{{ $item->id }}" data-mobile="{{ $item->mobile_number }}"
                                            {{ old('customer_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                            @if ($item->mobile_number)
                                                - {{ $item->mobile_number }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Issue Date</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('issue_date') is-invalid @enderror"
                                    name="issue_date" placeholder="DD/MM/YYYY" value="{{ old('issue_date') ? formatDateForUi(old('issue_date')) : '' }}">
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Policy Type</label>
                                <select name="policy_type_id" class="form-select form-select-sm @error('policy_type_id') is-invalid @enderror" id="policy_type_id">
                                    <option selected disabled>Select Policy Type</option>
                                    @foreach ($policy_type as $item)
                                        <option value="{{ $item->id }}" {{ old('policy_type_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('policy_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Company & Branch Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-building me-2"></i>Company & Branch Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Branch</label>
                                <select name="branch_id" class="form-select form-select-sm @error('branch_id') is-invalid @enderror" id="branch_id">
                                    <option selected disabled>Select Branch</option>
                                    @foreach ($branches as $item)
                                        <option value="{{ $item->id }}" {{ old('branch_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Broker</label>
                                <select name="broker_id" class="form-select form-select-sm @error('broker_id') is-invalid @enderror" id="broker_id">
                                    <option selected disabled>Select Broker</option>
                                    @foreach ($brokers as $item)
                                        <option value="{{ $item->id }}" {{ old('broker_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('broker_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Relationship Manager</label>
                                <select name="relationship_manager_id" class="form-select form-select-sm @error('relationship_manager_id') is-invalid @enderror" id="relationship_manager_id">
                                    <option selected disabled>Select Relationship Manager</option>
                                    @foreach ($relationship_managers as $item)
                                        <option value="{{ $item->id }}" {{ old('relationship_manager_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('relationship_manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Insurance Company Name</label>
                                <select name="insurance_company_id" class="form-select form-select-sm @error('insurance_company_id') is-invalid @enderror" id="insurance_company_id">
                                    <option selected disabled>Select Company</option>
                                    @foreach ($insurance_companies as $item)
                                        <option value="{{ $item->id }}" {{ old('insurance_company_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('insurance_company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Premium Type</label>
                                <select name="premium_type_id" class="form-select form-select-sm @error('premium_type_id') is-invalid @enderror" 
                                    id="premium_type_id" onchange="premiumTypeChanged()">
                                    <option selected disabled>Select Premium Type</option>
                                    @foreach ($premium_types as $item)
                                        <option value="{{ $item->id }}" 
                                            data-is_vehicle="{{ $item->is_vehicle }}"
                                            data-is_life_insurance_policies="{{ $item->is_life_insurance_policies }}"
                                            {{ old('premium_type_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('premium_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Reference By</label>
                                <select name="reference_by" class="form-select form-select-sm @error('reference_by') is-invalid @enderror" id="reference_by">
                                    <option value="0">Select Reference By</option>
                                    @foreach ($reference_by_user as $item)
                                        <option value="{{ $item->id }}" {{ old('reference_by') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('reference_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Policy Details -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-file-contract me-2"></i>Policy Details</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Policy No.</label>
                                <input type="text" class="form-control form-control-sm @error('policy_no') is-invalid @enderror"
                                    name="policy_no" placeholder="Enter policy number" value="{{ old('policy_no') }}">
                                @error('policy_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Start Date</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('start_date') is-invalid @enderror"
                                    name="start_date" id="start_date" placeholder="DD/MM/YYYY" value="{{ old('start_date') ? formatDateForUi(old('start_date')) : '' }}"
                                    onchange="setExpiredDate()">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Expired Date</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('expired_date') is-invalid @enderror"
                                    name="expired_date" id="expired_date" placeholder="DD/MM/YYYY" value="{{ old('expired_date') ? formatDateForUi(old('expired_date')) : '' }}">
                                @error('expired_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mode of Payment</label>
                                <input type="text" class="form-control form-control-sm @error('mode_of_payment') is-invalid @enderror"
                                    name="mode_of_payment" placeholder="Enter mode of payment" value="{{ old('mode_of_payment') }}">
                                @error('mode_of_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Cheque No.</label>
                                <input type="text" class="form-control form-control-sm @error('cheque_no') is-invalid @enderror"
                                    name="cheque_no" placeholder="Enter cheque number" value="{{ old('cheque_no') }}">
                                @error('cheque_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Policy Document</label>
                                <input type="file" class="form-control form-control-sm @error('policy_document_path') is-invalid @enderror"
                                    name="policy_document_path" accept=".pdf,.jpg,.jpeg,.png">
                                @error('policy_document_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Vehicle Information (Premium Fields) -->
                    <div class="mb-4 premium-fields" style="display: none;">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-car me-2"></i>Vehicle Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Registration No.</label>
                                <input type="text" class="form-control form-control-sm @error('registration_no') is-invalid @enderror"
                                    name="registration_no" placeholder="Enter registration number" value="{{ old('registration_no') }}">
                                @error('registration_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Location</label>
                                <input type="text" class="form-control form-control-sm @error('rto') is-invalid @enderror"
                                    name="rto" placeholder="Enter location" value="{{ old('rto') }}">
                                @error('rto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Make & Model</label>
                                <input type="text" class="form-control form-control-sm @error('make_model') is-invalid @enderror"
                                    name="make_model" placeholder="Enter make and model" value="{{ old('make_model') }}">
                                @error('make_model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Fuel Type</label>
                                <select name="fuel_type_id" class="form-select form-select-sm @error('fuel_type_id') is-invalid @enderror" id="fuel_type_id">
                                    <option selected disabled>Select Fuel Type</option>
                                    @foreach ($fuel_type as $item)
                                        <option value="{{ $item->id }}" {{ old('fuel_type_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fuel_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> TP Expiry Date</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('tp_expiry_date') is-invalid @enderror"
                                    name="tp_expiry_date" placeholder="DD/MM/YYYY" value="{{ old('tp_expiry_date') ? formatDateForUi(old('tp_expiry_date')) : '' }}">
                                @error('tp_expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> MFG Year</label>
                                <input type="number" min="1900" max="2099" step="1" class="form-control form-control-sm @error('mfg_year') is-invalid @enderror"
                                    name="mfg_year" placeholder="Enter manufacturing year" value="{{ old('mfg_year') }}">
                                @error('mfg_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> NCB %</label>
                                <input type="number" min="-100" max="100" step="0.01" class="form-control form-control-sm @error('ncb_percentage') is-invalid @enderror"
                                    name="ncb_percentage" placeholder="Enter NCB percentage" value="{{ old('ncb_percentage') }}">
                                @error('ncb_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 cgst_sgt2">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> GVW</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('gross_vehicle_weight') is-invalid @enderror"
                                    name="gross_vehicle_weight" placeholder="Enter GVW" value="{{ old('gross_vehicle_weight') }}">
                                @error('gross_vehicle_weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <!-- Empty column for consistent 3-column layout -->
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Life Insurance Information -->
                    <div class="mb-4 life-insurance-policies-fields" style="display: none;">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-heartbeat me-2"></i>Life Insurance Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Plan Name</label>
                                <input type="text" class="form-control form-control-sm @error('plan_name') is-invalid @enderror"
                                    name="plan_name" placeholder="Enter plan name" value="{{ old('plan_name') }}">
                                @error('plan_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Premium Paying Term</label>
                                <input type="number" step="1" min="0" class="decimal-input form-control form-control-sm @error('premium_paying_term') is-invalid @enderror"
                                    name="premium_paying_term" placeholder="Enter premium paying term" value="{{ old('premium_paying_term') }}">
                                @error('premium_paying_term')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Policy Term</label>
                                <input type="number" step="1" min="0" class="decimal-input form-control form-control-sm @error('policy_term') is-invalid @enderror"
                                    name="policy_term" placeholder="Enter policy term" value="{{ old('policy_term') }}">
                                @error('policy_term')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sum Insured</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('sum_insured') is-invalid @enderror"
                                    name="sum_insured" placeholder="Enter sum insured" value="{{ old('sum_insured') }}">
                                @error('sum_insured')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Pension Amount Yearly</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('pension_amount_yearly') is-invalid @enderror"
                                    name="pension_amount_yearly" placeholder="Enter pension amount yearly" value="{{ old('pension_amount_yearly') }}">
                                @error('pension_amount_yearly')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Approx Maturity Amount</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('approx_maturity_amount') is-invalid @enderror"
                                    name="approx_maturity_amount" placeholder="Enter approx maturity amount" value="{{ old('approx_maturity_amount') }}">
                                @error('approx_maturity_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Life Insurance Payment Mode</label>
                                <select name="life_insurance_payment_mode" class="form-select form-select-sm @error('life_insurance_payment_mode') is-invalid @enderror" 
                                    id="life_insurance_payment_mode">
                                    <option selected disabled>Select Life Insurance Payment Mode</option>
                                    @foreach ($life_insurance_payment_mode as $item)
                                        <option value="{{ $item['id'] }}" data-multiply_by="{{ $item['multiply_by'] }}"
                                            {{ old('life_insurance_payment_mode') == $item['id'] ? 'selected' : '' }}>
                                            {{ $item['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('life_insurance_payment_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Maturity Date</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('maturity_date') is-invalid @enderror"
                                    name="maturity_date" placeholder="DD/MM/YYYY" value="{{ old('maturity_date') ? formatDateForUi(old('maturity_date')) : '' }}">
                                @error('maturity_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <!-- Empty column for consistent 3-column layout -->
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea class="form-control form-control-sm @error('remarks') is-invalid @enderror"
                                    name="remarks" placeholder="Enter remarks" rows="3">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <!-- Empty column for consistent layout -->
                            </div>
                        </div>
                    </div>

                    <!-- Section 6: Premium & Financial Details -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-money-bill-wave me-2"></i>Premium & Financial Details</h6>
                        <div class="row g-3">
                            <div class="col-md-4 premium-fields">
                                <label class="form-label fw-semibold">OD Premium</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('od_premium') is-invalid @enderror"
                                    name="od_premium" id="od_premium" placeholder="Enter OD premium" value="{{ old('od_premium') }}">
                                @error('od_premium')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 premium-fields">
                                <label class="form-label fw-semibold">TP Premium</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('tp_premium') is-invalid @enderror"
                                    name="tp_premium" id="tp_premium" placeholder="Enter TP premium" value="{{ old('tp_premium') }}">
                                @error('tp_premium')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 life-insurance-policies-fields">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Premium Amount</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('premium_amount') is-invalid @enderror"
                                    name="premium_amount" placeholder="Enter premium amount" value="{{ old('premium_amount') }}">
                                @error('premium_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Net Premium</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('net_premium') is-invalid @enderror"
                                    name="net_premium" id="net_premium" placeholder="Enter net premium" value="{{ old('net_premium') }}" required>
                                @error('net_premium')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> CGST 1</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('cgst1') is-invalid @enderror"
                                    name="cgst1" id="cgst1" placeholder="Enter CGST 1" value="{{ old('cgst1') }}" required>
                                @error('cgst1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> SGST 1</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('sgst1') is-invalid @enderror"
                                    name="sgst1" id="sgst1" placeholder="Enter SGST 1" value="{{ old('sgst1') }}" required>
                                @error('sgst1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4 cgst_sgt2">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> CGST 2</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('cgst2') is-invalid @enderror"
                                    name="cgst2" id="cgst2" placeholder="Enter CGST 2" value="{{ old('cgst2') }}">
                                @error('cgst2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 cgst_sgt2">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> SGST 2</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('sgst2') is-invalid @enderror"
                                    name="sgst2" id="sgst2" placeholder="Enter SGST 2" value="{{ old('sgst2') }}">
                                @error('sgst2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Final Premium With GST</label>
                                <input type="number" step="0.01" min="0" class="decimal-input form-control form-control-sm @error('final_premium_with_gst') is-invalid @enderror"
                                    name="final_premium_with_gst" id="final_premium_with_gst" placeholder="Final premium with GST" value="{{ old('final_premium_with_gst') }}" readonly required>
                                @error('final_premium_with_gst')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: Commission Details (Admin Only) -->
                    @if (auth()->user()->hasRole('Admin'))
                        <div class="mb-3">
                            <h6 class="text-muted fw-bold mb-3"><i class="fas fa-percentage me-2"></i>Commission Details</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Commission On</label>
                                    <select name="commission_on" class="form-select form-select-sm @error('commission_on') is-invalid @enderror" id="commission_on">
                                        <option value="net_premium" @if (old('commission_on') == 'net_premium') selected @endif>Net Premium</option>
                                        <option value="od_premium" @if (old('commission_on') == 'od_premium') selected @endif>OD Premium</option>
                                        <option value="tp_premium" @if (old('commission_on') == 'tp_premium') selected @endif>TP Premium</option>
                                    </select>
                                    @error('commission_on')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">My Commission Percentage</label>
                                    <input type="number" min="-100" max="100" step="0.01" class="form-control form-control-sm @error('my_commission_percentage') is-invalid @enderror"
                                        name="my_commission_percentage" id="my_commission_percentage" placeholder="Enter my commission %" value="{{ old('my_commission_percentage') }}">
                                    @error('my_commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">My Commission Amount</label>
                                    <input type="number" step="0.01" class="decimal-input form-control form-control-sm @error('my_commission_amount') is-invalid @enderror"
                                        name="my_commission_amount" id="my_commission_amount" placeholder="My commission amount" value="{{ old('my_commission_amount') }}" readonly>
                                    @error('my_commission_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transfer Commission Percentage</label>
                                    <input type="number" min="-100" max="100" step="0.01" class="form-control form-control-sm @error('transfer_commission_percentage') is-invalid @enderror"
                                        name="transfer_commission_percentage" id="transfer_commission_percentage" placeholder="Enter transfer commission %" value="{{ old('transfer_commission_percentage') }}">
                                    @error('transfer_commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transfer Commission Amount</label>
                                    <input type="number" step="0.01" class="decimal-input form-control form-control-sm @error('transfer_commission_amount') is-invalid @enderror"
                                        name="transfer_commission_amount" id="transfer_commission_amount" placeholder="Transfer commission amount" value="{{ old('transfer_commission_amount') }}" readonly>
                                    @error('transfer_commission_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"><span class="text-danger">*</span> Actual Earnings</label>
                                    <input type="number" step="0.01" class="decimal-input form-control form-control-sm @error('actual_earnings') is-invalid @enderror"
                                        name="actual_earnings" id="actual_earnings" placeholder="Actual earnings" value="{{ old('actual_earnings') }}" readonly>
                                    @error('actual_earnings')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4 reference_commission_fields">
                                    <label class="form-label fw-semibold">Reference Commission Percentage</label>
                                    <input type="number" min="-100" max="100" step="0.01" class="form-control form-control-sm @error('reference_commission_percentage') is-invalid @enderror"
                                        name="reference_commission_percentage" id="reference_commission_percentage" placeholder="Enter reference commission %" value="{{ old('reference_commission_percentage') }}">
                                    @error('reference_commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 reference_commission_fields">
                                    <label class="form-label fw-semibold">Reference Commission Amount</label>
                                    <input type="number" step="0.01" class="decimal-input form-control form-control-sm @error('reference_commission_amount') is-invalid @enderror"
                                        name="reference_commission_amount" id="reference_commission_amount" placeholder="Reference commission amount" value="{{ old('reference_commission_amount') }}" readonly>
                                    @error('reference_commission_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <!-- Empty column for consistent 3-column layout -->
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('customer_insurances.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Save Customer Insurance
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

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
            // Calculate and update commission fields
            function calculateCommission() {
                var commissionOn = $('#commission_on').val();
                var baseRate = parseFloat($('input[name=' + commissionOn + ']').val()) || 0;
                var myCommissionPercentageVal = $('#my_commission_percentage').val() || '';
                var myCommissionPercentage = parseFloat(myCommissionPercentageVal.replace(",", ".")) || 0;
                var transferCommissionPercentageVal = $('#transfer_commission_percentage').val() || '';
                var transferCommissionPercentage = parseFloat(transferCommissionPercentageVal.replace(",", ".")) || 0;

                var referenceCommissionPercentageVal = $('#reference_commission_percentage').val() || '';
                var referenceCommissionPercentage = parseFloat(referenceCommissionPercentageVal.replace(",", ".")) || 0;


                // Validate percentage values (clamp between -100 and 100)
                myCommissionPercentage = Math.max(-100, Math.min(myCommissionPercentage, 100));
                transferCommissionPercentage = Math.max(-100, Math.min(transferCommissionPercentage, 100));
                referenceCommissionPercentage = Math.max(-100, Math.min(referenceCommissionPercentage, 100));

                var myCommissionAmount = (baseRate * myCommissionPercentage) / 100;
                var transferCommissionAmount = (baseRate * transferCommissionPercentage) / 100;
                var referenceCommissionAmount = (baseRate * referenceCommissionPercentage) / 100;
                var actualEarnings = myCommissionAmount - transferCommissionAmount - referenceCommissionAmount;

                $('#my_commission_amount').val(myCommissionAmount.toFixed(2));
                $('#transfer_commission_amount').val(transferCommissionAmount.toFixed(2));
                $('#reference_commission_amount').val(referenceCommissionAmount.toFixed(2));
                $('#actual_earnings').val(actualEarnings.toFixed(2));
            }

            // Validate decimal input
            $('.decimal-input').on('input', function() {
                var value = $(this).val();
                var regex = /^\d+(\.\d{0,2})?$/;

                if (!regex.test(value)) {
                    value = value.substring(0, value.length - 1);
                    $(this).val(value);
                }
            });

            // Validate percentage fields (-100 to 100 range)
            $('input[name="ncb_percentage"], input[name="my_commission_percentage"], input[name="transfer_commission_percentage"], input[name="reference_commission_percentage"]').on('input change', function() {
                var value = parseFloat($(this).val());
                if (isNaN(value)) return;
                
                if (value > 100) {
                    $(this).val(100);
                } else if (value < -100) {
                    $(this).val(-100);
                }
            });

            // Calculate on change of commission fields
            $('#commission_on, #my_commission_percentage, #transfer_commission_percentage, #reference_commission_percentage, #net_premium, #od_premium, #tp_premium')
                .on('change input',
                    function() {
                        calculateCommission();
                    });

            // Initial calculation on page load
            calculateCommission();

            // Client-side form validation
            $('form').on('submit', function(e) {
                let isValid = true;
                let firstErrorField = null;

                // Clear previous validation messages
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                // Validate required fields
                const requiredFields = [
                    { field: 'customer_id', name: 'Customer' },
                    { field: 'issue_date', name: 'Issue Date' },
                    { field: 'policy_type_id', name: 'Policy Type' },
                    { field: 'branch_id', name: 'Branch' },
                    { field: 'broker_id', name: 'Broker' },
                    { field: 'relationship_manager_id', name: 'Relationship Manager' },
                    { field: 'insurance_company_id', name: 'Insurance Company' },
                    { field: 'premium_type_id', name: 'Premium Type' },
                    { field: 'policy_no', name: 'Policy Number' },
                    { field: 'start_date', name: 'Start Date' },
                    { field: 'expired_date', name: 'Expired Date' },
                    { field: 'net_premium', name: 'Net Premium' },
                    { field: 'cgst1', name: 'CGST 1' },
                    { field: 'sgst1', name: 'SGST 1' },
                    { field: 'final_premium_with_gst', name: 'Final Premium With GST' }
                ];

                requiredFields.forEach(function(item) {
                    const field = $(`[name="${item.field}"]`);
                    if (field.length && (!field.val() || field.val().trim() === '')) {
                        field.addClass('is-invalid');
                        field.after(`<div class="invalid-feedback">The ${item.name} field is required.</div>`);
                        isValid = false;
                        if (!firstErrorField) firstErrorField = field;
                    }
                });

                // Validate numeric fields
                const numericFields = [
                    'net_premium', 'cgst1', 'sgst1', 'cgst2', 'sgst2', 
                    'od_premium', 'tp_premium', 'final_premium_with_gst',
                    'ncb_percentage', 'mfg_year', 'premium_amount'
                ];

                numericFields.forEach(function(fieldName) {
                    const field = $(`[name="${fieldName}"]`);
                    if (field.length && field.val() && field.val().trim() !== '') {
                        const value = parseFloat(field.val());
                        if (isNaN(value) || value < 0) {
                            field.addClass('is-invalid');
                            field.after(`<div class="invalid-feedback">The ${fieldName.replace('_', ' ')} must be a valid positive number.</div>`);
                            isValid = false;
                            if (!firstErrorField) firstErrorField = field;
                        }
                    }
                });

                // Focus on first error field and show error message
                if (!isValid) {
                    if (firstErrorField) {
                        firstErrorField.focus();
                        $('html, body').animate({
                            scrollTop: firstErrorField.offset().top - 100
                        }, 500);
                    }
                    toastr.error('Please correct the errors in the form before submitting.');
                    e.preventDefault();
                    return false;
                }

                return isValid;
            });
        });
        const referenceBySelect = document.getElementById('reference_by');
        const referenceCommissionFields = document.querySelectorAll('.reference_commission_fields');

        function toggleReferenceFields() {
            if (!referenceBySelect) return;
            const selectedOptionValue = referenceBySelect.value;
            const isReferenceSelected = Number(selectedOptionValue) >= 1;
            // Loop through each element with the class and hide or show them accordingly
            referenceCommissionFields.forEach(element => {
                element.style.display = isReferenceSelected ? 'block' : 'none';
            });
        }
        toggleReferenceFields();
        if (referenceBySelect) {
            referenceBySelect.addEventListener('change', toggleReferenceFields);
        }

        var netPremiumInput = document.getElementById('net_premium');
        var odPremiumInput = document.getElementById('od_premium');
        var tpPremiumInput = document.getElementById('tp_premium');
        var cgst1Input = document.getElementById('cgst1');
        var cgst2Input = document.getElementById('cgst2');
        var sgst1Input = document.getElementById('sgst1');
        var sgst2Input = document.getElementById('sgst2');
        var finalPremiumInput = document.getElementById('final_premium_with_gst');

        var premiumTypeSelect = document.getElementById('premium_type_id');

        function calculateFinalPremium() {
            var netPremium = parseFloat(netPremiumInput ? netPremiumInput.value : 0) || 0;
            var odPremium = parseFloat(odPremiumInput ? odPremiumInput.value : 0) || 0;
            var tpPremium = parseFloat(tpPremiumInput ? tpPremiumInput.value : 0) || 0;
            var cgst1 = parseFloat(cgst1Input ? cgst1Input.value : 0) || 0;
            var cgst2 = parseFloat(cgst2Input ? cgst2Input.value : 0) || 0;
            var sgst1 = parseFloat(sgst1Input ? sgst1Input.value : 0) || 0;
            var sgst2 = parseFloat(sgst2Input ? sgst2Input.value : 0) || 0;


            if (!premiumTypeSelect || premiumTypeSelect.selectedIndex === -1) {
                return;
            }

            var selectedOption = premiumTypeSelect.options[premiumTypeSelect.selectedIndex];
            if (!selectedOption) {
                return;
            }
            
            var isVehicle = selectedOption.getAttribute('data-is_vehicle');
            var selectedOptionText = selectedOption.text;

            if (isVehicle === 'true' || isVehicle === '1') {
                netPremium = odPremium + tpPremium;
            }

            var finalPremium = netPremium + cgst1 + cgst2 + sgst1 + sgst2;

            if (finalPremiumInput && !isNaN(finalPremium)) {
                finalPremiumInput.value = finalPremium.toFixed(2);
            } else if (finalPremiumInput) {
                finalPremiumInput.value = '';
            }
        }

        if (netPremiumInput) netPremiumInput.addEventListener('input', calculateFinalPremium);
        if (odPremiumInput) odPremiumInput.addEventListener('input', calculateFinalPremium);
        if (tpPremiumInput) tpPremiumInput.addEventListener('input', calculateFinalPremium);
        if (cgst1Input) cgst1Input.addEventListener('input', calculateFinalPremium);
        if (cgst2Input) cgst2Input.addEventListener('input', calculateFinalPremium);
        if (sgst1Input) sgst1Input.addEventListener('input', calculateFinalPremium);
        if (sgst2Input) sgst2Input.addEventListener('input', calculateFinalPremium);
        if (premiumTypeSelect) premiumTypeSelect.addEventListener('change', calculateFinalPremium);

        // Initial calculation on page load
        calculateFinalPremium();

        function premiumTypeChanged() {
            var premiumTypeSelect = document.getElementById('premium_type_id');
            var premiumFields = document.getElementsByClassName('premium-fields');
            var lifeInsuranceFields = document.getElementsByClassName('life-insurance-policies-fields');
            var sgst2Field = document.getElementsByClassName('cgst_sgt2');
            var netPremiumInput = document.getElementById('net_premium');

            // Check if premium type is selected
            if (premiumTypeSelect.selectedIndex === -1 || !premiumTypeSelect.options[premiumTypeSelect.selectedIndex]) {
                return;
            }

            // Get the selected option value
            var selectedOption = premiumTypeSelect.options[premiumTypeSelect.selectedIndex];
            var isVehicle = selectedOption.getAttribute('data-is_vehicle');
            var isLifeInsurancePolicies = selectedOption.getAttribute('data-is_life_insurance_policies');

            // Show/hide premium fields based on the selected option value
            if (isVehicle === '1') {
                for (var i = 0; i < premiumFields.length; i++) {
                    premiumFields[i].style.display = 'block';
                }
                // Show sgct2 field for COMMERCIAL VEHICLE COMP and COMMERCIAL VEHICLE SATP
                if (selectedOption.text === 'COMMERCIAL VEHICLE COMP' || selectedOption.text ===
                    'COMMERCIAL VEHICLE SATP') {
                    for (var i = 0; i < sgst2Field.length; i++) {
                        sgst2Field[i].style.display = 'block';
                    }
                } else {
                    for (var i = 0; i < sgst2Field.length; i++) {
                        sgst2Field[i].style.display = 'none';
                    }
                }
                // Set netPremiumInput as readonly if element exists
                if (netPremiumInput) {
                    netPremiumInput.readOnly = true;
                }
            } else {
                for (var i = 0; i < premiumFields.length; i++) {
                    premiumFields[i].style.display = 'none';
                }
                for (var i = 0; i < sgst2Field.length; i++) {
                    sgst2Field[i].style.display = 'none';
                }
                if (netPremiumInput) {
                    netPremiumInput.readOnly = false;
                }
            }

            // Show/hide life insurance policies fields based on the selected option value
            if (isLifeInsurancePolicies === '1') {
                for (var i = 0; i < lifeInsuranceFields.length; i++) {
                    lifeInsuranceFields[i].style.display = 'block';
                }
            } else {
                for (var i = 0; i < lifeInsuranceFields.length; i++) {
                    lifeInsuranceFields[i].style.display = 'none';
                }
            }

            // Recalculate final premium when premium type changes
            calculateFinalPremium();
        }

        // Event handler for OD premium change
        if (odPremiumInput) {
            odPremiumInput.addEventListener('input', calculateNetPremium);
        }

        // Event handler for TP premium change
        if (tpPremiumInput) {
            tpPremiumInput.addEventListener('input', calculateNetPremium);
        }

        // Attach the event listeners to life_insurance_payment_mode and premium_amount to trigger the calculation
        var lifeInsurancePaymentMode = document.getElementById('life_insurance_payment_mode');
        var premiumAmountElement = document.getElementById('premium_amount');
        
        if (lifeInsurancePaymentMode) {
            lifeInsurancePaymentMode.addEventListener('change', calculateNetPremium);
        }
        if (premiumAmountElement) {
            premiumAmountElement.addEventListener('input', calculateNetPremium);
        }

        function calculateNetPremium() {
            var lifeInsurancePaymentModeSelect = document.getElementById('life_insurance_payment_mode');
            var premiumAmountInput = document.getElementById('premium_amount');
            var netPremiumInput = document.getElementById('net_premium');

            if (!netPremiumInput) return;

            var odValue = parseFloat(odPremiumInput ? odPremiumInput.value : 0) || 0;
            var tpValue = parseFloat(tpPremiumInput ? tpPremiumInput.value : 0) || 0;
            var premiumAmount = 0;

            // Only calculate premium amount if life insurance fields exist and have values
            if (lifeInsurancePaymentModeSelect && premiumAmountInput && 
                lifeInsurancePaymentModeSelect.selectedIndex >= 0 &&
                premiumAmountInput.value) {
                
                var selectedOption = lifeInsurancePaymentModeSelect.options[lifeInsurancePaymentModeSelect.selectedIndex];
                if (selectedOption) {
                    var multiplyBy = parseFloat(selectedOption.getAttribute('data-multiply_by')) || 1;
                    premiumAmount = parseFloat(premiumAmountInput.value) * multiplyBy;
                }
            }

            var netPremium = odValue + tpValue + (premiumAmount || 0);

            if (!isNaN(netPremium)) {
                netPremiumInput.value = netPremium.toFixed(2);
            } else {
                netPremiumInput.value = '';
            }
        }


        // Call the function on page load to initially show/hide fields
        premiumTypeChanged();

        function setExpiredDate() {
            var startDateInput = document.getElementById("start_date");
            var expiredDateInput = document.getElementById("expired_date");
            
            if (!startDateInput || !startDateInput.value) return;
            if (!expiredDateInput) return;
            
            // Get the date from Flatpickr instance
            if (!startDateInput._flatpickr || !startDateInput._flatpickr.selectedDates[0]) return;
            var startDate = startDateInput._flatpickr.selectedDates[0];
            
            // Calculate expired date: start date + 1 year - 1 day with proper leap year handling
            var expiredDate = new Date(startDate);
            
            // Add exactly 1 year
            expiredDate.setFullYear(startDate.getFullYear() + 1);
            
            // Handle leap year edge case: if start date is Feb 29 and next year is not leap year
            // JavaScript automatically adjusts Feb 29 to Feb 28, which is correct
            
            // Now subtract 1 day from the year anniversary
            expiredDate.setDate(expiredDate.getDate() - 1);
            
            // Set the date using Flatpickr
            if (expiredDateInput._flatpickr) {
                expiredDateInput._flatpickr.setDate(expiredDate);
            }
        }

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