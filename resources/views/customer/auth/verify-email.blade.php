@extends('auth.layouts.app')

@section('title', 'Verify Email')

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
                                <h1 class="h4 text-gray-900 mb-4">Verify Your Email</h1>
                                <p class="mb-4 text-gray-600">Please verify your email address to continue</p>
                            </div>

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @if (session('info'))
                                <div class="alert alert-info">{{ session('info') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <div class="card border-left-info">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Verification Required
                                            </div>
                                            <div class="text-dark">
                                                <p>We have sent a verification link to your email address:</p>
                                                <strong>{{ $customer->email }}</strong>
                                                <p class="mt-2">Please check your email and click the verification link to verify your account.</p>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <form method="POST" action="{{ route('customer.verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-user btn-block">
                                        Resend Verification Email
                                    </button>
                                </form>
                            </div>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('customer.dashboard') }}">Back to Dashboard</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ route('customer.login') }}">Login Again</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection