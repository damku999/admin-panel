{{-- Reusable Bootstrap 5 Modal Component --}}
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog {{ $size ?? 'modal-dialog-centered' }}">
        <div class="modal-content border-0 shadow-lg">
            {{-- Header --}}
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark" id="{{ $id }}Label">
                    @if(isset($icon))
                        <i class="{{ $icon }} me-2"></i>
                    @endif
                    {{ $title }}
                </h5>
                <x-modal-close-button :modalId="$id" />
            </div>
            
            {{-- Body --}}
            <div class="modal-body {{ $bodyClass ?? '' }}">
                {{ $slot }}
            </div>
            
            {{-- Footer (optional) --}}
            @if(isset($footer))
                <div class="modal-footer border-0 pt-0">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>