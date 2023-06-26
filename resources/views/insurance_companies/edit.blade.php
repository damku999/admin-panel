@extends('layouts.app')

@section('title', 'Edit Relationship Manager')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Insurance Companies</h1>
            <a href="{{ route('insurance_companies.index') }}"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Relationship Manager</h6>
            </div>
            <form method="POST"
                action="{{ route('insurance_companies.update', ['insurance_company' => $insurance_company->id]) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group row">

                        {{-- First Name --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            Name</label>
                            <input type="text"
                                class="form-control form-control-insurance_company @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="Name" name="name"
                                value="{{ old('name') ? old('name') : $insurance_company->name }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            Email</label>
                            <input type="email"
                                class="form-control form-control-insurance_company @error('email') is-invalid @enderror"
                                id="exampleEmail" placeholder="Email" name="email"
                                value="{{ old('email') ? old('email') : $insurance_company->email }}">

                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            Mobile Number</label>
                            <input type="text"
                                class="form-control form-control-insurance_company @error('mobile_number') is-invalid @enderror"
                                id="exampleMobile" placeholder="Mobile Number" name="mobile_number"
                                value="{{ old('mobile_number') ? old('mobile_number') : $insurance_company->mobile_number }}">

                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit"
                            class="btn btn-success btn-insurance_company float-right mb-3">Update</button>
                        <a class="btn btn-primary float-right mr-3 mb-3"
                            href="{{ route('insurance_companies.index') }}">Cancel</a>
                    </div>
            </form>
        </div>

    </div>

@endsection
