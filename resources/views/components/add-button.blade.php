{{-- Add Button Component --}}
@props([
    'route',
    'permission' => null,
    'text' => 'Add New',
    'icon' => 'fas fa-plus',
    'class' => 'btn btn-primary btn-sm'
])

@if(!$permission || auth()->user()->hasPermissionTo($permission))
    <a href="{{ route($route) }}" class="{{ $class }}">
        <i class="{{ $icon }}"></i> 
        <span class="d-none d-sm-inline">{{ $text }}</span>
    </a>
@endif