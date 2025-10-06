@extends('layouts.app')

@section('title', 'Add App Setting')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- App Setting Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Add New App Setting</h6>
                <a href="{{ route('app-settings.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            <form method="POST" action="{{ route('app-settings.store') }}">
                @csrf
                <div class="card-body py-3">
                    <!-- Section: Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-cog me-2"></i>Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Key</label>
                                <input type="text" class="form-control form-control-sm @error('key') is-invalid @enderror"
                                    name="key" placeholder="e.g., whatsapp.api_token" value="{{ old('key') }}">
                                <small class="text-muted">Unique identifier for this setting (use dot notation for grouping)</small>
                                @error('key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Category</label>
                                <select class="form-control form-control-sm @error('category') is-invalid @enderror"
                                    name="category">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Value Configuration -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-database me-2"></i>Value Configuration</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Type</label>
                                <select class="form-control form-control-sm @error('type') is-invalid @enderror"
                                    name="type" id="type">
                                    <option value="">Select Type</option>
                                    @foreach($types as $key => $value)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Data type for the value</small>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Value</label>
                                <textarea class="form-control form-control-sm @error('value') is-invalid @enderror"
                                    name="value" rows="3" id="value" placeholder="Enter setting value">{{ old('value') }}</textarea>
                                <small class="text-muted" id="value-hint">Enter the setting value</small>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Description and Options -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Description and Options</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                                    name="description" rows="3" placeholder="Describe the purpose of this setting">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Security and Status -->
                    <div class="mb-3">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-shield-alt me-2"></i>Security and Status</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_encrypted" id="is_encrypted"
                                        value="1" {{ old('is_encrypted') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_encrypted">
                                        <i class="fas fa-lock text-warning me-1"></i> Encrypt Value
                                    </label>
                                    <small class="d-block text-muted">Enable encryption for sensitive data (API keys, passwords, tokens)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_active">
                                        Active
                                    </label>
                                    <small class="d-block text-muted">Only active settings are used by the application</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('app-settings.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Save Setting
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
        const typeSelect = document.getElementById('type');
        const valueInput = document.getElementById('value');
        const valueHint = document.getElementById('value-hint');

        typeSelect.addEventListener('change', function() {
            const type = this.value;
            switch(type) {
                case 'json':
                    valueHint.textContent = 'Enter JSON data (e.g., {"key": "value"})';
                    valueInput.placeholder = '{"key": "value"}';
                    break;
                case 'boolean':
                    valueHint.textContent = 'Enter true or false (or 1/0)';
                    valueInput.placeholder = 'true';
                    break;
                case 'numeric':
                    valueHint.textContent = 'Enter numeric value (integer or decimal)';
                    valueInput.placeholder = '123';
                    break;
                default:
                    valueHint.textContent = 'Enter the setting value';
                    valueInput.placeholder = 'Enter setting value';
            }
        });
    });
</script>
@endsection
