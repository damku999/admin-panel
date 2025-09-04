<!-- Delete Confirmation Modal-->
<div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                    Confirm <span class="module_action">Delete</span>
                </h5>
                <button class="close" type="button" onclick="hideModal('delete_confirm')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h6>Are you sure you want to <span class="module_action text-lowercase">delete</span><span id="module_title"></span>?</h6>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="hideModal('delete_confirm')">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button class="btn btn-danger" type="button" id="delete-btn">
                    <i class="fas fa-trash mr-1"></i> <span class="module_action">Delete</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner for AJAX operations -->
<div id="cover-spin" style="display: none;">
    <div class="d-flex justify-content-center align-items-center position-fixed w-100 h-100" 
         style="top: 0; left: 0; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>