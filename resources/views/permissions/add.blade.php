@extends('layouts.app')

@section('title', 'Add Permission')

@section('content')
<div class="container-fluid">

    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- Main Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-shield-alt me-2"></i>Add New Permission
            </h6>
            <a href="{{ route('permissions.index') }}" onclick="window.history.go(-1); return false;" 
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('permissions.store') }}">
                @csrf
                
                <!-- Permission Details Section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <span class="text-danger">*</span>Permission Name
                            </label>
                            <input type="text" 
                                   class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                   id="name"
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Enter permission name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="guard_name" class="form-label">
                                <span class="text-danger">*</span>Guard Name
                            </label>
                            <select class="form-select form-select-sm @error('guard_name') is-invalid @enderror" 
                                    name="guard_name" 
                                    id="guard_name"
                                    required>
                                <option value="" disabled selected>Select Guard Name</option>
                                <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>Web</option>
                                <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>API</option>
                            </select>
                            @error('guard_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Form Footer -->
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" form="permission-form" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Permission
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Move form tag outside to make it work with footer button
document.addEventListener('DOMContentLoaded', function() {
    // Find the form and give it an ID
    const form = document.querySelector('form');
    form.id = 'permission-form';
    
    // Make sure the submit button works
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            form.submit();
        });
    }
});
</script>

@endsection