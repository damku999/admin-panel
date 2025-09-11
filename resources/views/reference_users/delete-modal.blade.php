<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalExample"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark" id="deleteModalExample">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Delete Reference User
                </h5>
                <x-modal-close-button modalId="deleteModal" />
            </div>
            <div class="modal-body">Select "Delete" below if you want to delete Reference User!.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="hideModal('deleteModal')">Cancel</button>
                <a class="btn btn-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('broker-delete-form').submit();">
                    Delete
                </a>
                <form id="broker-delete-form" method="POST" action="{{ route('reference_users.destroy', ['broker' => $broker->id]) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
