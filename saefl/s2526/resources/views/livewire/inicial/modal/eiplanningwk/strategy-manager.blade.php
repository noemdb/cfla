<div>
    <!-- Strategy List -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-users mr-2"></i>Estrategias Registradas
            </h6>
            <button type="button" class="btn btn-success btn-sm" 
                    wire:click="openModal('edit-strategy')">
                <i class="fas fa-plus mr-1"></i>Agregar Estrategia
            </button>
        </div>

        @php
            $strategies = \App\Models\app\Inicial\Eiplanningwk::find($eiplanningwk_id)?->eiplanningwstrategies ?? collect();
        @endphp

        @if($strategies->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">Orden</th>
                            <th>Momento de Rutina</th>
                            <th>Estrategias por Día</th>
                            <th width="120" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($strategies as $strategy)
                            <tr>
                                <td>
                                    <span class="badge badge-success">{{ $strategy->order ?? 'S/N' }}</span>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $strategy->momento_rutina_diaria }}</strong>
                                </td>
                                <td>
                                    <div class="row small">
                                        
                                        {{-- ...existing code... --}}
                                        @if($strategy->estrategia)
                                            <div class="col-12 mb-1 border-bottom mb-4">
                                                <strong class="text-primary text-uppercase">{{ $strategy->day_of_week }}:</strong> {{ Str::limit($strategy->estrategia, 300) }}
                                            </div>
                                        @endif
                                        {{-- 
                                        @if($strategy->martes)
                                            <div class="col-12 mb-1 border-bottom mb-4">
                                                <strong class="text-success">M:</strong> {{ Str::limit($strategy->martes, 300) }}
                                            </div>
                                        @endif
                                        @if($strategy->miercoles)
                                            <div class="col-12 mb-1 border-bottom mb-4">
                                                <strong class="text-warning">Mi:</strong> {{ Str::limit($strategy->miercoles, 300) }}
                                            </div>
                                        @endif
                                        @if($strategy->jueves)
                                            <div class="col-12 mb-1 border-bottom mb-4">
                                                <strong class="text-danger">J:</strong> {{ Str::limit($strategy->jueves, 300) }}
                                            </div>
                                        @endif
                                        @if($strategy->viernes)
                                            <div class="col-12 mb-1 border-bottom mb-4">
                                                <strong class="text-info">V:</strong> {{ Str::limit($strategy->viernes, 300) }}
                                            </div>
                                        @endif --}}
                                        {{-- ...existing code... --}}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                wire:click="openModal('edit-strategy', {{ $strategy->id }})"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirm('¿Eliminar esta estrategia?') || event.stopImmediatePropagation()" 
                                                wire:click="deleteStrategy({{ $strategy->id }})"
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
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No hay estrategias registradas</h6>
                <p class="text-muted small">Agrega la primera estrategia para planificar las actividades semanales.</p>
                <button type="button" class="btn btn-success" 
                        wire:click="openModal('edit-strategy')">
                    <i class="fas fa-plus mr-1"></i>Agregar Primera Estrategia
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
