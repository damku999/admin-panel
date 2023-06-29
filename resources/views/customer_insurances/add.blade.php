@extends('layouts.app')

@section('title', 'Add Customer Insurance')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Customer Insurance</h1>
            <a href="{{ route('customer_insurances.index') }}"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Customer Insurance</h6>
            </div>
            <form method="POST" action="{{ route('customer_insurances.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0"><span style="color: red;">*</span>Customer
                            <select name="customer_id" class="form-control" id="customer_id">
                                <option selected="selected" disabled="disabled">Select Customer</option>
                                @foreach ($customers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}">{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Issue Date --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Issue Date</label>
                            <div class="input-group date" id="issue_date">
                                <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                                    id="issue_date" name="issue_date" value="{{ old('issue_date') }}" />
                                <span class="input-group-append">
                                    {{-- <span class="input-group-text bg-light d-block">
                                        <i class="fa fa-calendar"></i>
                                    </span> --}}
                                </span>
                            </div>
                            @error('issue_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Policy Type --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Policy Type</label>

                            @php
                                $policy_type_id = old('policy_type_id') ? old('policy_type_id') : 0;
                            @endphp
                            <select name="policy_type_id" class="form-control" id="policy_type_id">
                                <option selected="selected" disabled="disabled">Select Policy Type</option>
                                @foreach ($policy_type as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        @if ($policy_type_id == $item->id) @selected(true) @endif>
                                        {{ $item->name }}>{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('policy_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Branch --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control" id="branch_id">
                                <option selected="selected" disabled="disabled">Select Branch</option>
                                @foreach ($branches as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}">{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Broker --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Broker</label>
                            <select name="broker_id" class="form-control" id="broker_id">
                                <option selected="selected" disabled="disabled">Select Broker</option>
                                @foreach ($brokers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}">{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('broker_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- RM --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Relationship Manager</label>
                            <select name="relationship_manager_id" class="form-control" id="relationship_manager_id">
                                <option selected="selected" disabled="disabled">Select Broker</option>
                                @foreach ($brokers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}">{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('relationship_manager_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Insurance Company Name --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Insurance Company Name</label>
                            <select name="insurance_company_id" class="form-control" id="insurance_company_id">
                                <option selected="selected" disabled="disabled">Select Broker</option>
                                @foreach ($insurance_companies as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}">{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('insurance_company_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Type OF Policy --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Premium Type</label>
                            <select name="premium_type_id" class="form-control" id="premium_type_id"
                                onchange="premiumTypeChanged()">
                                <option selected="selected" disabled="disabled">Select Premium Type</option>
                                @foreach ($premium_types as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        data-is_vehicle={{ $item->is_vehicle }}>{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('premium_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Policy No. --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Policy No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('policy_no') is-invalid @enderror"
                                id="policy_no" placeholder="Registration No." name="policy_no"
                                value="{{ old('policy_no') }}">

                            @error('policy_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Registration No. --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Registration No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('registration_no') is-invalid @enderror"
                                id="registration_no" placeholder="Registration No." name="registration_no"
                                value="{{ old('registration_no') }}">

                            @error('registration_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Location --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Location</label>
                            <input type="text"
                                class="form-control form-control-customer @error('rto') is-invalid @enderror"
                                id="rto" placeholder="Location" name="rto" value="{{ old('rto') }}">

                            @error('rto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Make & Model --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Make & Model</label>
                            <input type="text"
                                class="form-control form-control-customer @error('make_model') is-invalid @enderror"
                                id="make_model" placeholder="Make & Model" name="make_model"
                                value="{{ old('make_model') }}">

                            @error('make_model')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Fuel Type --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Feaul Type</label>
                            @php
                                $fuel_type_id = old('fuel_type_id') ? old('fuel_type_id') : 0;
                            @endphp
                            <select name="fuel_type_id" class="form-control" id="fuel_type_id">
                                <option selected="selected" disabled="disabled">Select Feaul Type</option>
                                @foreach ($fuel_type as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        @if ($fuel_type_id == $item->id) @selected(true) @endif>{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fuel_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Start Date --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Start Date</label>
                            <div class="input-group date" id="start_date">
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date" value="{{ old('start_date') }}" />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </span>
                            </div>
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Expired Date --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Expired Date</label>
                            <div class="input-group date" id="expired_date">
                                <input type="date" class="form-control @error('expired_date') is-invalid @enderror"
                                    id="expired_date" name="expired_date" value="{{ old('expired_date') }}" />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </span>
                            </div>
                            @error('expired_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- OD Premium --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>OD Premium</label>
                            <input type="text"
                                class="form-control form-control-customer @error('od_premium') is-invalid @enderror"
                                id="od_premium" placeholder="OD Premium" name="od_premium"
                                value="{{ old('od_premium') }}">

                            @error('od_premium')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- TP Premium --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>TP Premium</label>
                            <input type="text"
                                class="form-control form-control-customer @error('tp_premium') is-invalid @enderror"
                                id="tp_premium" placeholder="TP Premium" name="tp_premium"
                                value="{{ old('tp_premium') }}">

                            @error('tp_premium')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- RSA --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>RSA</label>
                            <input type="text"
                                class="form-control form-control-customer @error('rsa') is-invalid @enderror"
                                id="rsa" placeholder="RSA" name="rsa" value="{{ old('rsa') }}">

                            @error('rsa')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Net Premium --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Net Premium</label>
                            <input type="text"
                                class="form-control form-control-customer @error('net_premium') is-invalid @enderror"
                                id="net_premium" placeholder="Net Premium" name="net_premium"
                                value="{{ old('net_premium') }}">

                            @error('net_premium')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GST --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>GST</label>
                            <input type="text"
                                class="form-control form-control-customer @error('gst') is-invalid @enderror"
                                id="gst" placeholder="GST" name="gst" value="{{ old('gst') }}">

                            @error('gst')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Final Premium With GST --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Final Premium With GST</label>
                            <input type="text"
                                class="form-control form-control-customer @error('final_premium_with_gst') is-invalid @enderror"
                                id="final_premium_with_gst" placeholder="Final Premium With GST"
                                name="final_premium_with_gst" value="{{ old('final_premium_with_gst') }}">

                            @error('final_premium_with_gst')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mode of Payment --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Mode of Payment</label>
                            <input type="text"
                                class="form-control form-control-customer @error('mode_of_payment') is-invalid @enderror"
                                id="mode_of_payment" placeholder="Mode of Payment" name="mode_of_payment"
                                value="{{ old('mode_of_payment') }}">

                            @error('mode_of_payment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Cheque No. --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Cheque No.</label>
                            <input type="text"
                                class="form-control form-control-customer @error('cheque_no') is-invalid @enderror"
                                id="cheque_no" placeholder="Cheque No." name="cheque_no"
                                value="{{ old('cheque_no') }}">

                            @error('cheque_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Premium --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Premium</label>
                            <input type="text"
                                class="form-control form-control-customer @error('premium') is-invalid @enderror"
                                id="premium" placeholder="Premium" name="premium" value="{{ old('premium') }}">

                            @error('premium')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Issued By --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Issued By</label>
                            <input type="text"
                                class="form-control form-control-customer @error('issued_by') is-invalid @enderror"
                                id="issued_by" placeholder="Issued By" name="issued_by" value="{{ old('issued_by') }}">

                            @error('issued_by')
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

        function premiumTypeChanged() {
            var dataid = $("#premium_type_id option:selected").attr('data-is_vehicle');
            if(dataid){
                
            }
        }
    </script>
@endsection
@section('stylesheets')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
