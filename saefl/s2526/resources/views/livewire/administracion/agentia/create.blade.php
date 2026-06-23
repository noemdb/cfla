<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $prompt_id ? 'Editar / Nueva Versión' : 'Crear Prompt' }}</h5>
                <button type="button" class="close" wire:click="closeModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="alert alert-info small mb-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Tipos de Prompt:</strong>
                    <ul class="mb-0 pl-3">
                        <li><strong>System Prompt (Fijo):</strong> Define el marco institucional, restricciones y rol de la IA. No cambia entre estudiantes.</li>
                        <li><strong>User Prompt (Dinámico):</strong> Incluye marcadores como <code>@{{ payload_json }}</code> para datos específicos del estudiante.</li>
                    </ul>
                </div>
                
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="prompt_type" class="small mb-1">Tipo</label>
                            <select class="form-control form-control-sm" id="prompt_type" wire:model="prompt_type">
                                <option value="system">System Prompt (Fijo)</option>
                                <option value="user">User Prompt (Dinámico)</option>
                            </select>
                            @error('prompt_type')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-5">
                            <label for="name" class="small mb-1">Nombre / Contexto</label>
                            <input type="text" class="form-control form-control-sm" id="name" wire:model="name"
                                placeholder="Ej. Diagnóstico 2024">
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label for="version" class="small mb-1">Versión</label>
                            <input type="text" class="form-control form-control-sm" id="version"
                                wire:model="version" placeholder="1.0">
                            @error('version')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="small mb-1">Descripción</label>
                        <input type="text" class="form-control form-control-sm" id="description"
                            wire:model="description" placeholder="Descripción breve del propósito">
                        @error('description')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content" class="small mb-1">Contenido del Prompt</label>
                        <textarea class="form-control form-control-sm font-monospace" id="content" wire:model="content" rows="12"
                            placeholder="Escriba aquí el prompt..." style="font-family: monospace; font-size: 0.85rem;"></textarea>
                        @error('content')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            @if ($prompt_type == 'user')
                                <i class="fas fa-info-circle"></i> Usar marcadores como
                                <code>@{{ payload_json }}</code> para inserción dinámica.
                            @else
                                <i class="fas fa-info-circle"></i> Define el rol, tono y restricciones generales.
                            @endif
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activeSwitch" wire:model="active">
                            <label class="custom-control-label small" for="activeSwitch">Marcar como Activo (Desactivará
                                otras versiones del mismo prompt)</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Cerrar</button>
                <button type="button" class="btn btn-primary" wire:click.prevent="store()">Guardar</button>
            </div>
        </div>
    </div>
</div>
