{{-- Modal: List all errors --}}
<div class="modal fade" id="modalDanger" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="modalDangerLabel" aria-hidden="true" x-init="new bootstrap.Modal(document.getElementById('modalDanger'), {}).toggle()">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="modalDangerLabel">
                    <i class="bx bx-error-circle bx-sm"></i> Oops!
                </h5>
                <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg fs-2x"></i>
                </button>
            </div>
            <div class="modal-body">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
