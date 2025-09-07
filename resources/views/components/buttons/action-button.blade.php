{{--
    Generic Action Button Component
    
    Usage:
    <x-buttons.action-button 
        type="button"
        variant="primary" 
        size="sm"
        icon="fas fa-edit"
        onclick="editRecord()"
        :disabled="false">
        Edit Record
    </x-buttons.action-button>
--}}

@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, dark
    'size' => 'sm', // sm, md, lg
    'icon' => '',
    'onclick' => '',
    'disabled' => false,
    'loading' => false,
    'href' => '',
    'target' => ''
])

@if($href)
    <a href="{{ $href }}" 
       @if($target) target="{{ $target }}" @endif
       class="btn btn-{{ $variant }} btn-{{ $size }} {{ $disabled ? 'disabled' : '' }}"
       @if($onclick) onclick="{{ $onclick }}" @endif>
        @if($loading)
            <i class="fas fa-spinner fa-spin"></i>
        @elseif($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" 
            class="btn btn-{{ $variant }} btn-{{ $size }}"
            @if($onclick) onclick="{{ $onclick }}" @endif
            @if($disabled) disabled @endif>
        @if($loading)
            <i class="fas fa-spinner fa-spin"></i>
        @elseif($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </button>
@endif