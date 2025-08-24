@extends('auth.layouts.app')

@section('title', 'Reset Password')

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
                                <h1 class="h4 text-gray-900 mb-4">Forgot Password?</h1>
                                <p class="mb-4 text-gray-600">Enter your email address and we'll send you a link to reset your password.</p>
                            </div>

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form method="POST" action="{{ route('customer.password.email') }}">
                                @csrf

                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror"
                                        id="exampleInputEmail" aria-describedby="emailHelp"
                                        placeholder="Enter Email Address..." name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Send Password Reset Link
                                </button>

                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('customer.login') }}">Back to Login</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">Admin Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection