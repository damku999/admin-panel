{{-- Complete List Page Component --}}
@props([
    'title',
    'subtitle',
    'addRoute' => null,
    'addPermission' => null,
    'exportRoute' => null,
    'exportPermission' => null,
    'searchRoute' => null,
    'searchValue' => '',
    'extraFilters' => '',
    'extraButtons' => ''
])

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- DataTables Card -->
    <div class="card shadow mb-4">
        <!-- Header with Buttons -->
        <x-list-header 
            :title="$title"
            :subtitle="$subtitle" 
            :addRoute="$addRoute"
            :addPermission="$addPermission"
            :exportRoute="$exportRoute"
            :exportPermission="$exportPermission"
            :extraButtons="$extraButtons"
        />

        <!-- Search/Filter Section -->
        @if($searchRoute)
        <div class="card-header border-bottom">
            <form action="{{ route($searchRoute) }}" method="GET" role="search">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" placeholder="Search..." name="search"
                                class="form-control" value="{{ $searchValue }}">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route($searchRoute) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    @if($extraFilters)
                    <div class="col-md-6">
                        {!! $extraFilters !!}
                    </div>
                    @endif
                </div>
            </form>
        </div>
        @endif

        <!-- Table Content -->
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>
@endsection