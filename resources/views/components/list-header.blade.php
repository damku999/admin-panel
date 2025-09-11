{{-- List Header Component --}}
@props([
    'title',
    'subtitle',
    'addRoute' => null,
    'addPermission' => null,
    'addText' => 'Add New',
    'exportRoute' => null,
    'exportPermission' => null,
    'exportText' => 'Export',
    'extraButtons' => ''
])

<div class="card-header py-2">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
        <div class="mb-1 mb-md-0">
            <h1 class="h5 mb-0 text-primary font-weight-bold">{{ $title }}</h1>
            @if($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <!-- Add Button -->
            @if($addRoute)
                <x-add-button 
                    :route="$addRoute" 
                    :permission="$addPermission" 
                    :text="$addText" 
                />
            @endif
            
            <!-- Export Button -->
            @if($exportRoute)
                <x-export-button 
                    :route="$exportRoute" 
                    :permission="$exportPermission" 
                    :text="$exportText" 
                />
            @endif
            
            <!-- Extra Buttons Slot -->
            {!! $extraButtons !!}
        </div>
    </div>
</div>