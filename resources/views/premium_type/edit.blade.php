@extends('layouts.app')

@section('title', 'Edit Premium Type')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Premium Type</h1>
            <a href="{{ route('premium_type.index') }}" onclick="window.history.go(-1); return false;"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                onclick="window.history.go(-1); return false;"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Premium Type</h6>
            </div>
            <form method="POST" action="{{ route('premium_type.update', ['premium_type' => $premium_type->id]) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group row">

                        {{-- First Name --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            Name</label>
                            <input type="text"
                                class="form-control form-control-premium_type @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="Name" name="name"
                                value="{{ old('name') ? old('name') : $premium_type->name }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span> Is it for Vehicle ?</label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input permission-input" type="radio" name="is_vehicle"
                                    value="1"
                                    {{ (old('is_vehicle') ? old('is_vehicle') : $premium_type->is_vehicle) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="inlineCheckbox">Yes</label>
                                &nbsp;
                                &nbsp;
                                <input class="form-check-input permission-input" type="radio" name="is_vehicle"
                                    value="0"
                                    {{ (old('is_vehicle') ? old('is_vehicle') : $premium_type->is_vehicle) == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="inlineCheckbox">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-premium_type float-right mb-3">Update</button>
                        <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('premium_type.index') }}"
                            onclick="window.history.go(-1); return false;">Cancel</a>
                    </div>
            </form>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        const inputElements = document.querySelectorAll('input[type="text"]');

        function convertToUppercase(event) {
            const input = event.target;
            input.value = input.value.toUpperCase();
        }
        inputElements.forEach(input => {
            input.addEventListener('input', convertToUppercase);
        });
    </script>
@endsection
