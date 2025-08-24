@extends('auth.layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="row justify-content-center">

    <div class="text-center mt-5">
        <img src="{{ asset('images/parth_logo.png') }}" style="max-width: 50%;">
        <h2 class="text-white mt-3">Customer Portal</h2>
    </div>

    <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Change Password</h1>
                                <p class="mb-4 text-gray-600">Please change your password to continue</p>
                            </div>

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @if (session('warning'))
                                <div class="alert alert-warning">{{ session('warning') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form method="POST" action="{{ route('customer.change-password.update') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="current_password" class="small mb-1">Current Password</label>
                                    <input type="password" class="form-control form-control-user @error('current_password') is-invalid @enderror"
                                        id="current_password" placeholder="Enter Current Password" 
                                        name="current_password" required>
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password" class="small mb-1">New Password</label>
                                    <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror"
                                        id="password" placeholder="Enter New Password" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Password must be at least 8 characters long.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation" class="small mb-1">Confirm New Password</label>
                                    <input type="password" class="form-control form-control-user"
                                        id="password_confirmation" placeholder="Confirm New Password" 
                                        name="password_confirmation" required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Change Password
                                </button>

                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('customer.dashboard') }}">Back to Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection