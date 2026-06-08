<div>
    <!-- Position List -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-list mr-2"></i>Posiciones Registradas
            </h6>
            <button type="button" class="btn btn-success btn-sm" 
                    wire:click="openModal('edit-position')">
                <i class="fas fa-plus mr-1"></i>Agregar Posición
            </button>
        </div>

        @php
            $positions = \App\Models\app\Inicial\Eievaluationk::find($eievaluationk_id)?->getOrderedEvaluationps() ?? collect();
        @endphp

        @if($positions->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">Orden</th>
                            <th>Área/Componente</th>
                            <th>Aprendizaje</th>
                            <th>Fecha</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $position)
                            <tr>
                                <td>
                                    <span class="badge badge-success">{{ $position->order ?? 'S/N' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $position->pevaluacion->pensum->asignatura->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($position->componente ?? 'Sin componente', 40) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div title="{{ $position->aprendizaje_alcanzado }}">
                                        {{ Str::limit($position->aprendizaje_alcanzado ?? 'No especificado', 60) }}
                                    </div>
                                    @if($position->nombre_ninos)
                                        <small class="text-muted">
                                            <strong>Niños:</strong> {{ Str::limit($position->nombre_ninos, 50) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $position->fecha ?? 'No especificada' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                wire:click="openModal('edit-position', {{ $position->id }})"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirm('¿Eliminar esta posición?') || event.stopImmediatePropagation()" 
                                                wire:click="deletePosition({{ $position->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No hay posiciones registradas</h6>
                <p class="text-muted small">Agrega la primera posición para organizar las evaluaciones del plan.</p>
                <button type="button" class="btn btn-success" 
                        wire:click="openModal('edit-position')">
                    <i class="fas fa-plus mr-1"></i>Agregar Primera Posición
                </button>
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cerrar
        </button>
    </div>
</div>
