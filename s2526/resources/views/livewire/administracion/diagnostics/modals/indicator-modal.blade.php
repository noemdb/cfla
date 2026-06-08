@if($isIndicatorModalOpen)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-bullseye mr-2"></i>
                    {{ $indicator_id ? 'Editar Indicador' : 'Nuevo Indicador de Logro' }}
                </h5>
                <button type="button" class="close text-white" wire:click="closeIndicatorModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="indicator_code" class="small mb-1">Código del Indicador *</label>
                            <input type="text" class="form-control form-control-sm" id="indicator_code" 
                                   wire:model.defer="indicator_code" 
                                   placeholder="Ej: MAT-4TO-IL-01">
                            @error('indicator_code') <span class="text-danger small">{{ $message }}</span> @enderror
                            <small class="text-muted">Formato: ÁREA-GRADO-IL-NÚMERO</small>
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label for="indicator_expected_level" class="small mb-1">Nivel Esperado *</label>
                            <select class="form-control form-control-sm" id="indicator_expected_level" 
                                    wire:model.defer="indicator_expected_level">
                                <option value="1">1 - Insuficiente</option>
                                <option value="2">2 - En desarrollo</option>
                                <option value="3" selected>3 - Satisfactorio</option>
                                <option value="4">4 - Avanzado</option>
                            </select>
                            @error('indicator_expected_level') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="indicator_description" class="small mb-1">Descripción del Indicador *</label>
                        <textarea class="form-control form-control-sm" id="indicator_description" 
                                  wire:model.defer="indicator_description" rows="4"
                                  placeholder="¿Qué debe hacer el estudiante para evidenciar esta competencia?"></textarea>
                        @error('indicator_description') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small class="text-muted">Debe ser observable, medible y verificable.</small>
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Definición de Niveles:</strong>
                        <ul class="mb-0 pl-3">
                            <li><strong>Insuficiente (1):</strong> No logra el indicador</li>
                            <li><strong>En desarrollo (2):</strong> Logra parcialmente con apoyo</li>
                            <li><strong>Satisfactorio (3):</strong> Logra el indicador esperado</li>
                            <li><strong>Avanzado (4):</strong> Supera el indicador esperado</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeIndicatorModal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" wire:click="saveIndicator">
                    <i class="fas fa-save mr-1"></i>Guardar Indicador
                </button>
            </div>
        </div>
    </div>
</div>
@endif