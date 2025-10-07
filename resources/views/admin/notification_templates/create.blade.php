@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create Notification Template</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('notification-templates.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="notification_type_id" class="form-label">Notification Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('notification_type_id') is-invalid @enderror"
                                    id="notification_type_id" name="notification_type_id" required>
                                <option value="">Select Notification Type</option>
                                @foreach($notificationTypes->groupBy('category') as $category => $types)
                                    <optgroup label="{{ ucwords($category) }}">
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ old('notification_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('notification_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="channel" class="form-label">Channel <span class="text-danger">*</span></label>
                                <select class="form-select @error('channel') is-invalid @enderror"
                                        id="channel" name="channel" required>
                                    <option value="">Select Channel</option>
                                    <option value="whatsapp" {{ old('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="both" {{ old('channel') === 'both' ? 'selected' : '' }}>Both</option>
                                </select>
                                @error('channel')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="subject" class="form-label">Email Subject</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                       id="subject" name="subject" value="{{ old('subject') }}">
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Required for email templates</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="template_content" class="form-label">Template Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('template_content') is-invalid @enderror"
                                      id="template_content" name="template_content" rows="8" required>{{ old('template_content') }}</textarea>
                            @error('template_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Use {variable_name} for dynamic content. Example: {customer_name}, {policy_number}</small>
                        </div>

                        <div class="mb-3">
                            <label for="available_variables" class="form-label">Available Variables (JSON)</label>
                            <textarea class="form-control @error('available_variables') is-invalid @enderror"
                                      id="available_variables" name="available_variables" rows="3">{{ old('available_variables') }}</textarea>
                            @error('available_variables')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Example: ["customer_name", "policy_number", "expiry_date"]</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('notification-templates.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Template</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
