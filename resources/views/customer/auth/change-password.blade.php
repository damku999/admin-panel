@extends('layouts.customer')

@section('title', 'Change Password')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="compact-card">
                    <div class="text-center mb-3">
                        <h5 class="mb-1">Change Password</h5>
                        <p class="text-muted small mb-0">Update your account security</p>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('customer.change-password.update') }}">
                        @csrf

                        <div class="mb-3">
                            <input type="password" 
                                class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" 
                                name="current_password" 
                                placeholder="Current password"
                                required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="password" 
                                class="form-control @error('password') is-invalid @enderror"
                                id="password" 
                                name="password" 
                                placeholder="New password"
                                required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <input type="password" 
                                class="form-control"
                                id="password_confirmation" 
                                name="password_confirmation" 
                                placeholder="Confirm new password"
                                required>
                        </div>

                        <button type="submit" class="btn btn-webmonks w-100 mb-3">
                            Update Password
                        </button>
                    </form>

                    <div class="text-center">
                        <small class="text-muted">Minimum 8 characters with letters and numbers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection