<div wire:ignore.self class="modal fade" id="createActivityModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header alert-success p-2">
                <h5 class="modal-title font-weight-bolder p-1">
                    <i class="fas fa-plus-circle text-success mr-1"></i>
                    Nueva Actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                @include('livewire.planning.activity.partials.form')
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-sm btn-success" wire:click="store()" wire:loading.attr="disabled">
                    <i class="fas fa-save mr-1"></i>
                    <span wire:loading.remove wire:target="store">Guardar</span>
                    <span wire:loading wire:target="store">Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('open-create-modal', event => {
        $('#createActivityModal').modal('show');
    });
    window.addEventListener('close-create-modal', event => {
        $('#createActivityModal').modal('hide');
    });
</script>
