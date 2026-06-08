@if($isCompetencyModalOpen)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tasks mr-2"></i>
                    {{ $competency_id ? 'Editar Competencia' : 'Nueva Competencia' }}
                </h5>
                <button type="button" class="close text-white" wire:click="closeCompetencyModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="competency_name" class="small mb-1">Nombre de la Competencia *</label>
                            <input type="text" class="form-control form-control-sm" id="competency_name" 
                                   wire:model.defer="competency_name" 
                                   placeholder="Ej: Resuelve problemas matemáticos contextualizados">
                            @error('competency_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="competency_pensum_id" class="small mb-1">Área de Formación</label>
                            <select class="form-control form-control-sm" id="competency_pensum_id" 
                                    wire:model.defer="competency_pensum_id">
                                <option value="">-- Seleccione un área --</option>
                                @foreach($pensums->sortBy('fullname') as $pensum)
                                    <option value="{{ $pensum->id }}">{{ $pensum->fullname }}</option>
                                @endforeach
                            </select>
                            @error('competency_pensum_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="competency_description" class="small mb-1">Descripción</label>
                        <textarea class="form-control form-control-sm" id="competency_description" 
                                  wire:model.defer="competency_description" rows="3"
                                  placeholder="Descripción de la competencia..."></textarea>
                        @error('competency_description') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Recordatorio:</strong> Una competencia es una capacidad compleja que integra 
                        conocimientos, habilidades y actitudes. No se evalúa directamente, se evalúan sus indicadores.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeCompetencyModal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" wire:click="saveCompetency">
                    <i class="fas fa-save mr-1"></i>Guardar Competencia
                </button>
            </div>
        </div>
    </div>
</div>
@endif