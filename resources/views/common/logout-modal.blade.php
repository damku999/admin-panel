<!-- Modern Logout Modal (Bootstrap 5) -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark" id="exampleModalLabel">
                    <i class="fas fa-sign-out-alt text-primary me-2"></i>
                    Ready to Leave?
                </h5>
                <button type="button" class="btn-close" onclick="hideLogoutModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-door-open fa-3x text-primary mb-3"></i>
                </div>
                <h6 class="mb-2">Are you ready to end your current session?</h6>
                <p class="text-muted small mb-0">You will be securely logged out of the admin panel.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-outline-secondary me-2" type="button" onclick="hideLogoutModal()">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <a class="btn btn-primary" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
