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
                                    name="start_date" placeholder="DD/MM/YYYY" value="{{ old('start_date') ? formatDateForUi(old('start_date')) : '' }}"
                                    onchange="setExpiredDate()">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Expired Date</label>
                                <input type="text" class="form-control form-control-sm datepicker @error('expired_date') is-invalid @enderror"
                                    name="expired_date" placeholder="DD/MM/YYYY" value="{{ old('expired_date') ? formatDateForUi(old('expired_date')) : '' }}">
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
                                <input type="text" class="form-control form-control-sm @error('ncb_percentage') is-invalid @enderror"
                                    name="ncb_percentage" placeholder="Enter NCB percentage" value="{{ old('ncb_percentage') }}">
                                @error('ncb_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 cgst_sgt2">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> GVW</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('gross_vehicle_weight') is-invalid @enderror"
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
                                <input type="text" class="decimal-input form-control form-control-sm @error('premium_paying_term') is-invalid @enderror"
                                    name="premium_paying_term" placeholder="Enter premium paying term" value="{{ old('premium_paying_term') }}">
                                @error('premium_paying_term')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Policy Term</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('policy_term') is-invalid @enderror"
                                    name="policy_term" placeholder="Enter policy term" value="{{ old('policy_term') }}">
                                @error('policy_term')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sum Insured</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('sum_insured') is-invalid @enderror"
                                    name="sum_insured" placeholder="Enter sum insured" value="{{ old('sum_insured') }}">
                                @error('sum_insured')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Pension Amount Yearly</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('pension_amount_yearly') is-invalid @enderror"
                                    name="pension_amount_yearly" placeholder="Enter pension amount yearly" value="{{ old('pension_amount_yearly') }}">
                                @error('pension_amount_yearly')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Approx Maturity Amount</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('approx_maturity_amount') is-invalid @enderror"
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
                                <input type="text" class="decimal-input form-control form-control-sm @error('od_premium') is-invalid @enderror"
                                    name="od_premium" placeholder="Enter OD premium" value="{{ old('od_premium') }}">
                                @error('od_premium')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 premium-fields">
                                <label class="form-label fw-semibold">TP Premium</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('tp_premium') is-invalid @enderror"
                                    name="tp_premium" placeholder="Enter TP premium" value="{{ old('tp_premium') }}">
                                @error('tp_premium')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 life-insurance-policies-fields">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Premium Amount</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('premium_amount') is-invalid @enderror"
                                    name="premium_amount" placeholder="Enter premium amount" value="{{ old('premium_amount') }}">
                                @error('premium_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Net Premium</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('net_premium') is-invalid @enderror"
                                    name="net_premium" placeholder="Enter net premium" value="{{ old('net_premium') }}">
                                @error('net_premium')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> CGST 1</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('cgst1') is-invalid @enderror"
                                    name="cgst1" placeholder="Enter CGST 1" value="{{ old('cgst1') }}">
                                @error('cgst1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> SGST 1</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('sgst1') is-invalid @enderror"
                                    name="sgst1" placeholder="Enter SGST 1" value="{{ old('sgst1') }}">
                                @error('sgst1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4 cgst_sgt2">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> CGST 2</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('cgst2') is-invalid @enderror"
                                    name="cgst2" placeholder="Enter CGST 2" value="{{ old('cgst2') }}">
                                @error('cgst2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 cgst_sgt2">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> SGST 2</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('sgst2') is-invalid @enderror"
                                    name="sgst2" placeholder="Enter SGST 2" value="{{ old('sgst2') }}">
                                @error('sgst2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Final Premium With GST</label>
                                <input type="text" class="decimal-input form-control form-control-sm @error('final_premium_with_gst') is-invalid @enderror"
                                    name="final_premium_with_gst" placeholder="Final premium with GST" value="{{ old('final_premium_with_gst') }}" readonly>
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
                                    <input type="text" class="decimal-input form-control form-control-sm @error('my_commission_percentage') is-invalid @enderror"
                                        name="my_commission_percentage" placeholder="Enter my commission %" value="{{ old('my_commission_percentage') }}">
                                    @error('my_commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">My Commission Amount</label>
                                    <input type="text" class="decimal-input form-control form-control-sm @error('my_commission_amount') is-invalid @enderror"
                                        name="my_commission_amount" placeholder="My commission amount" value="{{ old('my_commission_amount') }}" readonly>
                                    @error('my_commission_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transfer Commission Percentage</label>
                                    <input type="text" class="decimal-input form-control form-control-sm @error('transfer_commission_percentage') is-invalid @enderror"
                                        name="transfer_commission_percentage" placeholder="Enter transfer commission %" value="{{ old('transfer_commission_percentage') }}">
                                    @error('transfer_commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transfer Commission Amount</label>
                                    <input type="text" class="decimal-input form-control form-control-sm @error('transfer_commission_amount') is-invalid @enderror"
                                        name="transfer_commission_amount" placeholder="Transfer commission amount" value="{{ old('transfer_commission_amount') }}" readonly>
                                    @error('transfer_commission_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"><span class="text-danger">*</span> Actual Earnings</label>
                                    <input type="text" class="decimal-input form-control form-control-sm @error('actual_earnings') is-invalid @enderror"
                                        name="actual_earnings" placeholder="Actual earnings" value="{{ old('actual_earnings') }}" readonly>
                                    @error('actual_earnings')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4 reference_commission_fields">
                                    <label class="form-label fw-semibold">Reference Commission Percentage</label>
                                    <input type="text" class="decimal-input form-control form-control-sm @error('reference_commission_percentage') is-invalid @enderror"
                                        name="reference_commission_percentage" placeholder="Enter reference commission %" value="{{ old('reference_commission_percentage') }}">
                                    @error('reference_commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 reference_commission_fields">
                                    <label class="form-label fw-semibold">Reference Commission Amount</label>
                                    <input type="text" class="decimal-input form-control form-control-sm @error('reference_commission_amount') is-invalid @enderror"
                                        name="reference_commission_amount" placeholder="Reference commission amount" value="{{ old('reference_commission_amount') }}" readonly>
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
                var myCommissionPercentage = parseFloat($('#my_commission_percentage').val().replace(",", ".")) ||
                    0;
                var transferCommissionPercentage = parseFloat($('#transfer_commission_percentage').val().replace(
                    ",", ".")) || 0;

                var referenceCommissionPercentage = parseFloat($('#reference_commission_percentage').val().replace(
                    ",", ".")) || 0;


                // Validate percentage values
                myCommissionPercentage = Math.min(myCommissionPercentage, 100);
                transferCommissionPercentage = Math.min(transferCommissionPercentage, 100);
                referenceCommissionPercentage = Math.min(referenceCommissionPercentage, 100);

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

            // Calculate on change of commission fields
            $('#commission_on, #my_commission_percentage, #transfer_commission_percentage, #reference_commission_percentage, #net_premium, #od_premium, #tp_premium')
                .on('change',
                    function() {
                        calculateCommission();
                    });

            // Initial calculation on page load
            calculateCommission();
        });
        const referenceBySelect = document.getElementById('reference_by');
        const referenceCommissionFields = document.querySelectorAll('.reference_commission_fields');

        function toggleReferenceFields() {
            const selectedOptionValue = referenceBySelect.value;
            const isReferenceSelected = Number(selectedOptionValue) >= 1;
            // Loop through each element with the class and hide or show them accordingly
            referenceCommissionFields.forEach(element => {
                element.style.display = isReferenceSelected ? 'block' : 'none';
            });
        }
        toggleReferenceFields();
        referenceBySelect.addEventListener('change', toggleReferenceFields);

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
            var netPremium = parseFloat(netPremiumInput.value) || 0;
            var odPremium = parseFloat(odPremiumInput.value) || 0;
            var tpPremium = parseFloat(tpPremiumInput.value) || 0;
            var cgst1 = parseFloat(cgst1Input.value) || 0;
            var cgst2 = parseFloat(cgst2Input.value) || 0;
            var sgst1 = parseFloat(sgst1Input.value) || 0;
            var sgst2 = parseFloat(sgst2Input.value) || 0;

            var selectedOption = premiumTypeSelect.options[premiumTypeSelect.selectedIndex];
            var isVehicle = selectedOption.getAttribute('data-is_vehicle');
            var selectedOptionText = selectedOption.text;

            if (isVehicle === 'true' || isVehicle === '1') {
                netPremium = odPremium + tpPremium;
            }

            var finalPremium = netPremium + cgst1 + cgst2 + sgst1 + sgst2;

            if (!isNaN(finalPremium)) {
                finalPremiumInput.value = finalPremium.toFixed(2);
            } else {
                finalPremiumInput.value = '';
            }
        }

        netPremiumInput.addEventListener('input', calculateFinalPremium);
        odPremiumInput.addEventListener('input', calculateFinalPremium);
        tpPremiumInput.addEventListener('input', calculateFinalPremium);
        cgst1Input.addEventListener('input', calculateFinalPremium);
        cgst2Input.addEventListener('input', calculateFinalPremium);
        sgst1Input.addEventListener('input', calculateFinalPremium);
        sgst2Input.addEventListener('input', calculateFinalPremium);
        premiumTypeSelect.addEventListener('change', calculateFinalPremium);

        function premiumTypeChanged() {
            var premiumTypeSelect = document.getElementById('premium_type_id');
            var premiumFields = document.getElementsByClassName('premium-fields');
            var lifeInsuranceFields = document.getElementsByClassName('life-insurance-policies-fields');
            var sgst2Field = document.getElementsByClassName('cgst_sgt2');
            var netPremiumInput = document.getElementById(
                'net_premium'); // Replace 'net_premium' with the actual ID of the net premium input field

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
                // Set netPremiumInput as readonly
                netPremiumInput.readOnly = true;
            } else {
                for (var i = 0; i < premiumFields.length; i++) {
                    premiumFields[i].style.display = 'none';
                }
                for (var i = 0; i < sgst2Field.length; i++) {
                    sgst2Field[i].style.display = 'none';
                }
                netPremiumInput.readOnly = false;
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
        }

        // Event handler for OD premium change
        odPremiumInput.addEventListener('input', calculateNetPremium);

        // Event handler for TP premium change
        tpPremiumInput.addEventListener('input', calculateNetPremium);

        // Attach the event listeners to life_insurance_payment_mode and premium_amount to trigger the calculation
        document.getElementById('life_insurance_payment_mode').addEventListener('change', calculateNetPremium);
        document.getElementById('premium_amount').addEventListener('input', calculateNetPremium);

        function calculateNetPremium() {
            var lifeInsurancePaymentModeSelect = document.getElementById('life_insurance_payment_mode');
            var premiumAmountInput = document.getElementById('premium_amount');
            var netPremiumInput = document.getElementById('net_premium');
            var selectedOption = lifeInsurancePaymentModeSelect.options[lifeInsurancePaymentModeSelect.selectedIndex];

            var multiplyBy = parseFloat(selectedOption.getAttribute('data-multiply_by'));
            var premiumAmount = parseFloat(premiumAmountInput.value) * multiplyBy;

            var odValue = parseFloat(odPremiumInput.value) || 0;
            var tpValue = parseFloat(tpPremiumInput.value) || 0;
            var premiumAmount = parseFloat(premiumAmount) || 0;
            var netPremium = odValue + tpValue + premiumAmount;

            if (!isNaN(netPremium)) {
                netPremiumInput.value = netPremium.toFixed(2); // Adjust the decimal places as needed
            } else {
                netPremiumInput.value = '';
            }
        }


        // Call the function on page load to initially show/hide fields
        premiumTypeChanged();

        function setExpiredDate() {
            var startDateStr = document.getElementById("start_date").value;
            var startDateComponents = startDateStr.split("-"); // Split the date string by '-'

            // Create a new Date object using the parsed components
            var startDate = new Date(startDateComponents[2], startDateComponents[1] - 1, startDateComponents[0]);

            // Ensure startDate is set to the correct time (typically midnight)
            startDate.setHours(0, 0, 0, 0);
            console.log(startDate);
            // Calculate the expired date by adding 1 year - 1 day to the start date
            var expiredDate = new Date(startDate);
            expiredDate.setFullYear(startDate.getFullYear() + 1);
            expiredDate.setDate(startDate.getDate() - 1);
            console.log(expiredDate);

            // Format the expired date as "dd-mm-yyyy"
            var formattedExpiredDate = ('0' + expiredDate.getDate()).slice(-2) + '-' + ('0' + (expiredDate.getMonth() + 1))
                .slice(-2) + '-' + expiredDate.getFullYear();
            console.log(formattedExpiredDate);

            // Set the formatted expired date to the input field
            $('#expired_date').datepicker('update', formattedExpiredDate);
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