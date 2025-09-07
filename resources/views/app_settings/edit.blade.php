@extends('layouts.app')

@section('title', 'Edit App Setting')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header py-1">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Edit App Setting</h6>
                    <a href="{{ route('app-settings.index') }}" onclick="window.history.go(-1); return false;"
                        class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            <form method="POST" action="{{ route('app-settings.update', $appSetting) }}">
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
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-12 mb-1">
                            <label for="key" class="form-label">Setting Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('key') is-invalid @enderror"
                                id="key" name="key" value="{{ old('key', $appSetting->key) }}" 
                                placeholder="e.g., whatsapp_api_token" required>
                            @error('key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Use lowercase with underscores (snake_case)</small>
                        </div>
                        
                        <div class="col-md-6 col-sm-12 mb-1">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm @error('category') is-invalid @enderror" 
                                    id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="general" {{ old('category', $appSetting->category) === 'general' ? 'selected' : '' }}>General</option>
                                <option value="whatsapp" {{ old('category', $appSetting->category) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="mail" {{ old('category', $appSetting->category) === 'mail' ? 'selected' : '' }}>Mail</option>
                                <option value="api" {{ old('category', $appSetting->category) === 'api' ? 'selected' : '' }}>API</option>
                                <option value="application" {{ old('category', $appSetting->category) === 'application' ? 'selected' : '' }}>Application</option>
                                <option value="notifications" {{ old('category', $appSetting->category) === 'notifications' ? 'selected' : '' }}>Notifications</option>
                                @foreach($categories as $cat)
                                    @if(!in_array($cat, ['general', 'whatsapp', 'mail', 'api', 'application', 'notifications']))
                                        <option value="{{ $cat }}" {{ old('category', $appSetting->category) === $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('category')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 col-sm-12 mb-1">
                            <label for="value" class="form-label">
                                Value 
                                @if($appSetting->is_encrypted)
                                    <span class="badge badge-warning ml-2">Encrypted</span>
                                @endif
                            </label>
                            <textarea class="form-control form-control-sm @error('value') is-invalid @enderror" 
                                      id="value" name="value" rows="3" 
                                      placeholder="Enter the setting value">{{ old('value', $appSetting->is_encrypted ? '' : $appSetting->value) }}</textarea>
                            @error('value')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @if($appSetting->is_encrypted)
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Leave empty to keep current encrypted value, or enter new value to update
                                </small>
                            @endif
                        </div>
                        
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="string" {{ old('type', $appSetting->type) === 'string' ? 'selected' : '' }}>String</option>
                                <option value="numeric" {{ old('type', $appSetting->type) === 'numeric' ? 'selected' : '' }}>Numeric</option>
                                <option value="boolean" {{ old('type', $appSetting->type) === 'boolean' ? 'selected' : '' }}>Boolean</option>
                                <option value="json" {{ old('type', $appSetting->type) === 'json' ? 'selected' : '' }}>JSON</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-1">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2" 
                                      placeholder="Describe what this setting is for">{{ old('description', $appSetting->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 col-sm-12 mb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_encrypted" 
                                       name="is_encrypted" value="1"
                                       {{ old('is_encrypted', $appSetting->is_encrypted) ? 'checked' : '' }}>
                                <label class="form-check-label text-sm" for="is_encrypted">
                                    <i class="fas fa-lock"></i> Encrypt this value
                                </label>
                                <small class="text-muted d-block">Check this for sensitive data like API keys, passwords, etc.</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-sm-12 mb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1"
                                       {{ old('is_active', $appSetting->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label text-sm" for="is_active">
                                    <i class="fas fa-toggle-on"></i> Active
                                </label>
                                <small class="text-muted d-block">Inactive settings will not be accessible via the service</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer p-2">
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="{{ route('app-settings.index') }}" class="btn btn-secondary btn-sm mr-2">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">Update Setting</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-format key input
            const keyInput = document.getElementById('key');
            keyInput.addEventListener('input', function() {
                let value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '_').replace(/_+/g, '_');
                this.value = value;
            });
            
            // Show/hide value field based on type
            const typeSelect = document.getElementById('type');
            const valueField = document.getElementById('value');
            
            typeSelect.addEventListener('change', function() {
                const type = this.value;
                
                if (type === 'boolean') {
                    valueField.setAttribute('placeholder', 'true or false');
                } else if (type === 'json') {
                    valueField.setAttribute('placeholder', '{"key": "value"}');
                } else if (type === 'numeric') {
                    valueField.setAttribute('placeholder', '123 or 123.45');
                } else {
                    valueField.setAttribute('placeholder', 'Enter the setting value');
                }
            });
        });
    </script>
@endsection