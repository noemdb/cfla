{{-- /home/nuser/code/s2526/resources/views/livewire/academico/diagnostic/partials/diagmains.blade.php --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-2">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list me-2"></i> Lista de Diagnósticos
            </h5>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" 
                       id="activeOnly" wire:model="activeOnly">
                <label class="form-check-label small" for="activeOnly">
                    Solo activos
                </label>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-3 py-2">
                            <button class="btn btn-sm btn-link text-dark font-weight-bold p-0" 
                                    wire:click="sortBy('name')">
                                Nombre
                                @if($sortBy === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </button>
                        </th>
                        <th class="px-3 py-2">Descripción</th>
                        <th class="px-3 py-2 text-center">Sesiones</th>
                        <th class="px-3 py-2 text-center">Estudiantes</th>
                        <th class="px-3 py-2 text-center">
                            <button class="btn btn-sm btn-link text-dark font-weight-bold p-0" 
                                    wire:click="sortBy('active')">
                                Estado
                                @if($sortBy === 'active')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </button>
                        </th>
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diagnostics as $diagnostic)
                        <tr>
                            <td class="px-3 py-2">
                                <strong>{{ $diagnostic->name }}</strong>
                            </td>
                            <td class="px-3 py-2">
                                {{ Str::limit($diagnostic->description, 70) }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="badge bg-primary">
                                    {{ $diagnostic->sessions->count() }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="badge bg-info">
                                    {{ $diagnostic->sessions->unique('estudiant_id')->count() }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($diagnostic->active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary"
                                            wire:click="showDiagnosticDetails({{ $diagnostic->id }})"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-{{ $diagnostic->active ? 'outline-warning' : 'outline-success' }}"
                                            wire:click="toggleActive({{ $diagnostic->id }})"
                                            title="{{ $diagnostic->active ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $diagnostic->active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0">No se encontraron diagnósticos.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($diagnostics->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $diagnostics->links() }}
        </div>
    @endif
</div>