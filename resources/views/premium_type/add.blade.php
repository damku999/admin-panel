@extends('layouts.app')

@section('title', 'Add Premium Type')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Premium Type</h1>
            <a href="{{ route('premium_type.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                onclick="window.history.go(-1); return false;"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Premium Type</h6>
            </div>
            <form method="POST" action="{{ route('premium_type.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Name</label>
                            <input type="text"
                                class="form-control form-control-premium_type @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="Name" name="name" value="{{ old('name') }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span> Is it for Vehicle ?</label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input permission-input" type="radio" name="is_vehicle"
                                    value="1" id="vehicle_yes">
                                <label class="form-check-label" for="vehicle_yes">Yes</label>
                                &nbsp;
                                &nbsp;
                                <input class="form-check-input permission-input" type="radio" name="is_vehicle"
                                    value="0" checked id="vehicle_no">
                                <label class="form-check-label" for="vehicle_no">No</label>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span> Is Life Insurance Policies ? like(LIC,Endowment Plans, Term
                            plans, Ulip plans)</label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input permission-input" type="radio"
                                    name="is_life_insurance_policies" value="1" id="life_insurance_yes">
                                <label class="form-check-label" for="life_insurance_yes">Yes</label>
                                &nbsp;
                                &nbsp;
                                <input class="form-check-input permission-input" type="radio"
                                    name="is_life_insurance_policies" value="0" checked id="life_insurance_no">
                                <label class="form-check-label" for="life_insurance_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-premium_type float-right mb-3">Save</button>
                    <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('premium_type.index') }}"
                        onclick="window.history.go(-1); return false;">Cancel</a>
                </div>
            </form>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        const inputElements = document.querySelectorAll('input[type="text"]');
        const vehicleYesInput = document.getElementById('vehicle_yes');
        const vehicleNoInput = document.getElementById('vehicle_no');
        const lifeInsuranceYesInput = document.getElementById('life_insurance_yes');
        const lifeInsuranceNoInput = document.getElementById('life_insurance_no');

        function convertToUppercase(event) {
            const input = event.target;
            input.value = input.value.toUpperCase();
        }

        inputElements.forEach(input => {
            input.addEventListener('input', convertToUppercase);
        });

        function validateForm(event) {
            if (vehicleYesInput.checked && lifeInsuranceYesInput.checked) {
                alert("You cannot select 'Yes' for both options.");
                event.preventDefault();
            }
        }

        const form = document.querySelector('form');
        form.addEventListener('submit', validateForm);
    </script>
@endsection
