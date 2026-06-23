<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary font-weight-bold">
                <i class="fas fa-clipboard-list me-2"></i> Gestión de Diagnósticos
            </h5>
            <button class="btn btn-primary btn-sm" wire:click="$set('showCreateDiagMainModal', true)">
                <i class="fas fa-plus"></i> Nuevo Diagnóstico
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-top-0">ID</th>
                        <th class="border-top-0">Nombre</th>
                        <th class="border-top-0">Descripción</th>
                        <th class="border-top-0">Estado</th>
                        <th class="border-top-0 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diagMains as $diagMain)
                        <tr>
                            <td class="align-middle">{{ $diagMain->id }}</td>
                            <td class="align-middle fw-bold">{{ $diagMain->name }}</td>
                            <td class="align-middle">{{ Str::limit($diagMain->description, 50) }}</td>
                            <td class="align-middle">
                                @if ($diagMain->active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="align-middle text-end">
                                <button class="btn btn-sm btn-outline-primary"
                                    wire:click="editDiagMain({{ $diagMain->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    wire:click="deleteDiagMain({{ $diagMain->id }})"
                                    onclick="return confirm('¿Está seguro de eliminar este diagnóstico?') || event.stopImmediatePropagation()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">No hay diagnósticos configurados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create/Edit DiagMain Modal --}}
@if ($showCreateDiagMainModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
        role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $diagMainId ? 'Editar Diagnóstico' : 'Nuevo Diagnóstico' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showCreateDiagMainModal', false)"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="diagMainName" class="form-label">Nombre del Diagnóstico</label>
                        <input type="text" class="form-control @error('diagMainName') is-invalid @enderror"
                            id="diagMainName" wire:model.defer="diagMainName"
                            placeholder="Ej: Diagnóstico Inicial de Matemáticas">
                        @error('diagMainName')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="diagMainDescription" class="form-label">Descripción</label>
                        <textarea class="form-control @error('diagMainDescription') is-invalid @enderror" id="diagMainDescription"
                            wire:model.defer="diagMainDescription" rows="3" placeholder="Descripción opcional..."></textarea>
                        @error('diagMainDescription')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="diagMainActive"
                            wire:model.defer="diagMainActive">
                        <label class="form-check-label" for="diagMainActive">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        wire:click="$set('showCreateDiagMainModal', false)">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="saveDiagMain">
                        {{ $diagMainId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
