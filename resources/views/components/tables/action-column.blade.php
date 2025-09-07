{{--
    Table Action Column Component
    
    Usage:
    <x-tables.action-column :item="$customer">
        <x-buttons.action-button 
            variant="success" 
            size="sm" 
            icon="fas fa-whatsapp"
            onclick="sendWhatsApp({{ $item->id }})"
            title="Send WhatsApp">
        </x-buttons.action-button>
        
        <x-buttons.action-button 
            variant="primary" 
            size="sm" 
            icon="fas fa-edit"
            href="{{ route('customers.edit', $item->id) }}"
            title="Edit">
        </x-buttons.action-button>
        
        <x-buttons.action-button 
            variant="danger" 
            size="sm" 
            icon="fas fa-trash"
            onclick="confirmDelete({{ $item->id }})"
            title="Delete">
        </x-buttons.action-button>
    </x-tables.action-column>
--}}

@props([
    'item' => null,
    'alignment' => 'center', // left, center, right
    'direction' => 'horizontal', // horizontal, vertical
    'gap' => '6px',
    'wrapActions' => true
])

<td class="action-column text-{{ $alignment }}">
    <div class="action-buttons {{ $direction === 'vertical' ? 'd-flex flex-column' : 'd-flex' }} 
                {{ $wrapActions ? 'flex-wrap' : '' }} 
                {{ $alignment === 'center' ? 'justify-content-center' : '' }}
                {{ $alignment === 'right' ? 'justify-content-end' : '' }}
                {{ $alignment === 'left' ? 'justify-content-start' : '' }}"
         style="gap: {{ $gap }};">
        
        {{ $slot }}
    </div>
</td>

<style>
.action-column {
    white-space: nowrap;
    vertical-align: middle;
    padding: 8px;
}

.action-column .action-buttons {
    min-width: max-content;
}

.action-column .btn {
    min-width: auto;
}

/* Responsive behavior for action buttons */
@media (max-width: 768px) {
    .action-column .action-buttons {
        flex-direction: column !important;
        gap: 4px !important;
    }
    
    .action-column .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Hover effect for action column */
.table-hover tbody tr:hover .action-column {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Focus management for keyboard navigation */
.action-column .btn:focus {
    z-index: 2;
    position: relative;
}
</style>