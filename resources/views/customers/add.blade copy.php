@extends('layouts.app')

@section('title', 'Add Customers')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Customers</h1>
            <a href="{{ route('customers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Customer</h6>
            </div>
            <form method="POST" action="{{ route('customers.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Name</label>
                            <input type="text"
                                class="form-control form-control-customer @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="Name" name="name" value="{{ old('name') }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Email</label>
                            <input type="email"
                                class="form-control form-control-customer @error('email') is-invalid @enderror"
                                id="exampleEmail" placeholder="Email" name="email" value="{{ old('email') }}">

                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Mobile Number</label>
                            <input type="text"
                                class="form-control form-control-customer @error('mobile_number') is-invalid @enderror"
                                id="exampleMobile" placeholder="Mobile Number" name="mobile_number"
                                value="{{ old('mobile_number') }}">

                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Status</label>
                            <select class="form-control form-control-customer @error('status') is-invalid @enderror"
                                name="status">
                                <option selected disabled>Select Status</option>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        {{-- Date Of Birth --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Date Of Birth</label>
                            <div class="input-group date" id="datepicker">
                                <input type="text" class="form-control @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </span>
                            </div>
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date Of Engagement Anniversary --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Date Of Engagement Anniversary</label>
                            <div class="input-group date" id="datepicker">
                                <input type="text"
                                    class="form-control @error('engagement_anniversary_date') is-invalid @enderror"
                                    id="engagement_anniversary_date" name="engagement_anniversary_date"
                                    value="{{ old('engagement_anniversary_date') }}" />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </span>
                            </div>
                            @error('engagement_anniversary_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date Of Wedding Anniversary --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Date Of Wedding Anniversary</label>
                            <div class="input-group date" id="datepicker">
                                <input type="text"
                                    class="form-control @error('wedding_anniversary_date') is-invalid @enderror"
                                    id="wedding_anniversary_date" name="wedding_anniversary_date"
                                    value="{{ old('wedding_anniversary_date') }}" />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </span>
                            </div>
                            @error('wedding_anniversary_date')
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
                                    <span class="input-group-text bg-light d-block">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </span>
                            </div>
                            @error('issue_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Bus Type --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Bus Type</label>
                            <input type="text"
                                class="form-control form-control-customer @error('bus_type') is-invalid @enderror"
                                id="bus_type" placeholder="Bus Type" name="bus_type" value="{{ old('bus_type') }}">

                            @error('bus_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Branch --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Branch</label>
                            <input type="text"
                                class="form-control form-control-customer @error('branch') is-invalid @enderror"
                                id="branch" placeholder="Branch" name="branch" value="{{ old('branch') }}">

                            @error('branch')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Broker --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Broker</label>
                            <input type="text"
                                class="form-control form-control-customer @error('broker') is-invalid @enderror"
                                id="broker" placeholder="Broker" name="broker" value="{{ old('broker') }}">

                            @error('broker')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- RM --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>RM</label>
                            <input type="text"
                                class="form-control form-control-customer @error('rm') is-invalid @enderror"
                                id="rm" placeholder="RM" name="rm" value="{{ old('rm') }}">

                            @error('rm')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- {{-- Customer Name --}}
                            <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                                <label>Customer Name</label>
                                <input type="text" class="form-control form-control-customer @error('customer_name') is-invalid @enderror" id="customer_name" placeholder="Customer Name" name="customer_name" value="{{ old('customer_name') }}">

                                @error('customer_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                            </div> -->

                        {{-- Company Name --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Company Name</label>
                            <input type="text"
                                class="form-control form-control-customer @error('company_name') is-invalid @enderror"
                                id="company_name" placeholder="Company Name" name="company_name"
                                value="{{ old('company_name') }}">

                            @error('company_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Type OF Policy --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>Type Of Policy</label>
                            <input type="text"
                                class="form-control form-control-customer @error('type_of_policy') is-invalid @enderror"
                                id="type_of_policy" placeholder="Type of policy" name="type_of_policy"
                                value="{{ old('type_of_policy') }}">

                            @error('type_of_policy')
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

                        {{-- RTO --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <label>RTO</label>
                            <input type="text"
                                class="form-control form-control-customer @error('rto') is-invalid @enderror"
                                id="rto" placeholder="RTO" name="rto" value="{{ old('rto') }}">

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
                            <label>Fuel Type</label>
                            <input type="text"
                                class="form-control form-control-customer @error('fuel_type') is-invalid @enderror"
                                id="fuel_type" placeholder="Fuel Type" name="fuel_type" value="{{ old('fuel_type') }}">

                            @error('fuel_type')
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

                        <!-- {{-- Mobile Number --}}
                            <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                                <label>Mobile Number</label>
                                <input type="text" class="form-control form-control-customer @error('mobile_number') is-invalid @enderror" id="exampleMobile" placeholder="Mobile Number" name="mobile_number" value="{{ old('mobile_number') }}">

                                @error('mobile_number')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                                <label>Email</label>
                                <input type="email" class="form-control form-control-customer @error('email') is-invalid @enderror" id="exampleEmail" placeholder="Email" name="email" value="{{ old('email') }}">

                                @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                            </div> -->

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

                        {{-- Status --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Status</label>
                            <select class="form-control form-control-customer @error('status') is-invalid @enderror"
                                name="status">
                                <option selected disabled>Select Status</option>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status')
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
                    <button type="submit" class="btn btn-success btn-customer float-right mb-3">Save</button>
                    <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('customers.index') }}">Cancel</a>
                </div>
            </form>
        </div>

    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('#date_of_birth').datepicker({
            format: 'yyyy-mm-dd',
        });

        $('#wedding_anniversary_date').datepicker({
            format: 'yyyy-mm-dd',
        });

        $('#engagement_anniversary_date').datepicker({
            format: 'yyyy-mm-dd',
        });
    </script>

@endsection
@section('stylesheets')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
