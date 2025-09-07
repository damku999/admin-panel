{{--
    Generic Confirmation Modal Component
    
    Usage:
    <x-modals.confirm-modal 
        id="confirmModal" 
        title="Confirm Action"
        message="Are you sure you want to proceed?"
        :confirm-button="['text' => 'Yes, Delete', 'class' => 'btn-danger']"
        onclick="confirmAction()">
    </x-modals.confirm-modal>
--}}

@props([
    'id' => 'confirmModal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmButton' => ['text' => 'Confirm', 'class' => 'btn-primary'],
    'cancelButton' => ['text' => 'Cancel', 'class' => 'btn-secondary'],
    'onclick' => ''
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <p class="mb-0">{{ $message }}</p>
                {{ $slot }}
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn {{ $cancelButton['class'] }}" data-dismiss="modal">
                    <i class="fas fa-times"></i> {{ $cancelButton['text'] }}
                </button>
                
                <button type="button" class="btn {{ $confirmButton['class'] }}" 
                        @if($onclick) onclick="{{ $onclick }}" @endif
                        data-dismiss="modal">
                    <i class="fas fa-check"></i> {{ $confirmButton['text'] }}
                </button>
            </div>
        </div>
    </div>
</div>