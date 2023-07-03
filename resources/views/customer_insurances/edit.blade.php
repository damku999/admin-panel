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
            </div>
            <form method="POST"
                action="{{ route('customer_insurances.update', ['customer_insurance' => $customer_insurance->id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group row">
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
                                <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                                    id="issue_date" name="issue_date"
                                    value="{{ old('issue_date') ? old('issue_date') : $customer_insurance->issue_date }}" />
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
                        {{-- Policy No. --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Policy No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('policy_no') is-invalid @enderror"
                                id="policy_no" placeholder="Policy No." name="policy_no"
                                value="{{ old('policy_no') ? old('policy_no') : $customer_insurance->policy_no }}">

                            @error('policy_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Registration No. --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>Registration No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('registration_no') is-invalid @enderror"
                                id="registration_no" placeholder="Registration No." name="registration_no"
                                value="{{ old('registration_no') ? old('registration_no') : $customer_insurance->registration_no }}">

                            @error('registration_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Start Date --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Start Date</label>
                            <div class="input-group date">
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date"
                                    value="{{ old('start_date') ? old('start_date') : $customer_insurance->start_date }}"
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
                                <input type="date" class="form-control @error('expired_date') is-invalid @enderror"
                                    id="expired_date" name="expired_date"
                                    value="{{ old('expired_date') ? old('expired_date') : $customer_insurance->expired_date }}" />
                            </div>
                            @error('expired_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Premium Type</label>
                            <select name="premium_type_id" class="form-control" id="premium_type_id"
                                onchange="premiumTypeChanged()">
                                <option selected disabled>Select Premium Type</option>
                                @foreach ($premium_types as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        data-is_vehicle="{{ $item->is_vehicle }}"
                                        {{ old('premium_type_id', $customer_insurance->premium_type_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('premium_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Location --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label>Location</label>
                            <input type="text"
                                class="form-control form-control-customer @error('rto') is-invalid @enderror"
                                id="rto" placeholder="Location" name="rto"
                                value="{{ old('rto') ? old('rto') : $customer_insurance->rto }}">

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
                                value="{{ old('make_model') ? old('make_model') : $customer_insurance->make_model }}">

                            @error('make_model')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Fuel Type --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>Fuel Type</label>
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

                        {{-- OD Premium --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>OD Premium</label>
                            <input type="number"
                                class="form-control form-control-customer @error('od_premium') is-invalid @enderror"
                                id="od_premium" placeholder="OD Premium" name="od_premium"
                                value="{{ old('od_premium') ? old('od_premium') : $customer_insurance->od_premium }}">

                            @error('od_premium')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- TP Premium --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 premium-fields">
                            <label><span style="color: red;">*</span>TP Premium</label>
                            <input type="number"
                                class="form-control form-control-customer @error('tp_premium') is-invalid @enderror"
                                id="tp_premium" placeholder="TP Premium" name="tp_premium"
                                value="{{ old('tp_premium') ? old('tp_premium') : $customer_insurance->tp_premium }}">

                            @error('tp_premium')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Net Premium --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 net_premium_div">
                            <label><span style="color: red;">*</span>Net Premium</label>
                            <input type="number"
                                class="form-control form-control-customer @error('net_premium') is-invalid @enderror"
                                id="net_premium" placeholder="Net Premium" name="net_premium"
                                value="{{ old('net_premium') ? old('net_premium') : $customer_insurance->net_premium }}">

                            @error('net_premium')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GST --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 net_premium_div">
                            <label><span style="color: red;">*</span>GST</label>
                            <input type="number"
                                class="form-control form-control-customer @error('gst') is-invalid @enderror"
                                id="gst" placeholder="GST" name="gst"
                                value="{{ old('gst') ? old('gst') : $customer_insurance->gst }}">

                            @error('gst')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- CGST --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 cgst_sgst1 premium-fields">
                            <label><span style="color: red;">*</span>CGST 1</label>
                            <input type="number"
                                class="form-control form-control-customer @error('cgst1') is-invalid @enderror"
                                id="cgst1" placeholder="CGST" name="cgst1"
                                value="{{ old('cgst1') ? old('cgst1') : $customer_insurance->cgst1 }}">

                            @error('cgst1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- SGST --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 cgst_sgst1 premium-fields">
                            <label><span style="color: red;">*</span>SGST 1</label>
                            <input type="number"
                                class="form-control form-control-customer @error('sgst1') is-invalid @enderror"
                                id="sgst1" placeholder="SGST" name="sgst1"
                                value="{{ old('sgst1') ? old('sgst1') : $customer_insurance->sgst1 }}">

                            @error('sgst1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- CGST 2 --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 cgst_sgt2">
                            <label><span style="color: red;">*</span>CGST 2 </label>
                            <input type="number"
                                class="form-control form-control-customer @error('cgst2') is-invalid @enderror"
                                id="cgst2" placeholder="CGST 2" name="cgst2"
                                value="{{ old('cgst2') ? old('cgst2') : $customer_insurance->cgst2 }}">

                            @error('cgst2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- SGST 2 --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 cgst_sgt2">
                            <label><span style="color: red;">*</span>SGST 2 </label>
                            <input type="number"
                                class="form-control form-control-customer @error('sgst2') is-invalid @enderror"
                                id="sgst2" placeholder="SGST 2" name="sgst2"
                                value="{{ old('sgst2') ? old('sgst2') : $customer_insurance->sgst2 }}">

                            @error('sgst2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Final Premium With GST --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color: red;">*</span>Final Premium With GST</label>
                            <input type="number"
                                class="form-control form-control-customer @error('final_premium_with_gst') is-invalid @enderror"
                                id="final_premium_with_gst" placeholder="Final Premium With GST"
                                name="final_premium_with_gst"
                                value="{{ old('final_premium_with_gst') ? old('final_premium_with_gst') : $customer_insurance->final_premium_with_gst }}"
                                readonly>

                            @error('final_premium_with_gst')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mode of Payment --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label>Mode of Payment</label>
                            <input type="text"
                                class="form-control form-control-customer @error('mode_of_payment') is-invalid @enderror"
                                id="mode_of_payment" placeholder="Mode of Payment" name="mode_of_payment"
                                value="{{ old('mode_of_payment') ? old('mode_of_payment') : $customer_insurance->mode_of_payment }}">

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
                                value="{{ old('cheque_no') ? old('cheque_no') : $customer_insurance->cheque_no }}">

                            @error('cheque_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Policy Document --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0" id="gstDocumentSection">
                            <label for="policy_document_path">Policy Document</label>
                            <input type="file"
                                class="form-control form-control-customer @error('policy_document_path') is-invalid @enderror"
                                id="policy_document_path" placeholder="Policy Document" name="policy_document_path"
                                value="{{ old('policy_document_path') ? old('policy_document_path') : $customer_insurance->policy_document_path }}">
                            @error('policy_document_path')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
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
    <script>
        $(document).ready(function() {
            $('#customer_id').select2();
        });

        var netPremiumInput = document.getElementById('net_premium');
        var gstInput = document.getElementById('gst');
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
            var gst = parseFloat(gstInput.value) || 0;
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
                netPremium = 0;
                gst = 0;
                netPremiumInput.value = 0;
                gstInput.value = 0;
            }

            if (
                selectedOptionText !== 'COMMERCIAL VEHICLE COMP' ||
                selectedOptionText !== 'COMMERCIAL VEHICLE SATP'
            ) {
                cgst2 = 0;
                sgst2 = 0;
                cgst2Input.value = 0;
                sgst2Input.value = 0;
            }

            var finalPremium = netPremium + gst + odPremium + tpPremium + cgst1 + cgst2 + sgst1 + sgst2;

            if (!isNaN(finalPremium)) {
                finalPremiumInput.value = finalPremium.toFixed(2);
            } else {
                finalPremiumInput.value = '';
            }
        }

        netPremiumInput.addEventListener('input', calculateFinalPremium);
        gstInput.addEventListener('input', calculateFinalPremium);
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
            var sgst2Field = document.getElementsByClassName('cgst_sgt2');
            var netPremiumField = document.getElementsByClassName('net_premium_div');
            // Get the selected option value
            var selectedOption = premiumTypeSelect.options[premiumTypeSelect.selectedIndex];
            var isVehicle = selectedOption.getAttribute('data-is_vehicle');

            // Show/hide fields based on the selected option value
            if (isVehicle === 'true' || isVehicle === '1') {
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
                for (var i = 0; i < netPremiumField.length; i++) {
                    netPremiumField[i].style.display = 'none';
                }
            } else {
                for (var i = 0; i < premiumFields.length; i++) {
                    premiumFields[i].style.display = 'none';
                }
                for (var i = 0; i < sgst2Field.length; i++) {
                    sgst2Field[i].style.display = 'none';
                }
                for (var i = 0; i < netPremiumField.length; i++) {
                    netPremiumField[i].style.display = 'block';
                }
            }
        }

        // Call the function on page load to initially show/hide fields
        premiumTypeChanged();


        function setExpiredDate() {
            // Get the selected start date
            var startDate = new Date(document.getElementById("start_date").value);
            // Calculate the expired date by adding 1 year - 1 day to the start date
            var expiredDate = new Date(startDate.getFullYear() + 1, startDate.getMonth(), startDate.getDate() - 1);

            // Adjust the expired date if necessary
            if (startDate.getDate() === 29 && startDate.getMonth() === 1 && expiredDate.getDate() !== 28) {
                expiredDate.setDate(expiredDate.getDate() - 1);
            }

            // Format the expired date as "YYYY-MM-DD"
            var formattedExpiredDate = expiredDate.toISOString().split('T')[0];

            // Set the value of the expired date input field
            document.getElementById("expired_date").value = formattedExpiredDate;
        }
    </script>
@endsection
@section('stylesheets')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
