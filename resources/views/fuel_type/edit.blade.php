@extends('layouts.app')

@section('title', 'Edit Fuel Type')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header py-1">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Fuel Type</h6>
                    <a href="{{ route('fuel_type.index') }}" onclick="window.history.go(-1); return false;"
                        class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            <form method="POST" action="{{ route('fuel_type.update', ['fuel_type' => $fuel_type->id]) }}">
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
                        <div class="col-md-6 col-sm-12 mb-1">
                            <label for="name" class="form-label text-sm"><span class="text-danger">*</span>Name</label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                id="name" placeholder="Enter fuel type name" name="name"
                                value="{{ old('name', $fuel_type->name) }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer py-1">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-secondary btn-sm mr-2" href="{{ route('fuel_type.index') }}">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-save mr-1"></i>Update Fuel Type
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
        });
    </script>
@endsection
