@if ($isReferentModalOpen)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-book mr-2"></i>
                        {{ $referent_id ? 'Editar Referente' : 'Nuevo Referente Normativo' }}
                    </h5>
                    <button type="button" class="close text-white" wire:click="closeReferentModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="referent_name" class="small mb-1">Nombre del Referente *</label>
                                <input type="text" class="form-control form-control-sm" id="referent_name"
                                    wire:model.defer="referent_name" placeholder="Ej: Reforma Curricular EMG 2017">
                                @error('referent_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="referent_pestudio_id" class="small mb-1">Plan de Estudio *</label>
                                <select class="form-control form-control-sm" id="referent_pestudio_id"
                                    wire:model.defer="referent_pestudio_id">
                                    <option value="">Seleccione...</option>
                                    @foreach ($referentPestudios as $pestudio)
                                        <option value="{{ $pestudio->id }}">{{ $pestudio->name }}</option>
                                    @endforeach
                                </select>
                                @error('referent_pestudio_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="referent_code" class="small mb-1">Código/Resolución *</label>
                                <input type="text" class="form-control form-control-sm" id="referent_code"
                                    wire:model.defer="referent_code" placeholder="Ej: DM/0033">
                                @error('referent_code')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="referent_version" class="small mb-1">Versión *</label>
                                <input type="text" class="form-control form-control-sm" id="referent_version"
                                    wire:model.defer="referent_version" placeholder="Ej: 1.0, 2017.1">
                                @error('referent_version')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="referent_description" class="small mb-1">Descripción</label>
                            <textarea class="form-control form-control-sm" id="referent_description" wire:model.defer="referent_description"
                                rows="3" placeholder="Descripción del referente normativo..."></textarea>
                            @error('referent_description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="referent_active"
                                    wire:model.defer="referent_active">
                                <label class="custom-control-label small" for="referent_active">
                                    Activo (disponible para uso en diagnósticos)
                                </label>
                            </div>
                            @error('referent_active')
                                <span class="text-danger small d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info small mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Nota:</strong> El código y versión deben ser únicos.
                            Ejemplo: "DM/0033" v"2017.1"
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeReferentModal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveReferent">
                        <i class="fas fa-save mr-1"></i>Guardar Referente
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
