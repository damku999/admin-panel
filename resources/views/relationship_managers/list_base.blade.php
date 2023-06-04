@extends('layouts.app')

@section('title', 'Relationship Managers List')

@php
    $list_url = 'list_relationship_manager';
    $module_name = 'Relationship Managers';
    $excel_url = 'excel_relationship_manager';
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card master_card" data-url="{{ $list_url }}" data-excel_url="{{ $excel_url }}">
                <form id="search_form" onsubmit="event.preventDefault();">
                    <div class="card-header">
                        <h3 class="card-title">{{ $module_name }}</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 750px;">
                                <input type="text" placeholder="Search" name="search_box_relationship_manager"
                                    class="form-control float-right filter_by_key"
                                    value="{{ Session::get('search_box_relationship_manager') }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default filter_by_click">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button type="button" class="btn btn-default filter_by_click" reset="yes">
                                        <i class="fas fa-redo"></i>
                                    </button>

                                    <a href="{{ url(Config::get('app.url') . 'relationship_manager/create') }}"
                                        class="btn btn-default ml15" style="max-width: 125px;">
                                        <i class="fas fa-plus"></i> &nbsp; Add {{ $module_name }}
                                    </a>
                                    <button type="button" class="btn btn-default ml10 filter_by_click excel"><i
                                            class="fas fa-file-excel"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="list_load">
                </div>
            </div>
        </div>
    </div>



@endsection
@section('scripts')

    <script type="text/javascript">
        $(function() {
            filterDataAjax('{{ $list_url }}');
        });
    </script>
@endsection
