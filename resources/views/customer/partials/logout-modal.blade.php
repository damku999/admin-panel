<!-- Customer Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-danger">
                <h5 class="modal-title text-white fw-bold" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>Ready to Leave?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Select "Logout" below if you are ready to end your current session.</p>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form method="POST" action="{{ route('customer.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>