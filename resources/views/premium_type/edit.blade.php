@extends('layouts.app')

@section('title', 'Edit Premium Type')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header py-1">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Premium Type</h6>
                    <a href="{{ route('premium_type.index') }}" onclick="window.history.go(-1); return false;"
                        class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            <form method="POST" action="{{ route('premium_type.update', ['premium_type' => $premium_type->id]) }}">
                @csrf
                @method('PUT')
                <div class="card-body p-2">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif
                    
                    <div class="row g-2">
                        {{-- Name --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="name" class="form-label text-sm"><span class="text-danger">*</span>Name</label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                id="name" placeholder="Enter premium type name" name="name"
                                value="{{ old('name', $premium_type->name) }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        {{-- Is Vehicle --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label class="form-label text-sm"><span class="text-danger">*</span>Is it for Vehicle?</label>
                            <div class="form-check-container">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_vehicle"
                                        value="1" id="vehicle_yes" {{ old('is_vehicle', $premium_type->is_vehicle) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label text-sm" for="vehicle_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_vehicle"
                                        value="0" id="vehicle_no" {{ old('is_vehicle', $premium_type->is_vehicle) == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label text-sm" for="vehicle_no">No</label>
                                </div>
                            </div>
                            @error('is_vehicle')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Is Life Insurance --}}
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label class="form-label text-sm"><span class="text-danger">*</span>Is Life Insurance?</label>
                            <small class="text-muted d-block">LIC, Endowment Plans, Term plans, ULIP plans</small>
                            <div class="form-check-container">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_life_insurance_policies"
                                        value="1" id="life_insurance_yes" {{ old('is_life_insurance_policies', $premium_type->is_life_insurance_policies) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label text-sm" for="life_insurance_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_life_insurance_policies"
                                        value="0" id="life_insurance_no" {{ old('is_life_insurance_policies', $premium_type->is_life_insurance_policies) == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label text-sm" for="life_insurance_no">No</label>
                                </div>
                            </div>
                            @error('is_life_insurance_policies')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer py-1">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-secondary btn-sm mr-2" href="{{ route('premium_type.index') }}">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-save mr-1"></i>Update Premium Type
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Convert text inputs to uppercase
            const textInputs = document.querySelectorAll('input[type="text"]');
            textInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase();
                });
            });

            // Form validation
            const form = document.querySelector('form');
            const vehicleYesInput = document.getElementById('vehicle_yes');
            const lifeInsuranceYesInput = document.getElementById('life_insurance_yes');

            form.addEventListener('submit', function(event) {
                if (vehicleYesInput.checked && lifeInsuranceYesInput.checked) {
                    alert("You cannot select 'Yes' for both Vehicle and Life Insurance options.");
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
