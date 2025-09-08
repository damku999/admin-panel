<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalExample"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalExample">Are you Sure You wanted to Delete?</h5>
                <button class="close" type="button" onclick="hideModal('deleteModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Delete" below if you want to delete Relationship Manager!.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="hideModal('deleteModal')">Cancel</button>
                <a class="btn btn-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('insurance_company-delete-form').submit();">
                    Delete
                </a>
                <form id="insurance_company-delete-form" method="POST" action="{{ route('insurance_companies.destroy', ['insurance_company' => $insurance_company->id]) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
