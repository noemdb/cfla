<div>
    <!-- Strategy List -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-users mr-2"></i>Estrategias Registradas
            </h6>
            <button type="button" class="btn btn-success btn-sm" 
                    wire:click="openModal('strategy', {{ $eiplanningbwk_id }})">
                <i class="fas fa-plus mr-1"></i>Gestionar Estrategias
            </button>
        </div>

        @php
            $strategies = \App\Models\app\Inicial\Eiplanningbwk::find($eiplanningbwk_id)?->getOrderedStrategies() ?? collect();
        @endphp

        @if($strategies->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">Orden</th>
                            <th>Día</th>
                            <th>Momento de Rutina</th>
                            <th>Estrategia</th>
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
                                    <strong class="text-primary">{{ $weekDays[$strategy->day_of_week] ?? $strategy->day_of_week }}</strong>
                                </td>
                                <td>
                                    <strong class="text-info">{{ $strategy->momento_rutina_diaria }}</strong>
                                </td>
                                <td>
                                    @if($strategy->estrategia)
                                        <div class="small">
                                            {{ Str::limit($strategy->estrategia, 200) }}
                                        </div>
                                    @else
                                        <span class="text-muted small">Sin estrategia definida</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                wire:click="openModal('strategy', {{ $eiplanningbwk_id }})"
                                                title="Gestionar Estrategias">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirm('¿Eliminar esta estrategia?') || event.stopImmediatePropagation()" 
                                                wire:click="deleteStrategy('{{ $strategy->day_of_week }}', '{{ $strategy->momento_rutina_diaria }}')"
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
                <p class="text-muted small">Agrega estrategias para planificar las actividades quincenales.</p>
                <button type="button" class="btn btn-success" 
                        wire:click="openModal('strategy', {{ $eiplanningbwk_id }})">
                    <i class="fas fa-plus mr-1"></i>Gestionar Estrategias
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