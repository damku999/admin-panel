<!-- Modern Insurance Company Delete Modal (Bootstrap 5) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalExample" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark" id="deleteModalExample">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Delete Insurance Company
                </h5>
                <button type="button" class="btn-close" onclick="hideModal('deleteModal')" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-building fa-3x text-danger mb-3"></i>
                </div>
                <h6 class="mb-2">Are you sure you want to delete this insurance company?</h6>
                <p class="text-muted small mb-0">This action cannot be undone and will permanently remove the company record and all associated data.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-outline-secondary me-2" type="button" onclick="hideModal('deleteModal')">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <a class="btn btn-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('insurance_company-delete-form').submit();">
                    <i class="fas fa-trash me-1"></i> Delete Company
                </a>
                <form id="insurance_company-delete-form" method="POST" action="{{ route('insurance_companies.destroy', ['insurance_company' => $insurance_company->id]) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
