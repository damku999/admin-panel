@extends('layouts.app')

@section('title', 'Create App Setting')

@section('content')
<div class="container-fluid">

    {{-- Alert Messages --}}
    @include('common.alert')

    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 mb-1">
            <div class="card shadow mb-1">
                <div class="card-header py-1">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Create New Setting</h6>
                        <a href="{{ route('app-settings.index') }}" onclick="window.history.go(-1); return false;"
                            class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                
                <div class="card-body p-2">
                    <form method="POST" action="{{ route('app-settings.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="mb-1">
                                    <label for="key" class="form-label">Setting Key <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control form-control-sm @error('key') is-invalid @enderror" 
                                           id="key" 
                                           name="key" 
                                           value="{{ old('key') }}" 
                                           placeholder="e.g., whatsapp_api_token"
                                           required>
                                    @error('key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Use lowercase with underscores (snake_case)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="mb-1">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Select Category</option>
                                        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General</option>
                                        <option value="whatsapp" {{ old('category') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="mail" {{ old('category') === 'mail' ? 'selected' : '' }}>Mail</option>
                                        <option value="api" {{ old('category') === 'api' ? 'selected' : '' }}>API</option>
                                        <option value="application" {{ old('category') === 'application' ? 'selected' : '' }}>Application</option>
                                        <option value="notifications" {{ old('category') === 'notifications' ? 'selected' : '' }}>Notifications</option>
                                        @foreach($categories as $cat)
                                            @if(!in_array($cat, ['general', 'whatsapp', 'mail', 'api', 'application', 'notifications']))
                                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                                                    {{ ucfirst($cat) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8 col-sm-12 mb-1">
                                <div class="mb-1">
                                    <label for="value" class="form-label">Value</label>
                                    <textarea class="form-control form-control-sm @error('value') is-invalid @enderror" 
                                              id="value" 
                                              name="value" 
                                              rows="3" 
                                              placeholder="Enter the setting value">{{ old('value') }}</textarea>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 mb-1">
                                <div class="mb-1">
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="string" {{ old('type') === 'string' ? 'selected' : '' }}>String</option>
                                        <option value="numeric" {{ old('type') === 'numeric' ? 'selected' : '' }}>Numeric</option>
                                        <option value="boolean" {{ old('type') === 'boolean' ? 'selected' : '' }}>Boolean</option>
                                        <option value="json" {{ old('type') === 'json' ? 'selected' : '' }}>JSON</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="2" 
                                      placeholder="Describe what this setting is for">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_encrypted" 
                                           name="is_encrypted" 
                                           value="1"
                                           {{ old('is_encrypted') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_encrypted">
                                        <i class="fas fa-lock"></i> Encrypt this value
                                    </label>
                                    <small class="text-muted d-block">Check this for sensitive data like API keys, passwords, etc.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <i class="fas fa-toggle-on"></i> Active
                                    </label>
                                    <small class="text-muted d-block">Inactive settings will not be accessible via the service</small>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="card-footer p-2">
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="{{ route('app-settings.index') }}" class="btn btn-secondary btn-sm mr-2">Cancel</a>
                        <button type="submit" form="1" class="btn btn-success btn-sm">Create Setting</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Auto-format key input
    $('#key').on('input', function() {
        let value = $(this).val().toLowerCase().replace(/[^a-z0-9_]/g, '_').replace(/_+/g, '_');
        $(this).val(value);
    });
    
    // Show/hide value field based on type
    $('#type').change(function() {
        const type = $(this).val();
        const valueField = $('#value');
        
        if (type === 'boolean') {
            valueField.val('').attr('placeholder', 'true or false');
        } else if (type === 'json') {
            valueField.attr('placeholder', '{"key": "value"}');
        } else if (type === 'numeric') {
            valueField.attr('placeholder', '123 or 123.45');
        } else {
            valueField.attr('placeholder', 'Enter the setting value');
        }
    });
});
</script>
@endpush