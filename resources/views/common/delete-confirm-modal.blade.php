<!-- Modern Delete Confirmation Modal (Bootstrap 5) -->
<div class="modal fade" id="delete_confirm" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirm <span class="module_action">Delete</span>
                </h5>
                <button type="button" class="btn-close" onclick="hideModal('delete_confirm')" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                </div>
                <h6 class="mb-2">Are you sure you want to <span class="module_action text-lowercase">delete</span><span id="module_title"></span>?</h6>
                <p class="text-muted small mb-0">This action cannot be undone and will permanently remove all associated data.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-outline-secondary me-2" type="button" onclick="hideModal('delete_confirm')">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button class="btn btn-danger" type="button" id="delete-btn">
                    <i class="fas fa-trash me-1"></i> <span class="module_action">Delete</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modern Loading Spinner (Bootstrap 5) -->
<div id="cover-spin" style="display: none;">
    <div class="d-flex justify-content-center align-items-center position-fixed w-100 h-100" 
         style="top: 0; left: 0; background: rgba(0,0,0,0.6); z-index: 9999; backdrop-filter: blur(2px);">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="text-white fw-medium">Processing...</div>
        </div>
    </div>
</div>