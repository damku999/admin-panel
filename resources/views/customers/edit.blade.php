@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Customers</h1>
            <a href="{{ route('customers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Customer</h6>
            </div>
            <form method="POST" action="{{ route('customers.update', ['customer' => $customer->id]) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group row">

                        {{-- First Name --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>First Name</label>
                            <input type="text"
                                class="form-control form-control-customer @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="First Name" name="name"
                                value="{{ old('name') ? old('name') : $customer->name }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        {{-- Email --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Email</label>
                            <input type="email"
                                class="form-control form-control-customer @error('email') is-invalid @enderror"
                                id="exampleEmail" placeholder="Email" name="email"
                                value="{{ old('email') ? old('email') : $customer->email }}">

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
                                value="{{ old('mobile_number') ? old('mobile_number') : $customer->mobile_number }}">

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
                                <option value="1"
                                    {{ old('role_id') ? (old('role_id') == 1 ? 'selected' : '') : ($customer->status == 1 ? 'selected' : '') }}>
                                    Active</option>
                                <option value="0"
                                    {{ old('role_id') ? (old('role_id') == 0 ? 'selected' : '') : ($customer->status == 0 ? 'selected' : '') }}>
                                    Inactive</option>
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
                                    id="date_of_birth" name="date_of_birth"
                                    value="{{ old('date_of_birth') ? old('date_of_birth') : $customer->date_of_birth }}" />
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
                                    value="{{ old('engagement_anniversary_date') ? old('engagement_anniversary_date') : $customer->engagement_anniversary_date }}" />
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
                                    value="{{ old('wedding_anniversary_date') ? old('wedding_anniversary_date') : $customer->wedding_anniversary_date }}" />
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

                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-customer float-right mb-3">Update</button>
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
