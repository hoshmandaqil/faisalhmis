{{-- Modal: List all errors --}}
<div class="modal fade" id="modalSuccess" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="modalDangerLabel" aria-hidden="true" x-init="new bootstrap.Modal(document.getElementById('modalSuccess'), {}).toggle()">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="modalDangerLabel">
                    Success
                </h5>
                <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg fs-2x"></i>
                </button>
            </div>
            <div class="modal-body">
                {{ Session::get('success') }}
            </div>
        </div>
    </div>
</div>
