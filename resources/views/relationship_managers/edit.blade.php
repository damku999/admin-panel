@extends('layouts.app')

@section('title', 'Edit Relationship Manager')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header py-1">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Relationship Manager</h6>
                    <a href="{{ route('relationship_managers.index') }}" onclick="window.history.go(-1); return false;"
                        class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            <form method="POST" action="{{ route('relationship_managers.update', ['relationship_manager' => $relationship_manager->id]) }}">
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
                                id="name" placeholder="Enter relationship manager name" name="name"
                                value="{{ old('name', $relationship_manager->name) }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="email" class="form-label text-sm">Email</label>
                            <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                id="email" placeholder="Enter email address" name="email"
                                value="{{ old('email', $relationship_manager->email) }}">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="mobile_number" class="form-label text-sm">Mobile Number</label>
                            <input type="text" class="form-control form-control-sm @error('mobile_number') is-invalid @enderror"
                                id="mobile_number" placeholder="Enter mobile number" name="mobile_number"
                                value="{{ old('mobile_number', $relationship_manager->mobile_number) }}" maxlength="10">
                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer py-1">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-secondary btn-sm mr-2" href="{{ route('relationship_managers.index') }}">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-save mr-1"></i>Update Relationship Manager
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
            // Convert text inputs to uppercase (skip email)
            const textInputs = document.querySelectorAll('input[type="text"]');
            textInputs.forEach(input => {
                if (!input.name.includes('email')) {
                    input.addEventListener('input', function(e) {
                        e.target.value = e.target.value.toUpperCase();
                    });
                }
            });
        });
    </script>
@endsection
