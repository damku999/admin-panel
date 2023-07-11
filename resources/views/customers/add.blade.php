@extends('layouts.app')

@section('title', 'Add Customers')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Customers</h1>
            <a href="{{ route('customers.index') }}" onclick="window.history.go(-1); return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Customer</h6>
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
            <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Name</label>
                            <input type="text"
                                class="form-control form-control-customer @error('name') is-invalid @enderror"
                                id="FirstName" placeholder="Name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Email</label>
                            <input type="email"
                                class="form-control form-control-customer @error('email') is-invalid @enderror"
                                id="Email" placeholder="Email" name="email" value="{{ old('email') }}">

                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Mobile Number</label>
                            <input type="text"
                                class="form-control form-control-customer @error('mobile_number') is-invalid @enderror"
                                id="Mobile" placeholder="Mobile Number" name="mobile_number"
                                value="{{ old('mobile_number') }}">

                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Customer Type</label>
                            <select class="form-control form-control-customer @error('type') is-invalid @enderror"
                                name="type" id="customerType">
                                <option value="Retail" @if (old('type') == 'Retail') selected @endif>Retail</option>
                                <option value="Corporate" @if (old('type') == 'Corporate') selected @endif>Corporate
                                </option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Pan Card Number --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="pan_card_number">Pan Card Number</label>
                            <input type="text"
                                class="form-control form-control-customer @error('pan_card_number') is-invalid @enderror"
                                id="pan_card_number" placeholder="Pan Card Number" name="pan_card_number"
                                value="{{ old('pan_card_number') }}">
                            @error('pan_card_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Pan Card Document --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="pan_card_path">Pan Card Document</label>
                            <input type="file"
                                class="form-control form-control-customer @error('pan_card_path') is-invalid @enderror"
                                id="pan_card_path" placeholder="Pan Card Document" name="pan_card_path"
                                value="{{ old('pan_card_path') }}">
                            @error('pan_card_path')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Aadhar Card Number --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="aadhar_card_number">Aadhar Card Number</label>
                            <input type="text"
                                class="form-control form-control-customer @error('aadhar_card_number') is-invalid @enderror"
                                id="aadhar_card_number" placeholder="Aadhar Card Number" name="aadhar_card_number"
                                value="{{ old('aadhar_card_number') }}">
                            @error('aadhar_card_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Aadhar Card Document --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="aadhar_card_path">Aadhar Card Document</label>
                            <input type="file"
                                class="form-control form-control-customer @error('aadhar_card_path') is-invalid @enderror"
                                id="aadhar_card_path" placeholder="Aadhar Card Document" name="aadhar_card_path"
                                value="{{ old('aadhar_card_path') }}">
                            @error('aadhar_card_path')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GST Number --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0" id="gstNumberSection">
                            <label for="gst_number">GST Number</label>
                            <input type="text"
                                class="form-control form-control-customer @error('gst_number') is-invalid @enderror"
                                id="gst_number" placeholder="GST Number" name="gst_number" value="{{ old('gst_number') }}">
                            @error('gst_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- GST Document --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0" id="gstDocumentSection">
                            <label for="gst_path">GST Document</label>
                            <input type="file"
                                class="form-control form-control-customer @error('gst_path') is-invalid @enderror"
                                id="gst_path" placeholder="GST Document" name="gst_path"
                                value="{{ old('gst_path') }}">
                            @error('gst_path')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
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
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            Date Of Birth</label>
                            <div class="input-group date" id="datepicker">
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" />
                                </span>
                            </div>
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date Of Engagement Anniversary --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            Date Of Engagement Anniversary</label>
                            <div class="input-group date" id="datepicker">
                                <input type="date"
                                    class="form-control @error('engagement_anniversary_date') is-invalid @enderror"
                                    id="engagement_anniversary_date" name="engagement_anniversary_date"
                                    value="{{ old('engagement_anniversary_date') }}" />
                            </div>
                            @error('engagement_anniversary_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date Of Wedding Anniversary --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            Date Of Wedding Anniversary</label>
                            <div class="input-group date" id="datepicker">
                                <input type="date"
                                    class="form-control @error('wedding_anniversary_date') is-invalid @enderror"
                                    id="wedding_anniversary_date" name="wedding_anniversary_date"
                                    value="{{ old('wedding_anniversary_date') }}" />
                            </div>
                            @error('wedding_anniversary_date')
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
    <script>
        // Get the customer type select element
        const customerTypeSelect = document.getElementById('customerType');

        // Get the sections related to GST
        const gstNumberSection = document.getElementById('gstNumberSection');
        const gstDocumentSection = document.getElementById('gstDocumentSection');

        // Function to hide the sections
        const hideSections = () => {
            gstNumberSection.style.display = 'none';
            gstDocumentSection.style.display = 'none';
        };

        // Function to show the sections
        const showSections = () => {
            const customerType = customerTypeSelect.value;
            if (customerType === 'Corporate') {
                gstNumberSection.style.display = 'block';
                gstDocumentSection.style.display = 'block';
            }
        };

        // Hide all sections initially
        hideSections();

        // Show sections based on the selected customer type
        showSections();

        // Event listener for customer type change
        customerTypeSelect.addEventListener('change', () => {
            hideSections();
            showSections();
        });
    </script>
@endsection
@section('stylesheets')
@endsection
