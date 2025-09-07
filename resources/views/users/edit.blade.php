@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header py-1">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Edit User</h6>
                    <a href="{{ route('users.index') }}" onclick="window.history.go(-1); return false;"
                        class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            <form method="POST" action="{{ route('users.update', ['user' => $user->id]) }}">
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
                        {{-- First Name --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="first_name" class="form-label text-sm"><span class="text-danger">*</span>First Name</label>
                            <input type="text" class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                                id="first_name" placeholder="Enter first name" name="first_name"
                                value="{{ old('first_name', $user->first_name) }}">
                            @error('first_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="last_name" class="form-label text-sm"><span class="text-danger">*</span>Last Name</label>
                            <input type="text" class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                                id="last_name" placeholder="Enter last name" name="last_name"
                                value="{{ old('last_name', $user->last_name) }}">
                            @error('last_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="email" class="form-label text-sm"><span class="text-danger">*</span>Email</label>
                            <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                id="email" placeholder="Enter email address" name="email"
                                value="{{ old('email', $user->email) }}">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="password" class="form-label text-sm">Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror"
                                id="password" placeholder="Enter new password" name="password">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="confirm_password" class="form-label text-sm">Confirm Password</label>
                            <input type="password" class="form-control form-control-sm"
                                id="confirm_password" placeholder="Confirm new password" name="password_confirmation">
                        </div>

                        {{-- Role --}}
                        <div class="col-md-4 col-sm-6 mb-1">
                            <label for="role_id" class="form-label text-sm"><span class="text-danger">*</span>Role</label>
                            <select class="form-control form-control-sm @error('role_id') is-invalid @enderror" name="role_id">
                                <option value="" disabled>Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->roles->first()?->id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="card-footer py-1">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-secondary btn-sm mr-2" href="{{ route('users.index') }}">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-save mr-1"></i>Update User
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
                // Skip certain fields that shouldn't be uppercase
                if (!input.name.includes('email') && !input.name.includes('password')) {
                    input.addEventListener('input', function(e) {
                        e.target.value = e.target.value.toUpperCase();
                    });
                }
            });
        });
    </script>
@endsection