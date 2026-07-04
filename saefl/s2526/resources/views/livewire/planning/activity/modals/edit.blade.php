<div wire:ignore.self class="modal fade" id="editActivityModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header alert-warning p-2">
                <h5 class="modal-title font-weight-bolder p-1">
                    <i class="fas fa-edit text-warning mr-1"></i>
                    Editar Actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                @if ($activity ?? false)
                    <div class="alert alert-secondary p-2 mb-3 small">
                        <strong><i class="fas fa-book-open mr-1"></i> Plan de Evaluación:</strong>
                        {{ $activity->pevaluacion->pensum->asignatura->name ?? '' }}
                        <span class="font-weight-bold">
                            [{{ $activity->pevaluacion->pensum->grado->name ?? '' }} {{ $activity->pevaluacion->seccion->name ?? '' }}]
                        </span>
                        <span class="badge badge-secondary ml-1">{{ $activity->pevaluacion->lapso->name ?? '' }}</span>
                    </div>
                @endif

                @include('livewire.planning.activity.partials.form')
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-warning" wire:click="updateAct()" wire:loading.attr="disabled">
                    <i class="fas fa-save mr-1"></i>
                    <span wire:loading.remove wire:target="updateAct">Actualizar</span>
                    <span wire:loading wire:target="updateAct">Actualizando...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('open-edit-modal', event => {
        $('#editActivityModal').modal('show');
    });
    window.addEventListener('close-edit-modal', event => {
        $('#editActivityModal').modal('hide');
    });
</script>
