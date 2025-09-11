@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- User Edit Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Edit User</h6>
                <a href="{{ route('users.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            <form method="POST" action="{{ route('users.update', ['user' => $user->id]) }}">
                @csrf
                @method('PUT')

                <div class="card-body py-3">
                    <!-- Section 1: Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-user me-2"></i>Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> First Name</label>
                                <input type="text" class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                                    name="first_name" placeholder="Enter first name" value="{{ old('first_name', $user->first_name) }}">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Last Name</label>
                                <input type="text" class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                                    name="last_name" placeholder="Enter last name" value="{{ old('last_name', $user->last_name) }}">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Email</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" placeholder="Enter email address" value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Contact & Role Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-cogs me-2"></i>Contact & Role Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Mobile Number</label>
                                <input type="text" class="form-control form-control-sm @error('mobile_number') is-invalid @enderror"
                                    name="mobile_number" placeholder="Enter mobile number" value="{{ old('mobile_number', $user->mobile_number) }}">
                                @error('mobile_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Role</label>
                                <select class="form-select form-select-sm @error('role_id') is-invalid @enderror" name="role_id">
                                    <option selected disabled>Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" 
                                            @if (old('role_id', $user->role_id) == $role->id) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Status</label>
                                <select class="form-select form-select-sm @error('status') is-invalid @enderror" name="status">
                                    <option value="1" @if (old('status', $user->status) == 1) selected @endif>Active</option>
                                    <option value="0" @if (old('status', $user->status) == 0) selected @endif>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Password Information -->
                    <div class="mb-3">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-lock me-2"></i>Change Password (Optional)</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" class="form-control form-control-sm @error('new_password') is-invalid @enderror"
                                    name="new_password" placeholder="Enter new password" autocomplete="new-password">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Confirm New Password</label>
                                <input type="password" class="form-control form-control-sm @error('new_confirm_password') is-invalid @enderror"
                                    name="new_confirm_password" placeholder="Confirm new password" autocomplete="new-password">
                                @error('new_confirm_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <!-- Empty column for consistent 3-column layout -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('users.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

@endsection