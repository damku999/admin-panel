{{--
    Date Range Picker Component
    
    Usage:
    <x-forms.date-range-picker 
        start-id="start_date"
        end-id="end_date"
        :start-value="old('start_date')"
        :end-value="old('end_date')"
        label="Date Range"
        placeholder-start="Start Date"
        placeholder-end="End Date"
        onchange="filterByDateRange()"
        :required="true">
    </x-forms.date-range-picker>
--}}

@props([
    'startId' => 'start_date',
    'endId' => 'end_date',
    'startName' => '',
    'endName' => '',
    'startValue' => '',
    'endValue' => '',
    'label' => 'Date Range',
    'placeholderStart' => 'Start Date',
    'placeholderEnd' => 'End Date',
    'onchange' => '',
    'required' => false,
    'disabled' => false,
    'showLabel' => true,
    'showClearButton' => true,
    'minDate' => '',
    'maxDate' => ''
])

@php
    $startName = $startName ?: $startId;
    $endName = $endName ?: $endId;
@endphp

<div class="date-range-picker-container">
    @if($showLabel && $label)
        <label class="form-label">{{ $label }}</label>
    @endif
    
    <div class="row g-2">
        <div class="col-md-5">
            <input type="date" 
                   id="{{ $startId }}" 
                   name="{{ $startName }}"
                   class="form-control date-range-start"
                   placeholder="{{ $placeholderStart }}"
                   value="{{ $startValue }}"
                   @if($minDate) min="{{ $minDate }}" @endif
                   @if($maxDate) max="{{ $maxDate }}" @endif
                   @if($onchange) onchange="{{ $onchange }}" @endif
                   @if($disabled) disabled @endif
                   @if($required) required @endif>
            
            @error($startName)
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-2 text-center d-flex align-items-center justify-content-center">
            <span class="text-muted">to</span>
        </div>
        
        <div class="col-md-5">
            <input type="date" 
                   id="{{ $endId }}" 
                   name="{{ $endName }}"
                   class="form-control date-range-end"
                   placeholder="{{ $placeholderEnd }}"
                   value="{{ $endValue }}"
                   @if($minDate) min="{{ $minDate }}" @endif
                   @if($maxDate) max="{{ $maxDate }}" @endif
                   @if($onchange) onchange="{{ $onchange }}" @endif
                   @if($disabled) disabled @endif
                   @if($required) required @endif>
            
            @error($endName)
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    @if($showClearButton)
        <div class="mt-2">
            <button type="button" 
                    class="btn btn-sm btn-outline-secondary clear-date-range-btn"
                    onclick="clearDateRange('{{ $startId }}', '{{ $endId }}')"
                    style="display: none;">
                <i class="fas fa-times"></i> Clear Dates
            </button>
        </div>
    @endif
</div>

{{-- JavaScript and CSS moved to /public/admin/js/components.js and /resources/sass/app.scss --}}