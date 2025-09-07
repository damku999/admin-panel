{{--
    Generic Form Modal Component
    
    Usage:
    <x-modals.form-modal 
        id="myModal" 
        title="Modal Title"
        :size="'lg'"
        :show-footer="true">
        
        <x-slot name="body">
            <!-- Modal content goes here -->
        </x-slot>
        
        <x-slot name="footer">
            <!-- Custom footer buttons -->
        </x-slot>
    </x-modals.form-modal>
--}}

@props([
    'id' => 'modal',
    'title' => 'Modal',
    'size' => 'md', // sm, md, lg, xl
    'showFooter' => true,
    'showCloseButton' => true
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label">
    <div class="modal-dialog modal-{{ $size }}" role="document">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">
                    {{ $title }}
                </h5>
                @if($showCloseButton)
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                @endif
            </div>

            <!-- Body -->
            <div class="modal-body">
                {{ $body ?? $slot }}
            </div>

            <!-- Footer -->
            @if($showFooter && isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @elseif($showFooter)
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>