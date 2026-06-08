<div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modalError"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">

                <div class="p-3">
                    {{-- <i class="fa fa-exclamation fa-4x text-danger p-5 border border-danger rounded-pill" aria-hidden="true"></i> --}}
                    <i class="{{ $icon_menus['danger'] }} fa-4x text-danger p-5 border border-danger rounded-pill" aria-hidden="true"></i>
                </div>

                <h3 class="text-primary font-weight-bold text-center">
                    <span class="text-danger small">{{ session('error') }}</span>
                </h3>
                <div class=" text-muted text-center small pt-1">
                    Verique que este sea un proceso válido.
                </div>

            </div>
            <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
