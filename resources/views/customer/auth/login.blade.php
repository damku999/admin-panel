@extends('auth.layouts.app')

@section('title', 'Customer Login')

@section('content')
    <div class="row justify-content-center">

        <div class="text-center mt-5">
            <img src="{{ asset('images/parth_logo.png') }}" style="max-width: 50%;">
            <h2 class="text-white mt-3">Customer Portal</h2>
        </div>

        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        {{-- <div class="col-lg-6 d-none d-lg-block bg-login-image">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 p-3">Test Login Credentials
                            </div>
                            <div class="text-dark p-3">
                                <strong>Family Head:</strong> damku999@gmail.com / 5B8HT6SO<br>
                                <strong>Family Head:</strong> rajesh.sharma@example.com / password123<br>
                                <strong>Family Head:</strong> amit.kumar@example.com / password123<br>
                                <strong>Family Member:</strong> priya.sharma@example.com / password123
                            </div>
                        </div> --}}
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Customer Login</h1>
                                    <p class="mb-4 text-gray-600">Access your family insurance policies</p>
                                </div>

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                @if (session('message'))
                                    <div class="alert alert-success">{{ session('message') }}</div>
                                @endif

                                <form method="POST" action="{{ route('customer.login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                            id="exampleInputEmail" aria-describedby="emailHelp"
                                            placeholder="Enter Email Address..." name="email" value="{{ old('email') }}"
                                            required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="password"
                                            class="form-control form-control-user @error('password') is-invalid @enderror"
                                            id="exampleInputPassword" placeholder="Password" name="password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="customCheck"
                                                name="remember">
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login to Customer Portal
                                    </button>

                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="{{ route('customer.password.request') }}">Forgot Password?</a>
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
