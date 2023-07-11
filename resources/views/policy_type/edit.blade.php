@extends('layouts.app')

@section('title', 'Edit Policy Type')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Policy Type</h1>
            <a href="{{ route('policy_type.index') }}" onclick="window.history.go(-1); return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Policy Type</h6>
            </div>
            <form method="POST" action="{{ route('policy_type.update', ['policy_type' => $policy_type->id]) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group row">

                        {{-- First Name --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            Name</label>
                            <input type="text"
                                class="form-control form-control-policy_type @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="Name" name="name"
                                value="{{ old('name') ? old('name') : $policy_type->name }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-policy_type float-right mb-3">Update</button>
                        <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('policy_type.index') }}">Cancel</a>
                    </div>
            </form>
        </div>

    </div>

@endsection
