<div>
    <!-- Activity List -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-tasks mr-2"></i>Actividades Registradas
            </h6>
            <button type="button" class="btn btn-success btn-sm" 
                    wire:click="openModal('edit-activity')">
                <i class="fas fa-plus mr-1"></i>Agregar Actividad
            </button>
        </div>

        @php
            $activities = \App\Models\app\Inicial\Eispecialk::find($eispecialk_id)?->getOrderedActivities() ?? collect();
        @endphp

        @if($activities->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">Orden</th>
                            <th>Área/Componente</th>
                            <th>Objetivo</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                            <tr>
                                <td>
                                    <span class="badge badge-success">{{ $activity->order ?? 'S/N' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $activity->pevaluacion->pensum->asignatura->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($activity->componente, 40) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div title="{{ $activity->objetivo }}">
                                        {{ Str::limit($activity->objetivo, 60) }}
                                    </div>
                                    @if($activity->aprendizaje_esperado)
                                        <small class="text-muted">
                                            <strong>Aprendizaje:</strong> {{ Str::limit($activity->aprendizaje_esperado, 50) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                wire:click="openModal('edit-activity', {{ $activity->id }})"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirm('¿Eliminar esta actividad?') || event.stopImmediatePropagation()" 
                                                wire:click="deleteActivity({{ $activity->id }})"
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
                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No hay actividades registradas</h6>
                <p class="text-muted small">Agrega la primera actividad para planificar las actividades especiales.</p>
                <button type="button" class="btn btn-success" 
                        wire:click="openModal('edit-activity')">
                    <i class="fas fa-plus mr-1"></i>Agregar Primera Actividad
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
