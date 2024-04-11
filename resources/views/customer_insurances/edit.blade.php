@extends('layouts.app')

@section('title', 'Add Customer Insurance')

@section('content')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Customer Insurance</h1>
            <a href="{{ route('customer_insurances.index') }}" onclick="window.history.go(-1); return false;"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Customer Insurance</h6>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <form method="POST"
                action="{{ route('customer_insurances.update', ['customer_insurance' => $customer_insurance->id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group row mb-12">
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0"><span style="color: red;">*</span>Customer
                            <select name="customer_id" class="form-control" id="customer_id">
                                <option selected="selected" disabled="disabled">Select Customer</option>
                                @foreach ($customers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('customer_id', $customer_insurance->customer_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Issue Date --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Issue Date</label>
                            <div class="input-group date" id="issue_date">
                                <input type="text"
                                    class="form-control datepicker @error('issue_date') is-invalid @enderror"
                                    id="issue_date" name="issue_date"
                                    value="{{ old('issue_date', date('d-m-Y', strtotime($customer_insurance->issue_date))) }}" />
                            </div>
                            @error('issue_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Policy Type --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Policy Type</label>

                            <select name="policy_type_id" class="form-control" id="policy_type_id">
                                <option selected="selected" disabled="disabled">Select Policy Type</option>
                                @foreach ($policy_type as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('policy_type_id', $customer_insurance->policy_type_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('policy_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Branch --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Branch</label>
                            <select name="branch_id" class="form-control" id="branch_id">
                                <option selected="selected" disabled="disabled">Select Branch</option>
                                @foreach ($branches as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('branch_id', $customer_insurance->branch_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Broker --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Broker</label>
                            <select name="broker_id" class="form-control" id="broker_id">
                                <option selected="selected" disabled="disabled">Select Broker</option>
                                @foreach ($brokers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('broker_id', $customer_insurance->broker_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('broker_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- RM --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Relationship Manager</label>
                            <select name="relationship_manager_id" class="form-control" id="relationship_manager_id">
                                <option selected="selected" disabled="disabled">Select Broker</option>
                                @foreach ($relationship_managers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('relationship_manager_id', $customer_insurance->relationship_manager_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('relationship_manager_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Insurance Company Name --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Insurance Company Name</label>
                            <select name="insurance_company_id" class="form-control" id="insurance_company_id">
                                <option selected="selected" disabled="disabled">Select Company</option>
                                @foreach ($insurance_companies as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('insurance_company_id', $customer_insurance->insurance_company_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('insurance_company_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Type OF Policy --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Premium Type</label>
                            <select name="premium_type_id" class="form-control" id="premium_type_id"
                                onchange="premiumTypeChanged()">
                                <option selected="selected" disabled="disabled">Select Premium Type</option>
                                @foreach ($premium_types as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        data-is_vehicle={{ $item->is_vehicle }}
                                        data-is_life_insurance_policies={{ $item->is_life_insurance_policies }}
                                        {{ old('premium_type_id', $customer_insurance->premium_type_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('premium_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Policy No. --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Policy No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('policy_no') is-invalid @enderror"
                                id="policy_no" placeholder="Policy No." name="policy_no"
                                value="{{ old('policy_no', $customer_insurance->policy_no) }}">

                            @error('policy_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Start Date --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Start Date</label>
                            <div class="input-group date">
                                <input type="text"
                                    class="form-control datepicker @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date"
                                    value="{{ old('start_date', date('d-m-Y', strtotime($customer_insurance->start_date))) }}"
                                    onblur="setExpiredDate()" />
                            </div>
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Expired Date --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Expired Date</label>
                            <div class="input-group date">
                                <input type="text"
                                    class="form-control datepicker @error('expired_date') is-invalid @enderror"
                                    id="expired_date" name="expired_date"
                                    value="{{ old('expired_date', date('d-m-Y', strtotime($customer_insurance->expired_date))) }}" />
                            </div>
                            @error('expired_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Registration No. --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>Registration No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('registration_no') is-invalid @enderror"
                                id="registration_no" placeholder="Registration No." name="registration_no"
                                value="{{ old('registration_no', $customer_insurance->registration_no) }}">

                            @error('registration_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Location --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label>Location</label>
                            <input type="text"
                                class="form-control form-control-customer @error('rto') is-invalid @enderror"
                                id="rto" placeholder="Location" name="rto"
                                value="{{ old('rto', $customer_insurance->rto) }}">

                            @error('rto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Make & Model --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label>Make & Model</label>
                            <input type="text"
                                class="form-control form-control-customer @error('make_model') is-invalid @enderror"
                                id="make_model" placeholder="Make & Model" name="make_model"
                                value="{{ old('make_model', $customer_insurance->make_model) }}">

                            @error('make_model')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Fuel Type --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label>Fuel Type</label>
                            <select name="fuel_type_id" class="form-control" id="fuel_type_id">
                                <option selected="selected" disabled="disabled">Select Fuel Type</option>
                                @foreach ($fuel_type as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('fuel_type_id', $customer_insurance->fuel_type_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fuel_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- TP Expiry Date --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>TP Expiry Date</label>
                            <div class="input-group date">
                                <input type="text"
                                    class="form-control datepicker @error('tp_expiry_date') is-invalid @enderror"
                                    id="tp_expiry_date" name="tp_expiry_date"
                                    value="{{ old('tp_expiry_date', date('d-m-Y', strtotime($customer_insurance->tp_expiry_date))) }}" />
                            </div>
                            @error('tp_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- MFG Year --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>MFG Year</label>
                            <div class="input-group date">
                                <input type="number" min="1900" max="2099" step="1"
                                    class="form-control @error('mfg_year') is-invalid @enderror" id="mfg_year"
                                    name="mfg_year" value="{{ old('mfg_year', $customer_insurance->mfg_year) }}" />
                            </div>
                            @error('mfg_year')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NCB % --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>NCB %</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('ncb_percentage') is-invalid @enderror"
                                    id="ncb_percentage" name="ncb_percentage"
                                    value="{{ old('ncb_percentage', $customer_insurance->ncb_percentage) }}"
                                    placeholder="NCB %" />
                            </div>
                            @error('ncb_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GVW --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 cgst_sgt2">
                            <label><span style="color: red;">*</span>GVW </label>
                            <input type="text"
                                class="decimal-input form-control form-control-customer @error('gross_vehicle_weight') is-invalid @enderror"
                                id="gross_vehicle_weight" placeholder="GVW" name="gross_vehicle_weight"
                                value="{{ old('gross_vehicle_weight', $customer_insurance->gross_vehicle_weight) }}">

                            @error('gross_vehicle_weight')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Plan Name --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label>Plan Name</label>
                            <input type="text"
                                class="form-control form-control-customer @error('plan_name') is-invalid @enderror"
                                id="plan_name" placeholder="Plan Name" name="plan_name"
                                value="{{ old('plan_name', $customer_insurance->plan_name) }}">
                            @error('plan_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Premium Paying Term --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label>Premium Paying Term</label>
                            <input type="text"
                                class="decimal-input form-control form-control-customer @error('premium_paying_term') is-invalid @enderror"
                                id="premium_paying_term" placeholder="Premium Paying Term" name="premium_paying_term"
                                value="{{ old('premium_paying_term', $customer_insurance->premium_paying_term) }}">
                            @error('premium_paying_term')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Policy Term --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label>Policy Term</label>
                            <input type="text"
                                class="decimal-input form-control form-control-customer @error('policy_term') is-invalid @enderror"
                                id="policy_term" placeholder="Policy Term" name="policy_term"
                                value="{{ old('policy_term', $customer_insurance->policy_term) }}">
                            @error('policy_term')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Sum Insured --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label>Sum Insured</label>
                            <input type="text"
                                class="decimal-input form-control form-control-customer @error('sum_insured') is-invalid @enderror"
                                id="sum_insured" placeholder="Sum Insured" name="sum_insured"
                                value="{{ old('sum_insured', $customer_insurance->sum_insured) }}">
                            @error('sum_insured')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Pension Amount Yearly --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label>Pension Amount Yearly </label>
                            <input type="text"
                                class="decimal-input form-control form-control-customer @error('pension_amount_yearly') is-invalid @enderror"
                                id="pension_amount_yearly" placeholder="Pension Amount Yearly "
                                name="pension_amount_yearly"
                                value="{{ old('pension_amount_yearly', $customer_insurance->pension_amount_yearly) }}">
                            @error('pension_amount_yearly')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Approx Maturity Amount --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label>Approx Maturity Amount </label>
                            <input type="text"
                                class="decimal-input form-control form-control-customer @error('approx_maturity_amount') is-invalid @enderror"
                                id="approx_maturity_amount" placeholder="Approx Maturity Amount "
                                name="approx_maturity_amount"
                                value="{{ old('approx_maturity_amount', $customer_insurance->approx_maturity_amount) }}">
                            @error('approx_maturity_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Life Insurance Payment Mode --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label><span style="color: red;">*</span>Life Insurance Payment Mode</label>
                            <select name="life_insurance_payment_mode" class="form-control"
                                id="life_insurance_payment_mode">
                                <option selected="selected">Select Life Insurance Payment Mode
                                </option>
                                @foreach ($life_insurance_payment_mode as $item)
                                    <option id="{{ $item['id'] }}" value="{{ $item['id'] }}"
                                        data-multiply_by={{ $item['multiply_by'] }}
                                        {{ old('life_insurance_payment_mode', $customer_insurance->life_insurance_payment_mode) == $item['id'] ? 'selected' : '' }}>
                                        {{ $item['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('life_insurance_payment_mode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Maturity Date --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label><span style="color: red;">*</span>Maturity Date</label>
                            <div class="input-group date" id="maturity_date">
                                <input type="text"
                                    class="form-control datepicker @error('maturity_date') is-invalid @enderror"
                                    id="maturity_date" name="maturity_date"
                                    value="{{ old('maturity_date', date('d-m-Y', strtotime($customer_insurance->maturity_date))) }}" />
                            </div>
                            @error('maturity_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Remarks --}}
                        <div class="col-sm-6 col-md-8 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                            <label>Remarks</label>
                            <textarea class="form-control form-control-customer @error('remarks') is-invalid @enderror" id="remarks"
                                placeholder="Remarks" name="remarks" rows="4">{{ old('remarks', $customer_insurance->remarks) }}</textarea>
                            @error('remarks')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mode of Payment --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label>Mode of Payment</label>
                            <input type="text"
                                class="form-control form-control-customer @error('mode_of_payment') is-invalid @enderror"
                                id="mode_of_payment" placeholder="Mode of Payment" name="mode_of_payment"
                                value="{{ old('mode_of_payment', $customer_insurance->mode_of_payment) }}">

                            @error('mode_of_payment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Cheque No. --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label>Cheque No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('cheque_no') is-invalid @enderror"
                                id="cheque_no" placeholder="Cheque No." name="cheque_no"
                                value="{{ old('cheque_no', $customer_insurance->cheque_no) }}">

                            @error('cheque_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Policy Document --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="policy_document_path">Policy Document</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input @error('policy_document_path') is-invalid @enderror"
                                        id="policy_document_path" placeholder="Policy Document"
                                        name="policy_document_path" value="{{ old('policy_document_path') }}">
                                    <label class="custom-file-label" for="policy_document_path">Choose file</label>
                                </div>
                                <div class="input-group-append">
                                    @if ($customer_insurance->policy_document_path)
                                        <a href="{{ asset('storage/' . $customer_insurance->policy_document_path) }}"
                                            class="btn btn-primary" target="__blank">Download</a>
                                    @endif
                                </div>
                            </div>
                            @error('policy_document_path')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Reference Name --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label>Reference By :</label>
                            <select name="reference_by" class="form-control" id="reference_by">
                                <option selected="selected" value="0">Select Reference By</option>
                                @foreach ($reference_by_user as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ old('reference_by', $customer_insurance->reference_by) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reference_by')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card mb-12 col-md-12 border-left-success">
                        <div class="form-group row">
                            {{-- OD Premium --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                                <label>OD Premium</label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('od_premium') is-invalid @enderror"
                                    id="od_premium" placeholder="OD Premium" name="od_premium"
                                    value="{{ old('od_premium', $customer_insurance->od_premium) }}">

                                @error('od_premium')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- TP Premium --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                                <label>TP Premium</label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('tp_premium') is-invalid @enderror"
                                    id="tp_premium" placeholder="TP Premium" name="tp_premium"
                                    value="{{ old('tp_premium', $customer_insurance->tp_premium) }}">

                                @error('tp_premium')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- Premium Amount --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 life-insurance-policies-fields">
                                <label><span style="color: red;">*</span>Premium Amount</label>
                                <div class="input-group date">
                                    <input type="date"
                                        class="form-control @error('premium_amount') is-invalid @enderror"
                                        id="premium_amount" name="premium_amount"
                                        value="{{ old('premium_amount', $customer_insurance->premium_amount) }}" />
                                </div>
                                @error('premium_amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Net Premium --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                <label><span style="color: red;">*</span>Net Premium</label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('net_premium') is-invalid @enderror"
                                    id="net_premium" placeholder="Net Premium" name="net_premium"
                                    value="{{ old('net_premium', $customer_insurance->net_premium) }}">

                                @error('net_premium')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- CGST --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                <label><span style="color: red;">*</span>CGST 1</label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('cgst1') is-invalid @enderror"
                                    id="cgst1" placeholder="CGST" name="cgst1"
                                    value="{{ old('cgst1', $customer_insurance->cgst1) }}">

                                @error('cgst1')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- SGST --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                <label><span style="color: red;">*</span>SGST 1</label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('sgst1') is-invalid @enderror"
                                    id="sgst1" placeholder="SGST" name="sgst1"
                                    value="{{ old('sgst1', $customer_insurance->sgst1) }}">

                                @error('sgst1')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- CGST 2 --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 cgst_sgt2">
                                <label><span style="color: red;">*</span>CGST 2 </label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('cgst2') is-invalid @enderror"
                                    id="cgst2" placeholder="CGST 2" name="cgst2"
                                    value="{{ old('cgst2', $customer_insurance->cgst2) }}">

                                @error('cgst2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- SGST 2 --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 cgst_sgt2">
                                <label><span style="color: red;">*</span>SGST 2 </label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('sgst2') is-invalid @enderror"
                                    id="sgst2" placeholder="SGST 2" name="sgst2"
                                    value="{{ old('sgst2', $customer_insurance->sgst2) }}">

                                @error('sgst2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Final Premium With GST --}}
                            <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                <label><span style="color: red;">*</span>Final Premium With GST</label>
                                <input type="text"
                                    class="decimal-input form-control form-control-customer @error('final_premium_with_gst') is-invalid @enderror"
                                    id="final_premium_with_gst" placeholder="Final Premium With GST"
                                    name="final_premium_with_gst"
                                    value="{{ old('final_premium_with_gst', $customer_insurance->final_premium_with_gst) }}"
                                    readonly>

                                @error('final_premium_with_gst')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <br>
                    @if (auth()->user()->hasRole('Admin'))
                        <div class="card mt-12 col-md-12 border-left-dark">
                            <div class="form-group row">
                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label>Commission On</label>
                                    <select name="commission_on" class="form-control" id="commission_on">
                                        <option value="net_premium" @if (old('commission_on', $customer_insurance->commission_on) == 'net_premium') selected @endif> Net
                                            Premium </option>
                                        <option value="od_premium" @if (old('commission_on', $customer_insurance->commission_on) == 'od_premium') selected @endif>OD
                                            Premium </option>
                                        <option value="tp_premium" @if (old('commission_on', $customer_insurance->commission_on) == 'tp_premium') selected @endif>TP
                                            Premium </option>
                                    </select>
                                    @error('commission_on')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label>My Commission Percentage</label>
                                    <input type="text"
                                        class="decimal-input form-control form-control-customer @error('my_commission_percentage') is-invalid @enderror"
                                        id="my_commission_percentage" placeholder="My Commission Percentage"
                                        name="my_commission_percentage"
                                        value="{{ old('my_commission_percentage', $customer_insurance->my_commission_percentage) }}">
                                    @error('my_commission_percentage')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label>My Commission Amount</label>
                                    <input type="text"
                                        class="decimal-input form-control form-control-customer @error('my_commission_amount') is-invalid @enderror"
                                        id="my_commission_amount" placeholder="My Commission Amount"
                                        name="my_commission_amount"
                                        value="{{ old('my_commission_amount', $customer_insurance->my_commission_amount) }}"
                                        readonly>
                                    @error('my_commission_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label><span style="color: red;">*</span>Transfer Commission Percentage</label>
                                    <input type="text"
                                        class="decimal-input form-control form-control-customer @error('transfer_commission_percentage') is-invalid @enderror"
                                        id="transfer_commission_percentage" placeholder="Transfer Commission Percentage"
                                        name="transfer_commission_percentage"
                                        value="{{ old('transfer_commission_percentage', $customer_insurance->transfer_commission_percentage) }}">
                                    @error('transfer_commission_percentage')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label><span style="color: red;">*</span>Transfer Commission Amount</label>
                                    <input type="text"
                                        class="decimal-input form-control form-control-customer @error('transfer_commission_amount') is-invalid @enderror"
                                        id="transfer_commission_amount" placeholder="Transfer Commission Amount"
                                        name="transfer_commission_amount"
                                        value="{{ old('transfer_commission_amount', $customer_insurance->transfer_commission_amount) }}"
                                        readonly>
                                    @error('transfer_commission_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label>Reference Commission Percentage</label>
                                    <input type="text"
                                        class="decimal-input form-control form-control-customer @error('reference_commission_percentage') is-invalid @enderror"
                                        id="reference_commission_percentage" placeholder="Reference Commission Percentage"
                                        name="reference_commission_percentage"
                                        value="{{ old('reference_commission_percentage', $customer_insurance->reference_commission_percentage) }}">
                                    @error('reference_commission_percentage')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label>Reference Commission Amount</label>
                                    <input type="text"
                                        class="decimal-input form-control form-control-customer @error('reference_commission_amount') is-invalid @enderror"
                                        id="reference_commission_amount" placeholder="Reference Commission Amount"
                                        name="reference_commission_amount"
                                        value="{{ old('reference_commission_amount', $customer_insurance->reference_commission_amount) }}"
                                        readonly>
                                    @error('reference_commission_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                                    <label><span style="color: red;">*</span>Actual Earnings</label>
                                    <input type="text"
                                        class="decimal-input form-control form-control-customer @error('actual_earnings') is-invalid @enderror"
                                        id="actual_earnings" placeholder="Actual Earnings" name="actual_earnings"
                                        value="{{ old('actual_earnings', $customer_insurance->actual_earnings) }}"
                                        readonly>
                                    @error('actual_earnings')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-customer_insurance float-right mb-3">Save</button>
                    <a class="btn btn-primary float-right mr-3 mb-3"
                        href="{{ route('customer_insurances.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy', // Adjust the format as per your requirement
                autoclose: true
            });
            $('#customer_id').select2();
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection
