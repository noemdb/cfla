<div class="modal fade resultCreatedOk" id="resultCreatedOk" tabindex="-1" role="dialog" aria-labelledby="resultCreatedOk"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">

                <div class="p-3">
                    <i class="{{ $icon_menus['check'] ?? '' }} fa-4x text-success p-5 border border-success rounded-pill" aria-hidden="true"></i>
                </div>

                <h5 class="text-primary font-weight-bold text-center">
                    <span class="text-primary small">{{ session('createdOk') }}</span>
                </h5>

                {{-- <div class=" text-muted text-center small pt-1"> Verique que este sea un proceso válido. </div> --}}

            </div>
            <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
