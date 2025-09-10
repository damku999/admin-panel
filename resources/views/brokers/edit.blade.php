@extends('layouts.app')

@section('title', 'Edit Broker')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Broker Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Edit Broker</h6>
                <a href="{{ route('brokers.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form method="POST" action="{{ route('brokers.update', ['broker' => $broker->id]) }}">
                @csrf
                @method('PUT')
                <div class="card-body py-3">
                    <!-- Section: Broker Information -->
                    <div class="mb-3">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-user-tie me-2"></i>Broker Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Name</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    name="name" placeholder="Enter broker name" 
                                    value="{{ old('name') ? old('name') : $broker->name }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" placeholder="Enter email address" 
                                    value="{{ old('email') ? old('email') : $broker->email }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mobile Number</label>
                                <input type="text" class="form-control form-control-sm @error('mobile_number') is-invalid @enderror"
                                    name="mobile_number" placeholder="Enter mobile number" 
                                    value="{{ old('mobile_number') ? old('mobile_number') : $broker->mobile_number }}">
                                @error('mobile_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('brokers.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Update Broker
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

        function convertToUppercase(event) {
            const input = event.target;
            input.value = input.value.toUpperCase();
        }
        inputElements.forEach(input => {
            input.addEventListener('input', convertToUppercase);
        });
    </script>
@endsection