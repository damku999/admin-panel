@extends('layouts.app')

@section('title', 'Edit Premium Type')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Premium Type Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Edit Premium Type</h6>
                <a href="{{ route('premium_type.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            <form method="POST" action="{{ route('premium_type.update', ['premium_type' => $premium_type->id]) }}">
                @csrf
                @method('PUT')
                <div class="card-body py-3">
                    <!-- Section: Premium Type Information -->
                    <div class="mb-3">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-percentage me-2"></i>Premium Type Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Name</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    name="name" placeholder="Enter premium type name" 
                                    value="{{ old('name') ? old('name') : $premium_type->name }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Is it for Vehicle?</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_vehicle" value="1" id="vehicle_yes"
                                            {{ (old('is_vehicle') ? old('is_vehicle') : $premium_type->is_vehicle) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vehicle_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_vehicle" value="0" id="vehicle_no"
                                            {{ (old('is_vehicle') ? old('is_vehicle') : $premium_type->is_vehicle) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vehicle_no">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Is Life Insurance Policy?</label>
                                <small class="text-muted d-block">LIC, Endowment Plans, Term Plans, ULIP Plans</small>
                                <div class="mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_life_insurance_policies" value="1" id="life_insurance_yes"
                                            {{ (old('is_life_insurance_policies') ? old('is_life_insurance_policies') : $premium_type->is_life_insurance_policies) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="life_insurance_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_life_insurance_policies" value="0" id="life_insurance_no"
                                            {{ (old('is_life_insurance_policies') ? old('is_life_insurance_policies') : $premium_type->is_life_insurance_policies) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="life_insurance_no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('premium_type.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Update Premium Type
                        </button>
                    </div>
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
                show_notification('error', \"You cannot select 'Yes' for both options.\");
                event.preventDefault();
            }
        }

        const form = document.querySelector('form');
        form.addEventListener('submit', validateForm);
    </script>
@endsection