<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; {{ config('app.name', 'Laravel') }} @ {{ date('Y') }}</span>
        </div>
    </div>
</footer>
<div class="modal fade" id="delete_confirm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <span class="module_action">Delete</span> Confirmation
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <span class='module_action'>Delete</span> this <span
                        id="module_title"></span>?</p><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="delete-btn">
                    <span class="module_action">Delete</span>
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
