<div>
    <!-- Summary List -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-list mr-2"></i>Resúmenes Registrados
            </h6>
            <button type="button" class="btn btn-success btn-sm" 
                    wire:click="openModal('edit-summary')">
                <i class="fas fa-plus mr-1"></i>Agregar Resumen
            </button>
        </div>

        @php
            $summaries = \App\Models\app\Inicial\Eiplanningbwk::find($eiplanningbwk_id)?->getOrderedSummaries() ?? collect();
        @endphp

        @if($summaries->count() > 0)
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
                        @foreach($summaries as $summary)
                            <tr>
                                <td>
                                    <span class="badge badge-primary">{{ $summary->order ?? 'S/N' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $summary->pevaluacion->pensum->asignatura->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($summary->componente, 40) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div title="{{ $summary->objetivo }}">
                                        {{ Str::limit($summary->objetivo, 60) }}
                                    </div>
                                    @if($summary->aprendizaje_esperado)
                                        <small class="text-muted">
                                            <strong>Aprendizaje:</strong> {{ Str::limit($summary->aprendizaje_esperado, 50) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                wire:click="openModal('edit-summary', {{ $summary->id }})"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirm('¿Eliminar este resumen?') || event.stopImmediatePropagation()" 
                                                wire:click="deleteSummary({{ $summary->id }})"
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
                <h6 class="text-muted">No hay resúmenes registrados</h6>
                <p class="text-muted small">Agrega el primer resumen para organizar los objetivos del plan.</p>
                <button type="button" class="btn btn-success" 
                        wire:click="openModal('edit-summary')">
                    <i class="fas fa-plus mr-1"></i>Agregar Primer Resumen
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
