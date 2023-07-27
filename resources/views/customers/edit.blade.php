@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Customers</h1>
            <a href="{{ route('customers.index') }}" onclick="window.history.go(-1); return false;"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Customer</h6>
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
            <form method="POST" action="{{ route('customers.update', ['customer' => $customer->id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group row">

                        {{-- First Name --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
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
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
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
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Mobile Number</label>
                            <input type="text"
                                class="form-control form-control-customer @error('mobile_number') is-invalid @enderror"
                                id="exampleMobile" placeholder="Mobile Number" name="mobile_number"
                                value="{{ old('mobile_number') ? old('mobile_number') : $customer->mobile_number }}">

                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Customer Type</label>
                            <select class="form-control form-control-customer @error('type') is-invalid @enderror"
                                name="type" id="customerType">
                                <option selected disabled>Select Customer Type</option>
                                <option value="Retail" @if (old('type', $customer->type) == 'Retail') selected @endif>Retail</option>
                                <option value="Corporate" @if (old('type', $customer->type) == 'Corporate') selected @endif>Corporate
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
                                value="{{ old('pan_card_number', $customer->pan_card_number) }}">
                            @error('pan_card_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Pan Card Document --}}

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="gst_path">Pan Card Document</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input @error('pan_card_path') is-invalid @enderror"
                                        id="pan_card_path" placeholder="GST Document" name="pan_card_path"
                                        value="{{ old('pan_card_path') }}">
                                    <label class="custom-file-label" for="pan_card_path">Choose file</label>
                                </div>
                                <div class="input-group-append">
                                    @if ($customer->pan_card_path)
                                        <a href="{{ asset('storage/' . $customer->pan_card_path) }}"
                                            class="btn btn-primary" target="__blank">Download</a>
                                    @endif
                                </div>
                            </div>
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
                                value="{{ old('aadhar_card_number', $customer->aadhar_card_number) }}">
                            @error('aadhar_card_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Aadhar Card Document --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="aadhar_card_path">Aadhar Card Document</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input @error('aadhar_card_path') is-invalid @enderror"
                                        id="aadhar_card_path" placeholder="Aadhar Card Document" name="aadhar_card_path"
                                        value="{{ old('aadhar_card_path') }}">
                                    <label class="custom-file-label" for="aadhar_card_path">Choose file</label>
                                </div>
                                <div class="input-group-append">
                                    @if ($customer->aadhar_card_path)
                                        <a href="{{ asset('storage/' . $customer->aadhar_card_path) }}"
                                            class="btn btn-primary" target="__blank">Download</a>
                                    @endif
                                </div>
                            </div>
                            @error('aadhar_card_path')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GST Number --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="gst_number">GST Number</label>
                            <input type="text"
                                class="form-control form-control-customer @error('gst_number') is-invalid @enderror"
                                id="gst_number" placeholder="GST Number" name="gst_number"
                                value="{{ old('gst_number', $customer->gst_number) }}">
                            @error('gst_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- GST Document --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="gst_path">GST Document</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input @error('gst_path') is-invalid @enderror" id="gst_path"
                                        placeholder="GST Document" name="gst_path" value="{{ old('gst_path') }}">
                                    <label class="custom-file-label" for="gst_path">Choose file</label>
                                </div>
                                <div class="input-group-append">
                                    @if ($customer->gst_path)
                                        <a href="{{ asset('storage/' . $customer->gst_path) }}" class="btn btn-primary"
                                            target="__blank">Download</a>
                                    @endif
                                </div>
                            </div>
                            @error('gst_path')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Status</label>
                            <select class="form-control form-control-customer @error('status') is-invalid @enderror"
                                name="status">
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
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Date Of Birth</label>
                            <div class="input-group date" id="datepicker">
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth"
                                    value="{{ old('date_of_birth') ? old('date_of_birth') : $customer->date_of_birth }}" />
                            </div>
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date Of Engagement Anniversary --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Date Of Engagement Anniversary</label>
                            <div class="input-group date" id="datepicker">
                                <input type="date"
                                    class="form-control @error('engagement_anniversary_date') is-invalid @enderror"
                                    id="engagement_anniversary_date" name="engagement_anniversary_date"
                                    value="{{ old('engagement_anniversary_date') ? old('engagement_anniversary_date') : $customer->engagement_anniversary_date }}" />
                            </div>
                            @error('engagement_anniversary_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date Of Wedding Anniversary --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Date Of Wedding Anniversary</label>
                            <div class="input-group date" id="datepicker">
                                <input type="date"
                                    class="form-control @error('wedding_anniversary_date') is-invalid @enderror"
                                    id="wedding_anniversary_date" name="wedding_anniversary_date"
                                    value="{{ old('wedding_anniversary_date') ? old('wedding_anniversary_date') : $customer->wedding_anniversary_date }}" />
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
        <div class="card-body table-responsive p-0">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>Added Date</th>
                        <th>Issue Date</th>
                        <th>Expired Date</th>
                        <th>Policy Number</th>
                        <th>Registration Number</th>
                        <th>Premium Type</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($customer_insurances))
                        @foreach ($customer_insurances as $customer_insurance)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($customer_insurance->created_at)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($customer_insurance->issue_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($customer_insurance->expired_date)->format('d/m/Y') }}</td>
                                <td>{{ $customer_insurance->policy_no }}</td>
                                <td>{{ $customer_insurance->registration_no }}</td>
                                <td>{{ $customer_insurance->premiumType->name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('customer_insurances.edit', ['customer_insurance' => $customer_insurance->id]) }}"
                                        class="btn btn-primary m-2">
                                        <i class="fa fa-pen"></i>
                                    </a> &nbsp;
                                    @if ($customer_insurance->status == 0)
                                        <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 1]) }}"
                                            class="btn btn-success m-2">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    @elseif ($customer_insurance->status == 1)
                                        <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 0]) }}"
                                            class="btn btn-danger m-2">
                                            <i class="fa fa-ban"></i>
                                        </a>
                                    @endif
                                    @if ($customer_insurance->policy_document_path)
                                        <a href="{{ asset('storage/' . $customer_insurance->policy_document_path) }}"
                                            class="btn btn-info m-2" target="__blank"><i class="fa fa-download"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="center">No Record found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
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
