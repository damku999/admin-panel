{{-- Export Button Component --}}
@props([
    'route',
    'text' => 'Export',
    'icon' => 'fas fa-file-excel',
    'class' => 'btn btn-success btn-sm',
    'permission' => null
])

@if(!$permission || auth()->user()->hasPermissionTo($permission))
    <a href="{{ route($route) }}" class="{{ $class }}">
        <i class="{{ $icon }}"></i> 
        <span class="d-none d-sm-inline">{{ $text }}</span>
    </a>
@endif