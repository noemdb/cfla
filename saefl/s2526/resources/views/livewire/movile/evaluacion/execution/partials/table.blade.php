<div class="table-responsive" style="overflow-x: hidden;">
    <table class="table table-sm table-hover small mb-0" style="width: 100%; min-width: auto;">
        <thead class="sticky-top bg-light">
            <tr>
                <th scope="col" class="fw-bold text-center" style="width: 40px;">#</th>
                <th scope="col" class="fw-bold">Evaluación</th>
                <th scope="col" class="fw-bold text-center" style="width: 50px;">Estado</th>
                <th scope="col" class="fw-bold text-center" style="width: 50px;">Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluaciones as $evaluacion)
                @php 
                    $pevaluacion = $evaluacion->pevaluacion; 
                    $pensum = $pevaluacion->pensum ?? null; 
                    $asignatura = $pensum->asignatura ?? null; 
                    $profesor = $pevaluacion->profesor ?? null; 
                    $grado = $pensum->grado ?? null; 
                    $seccion = $pevaluacion->seccion ?? null; 
                    $lapso = $pevaluacion->lapso ?? null; 
                    $notas_count = $evaluacion->boletins->count();
                    $promedio = ($notas_count > 0) ? $evaluacion->boletins->avg('nota') : 0; 
                @endphp

                <tr class="@if($notas_count == 0) table-danger @endif align-middle">
                    {{-- Número - MÁS COMPACTO --}}
                    <td class="text-muted text-center small" style="padding: 0.4rem;">
                        {{ (($evaluaciones->currentPage() - 1) * $evaluaciones->perPage()) + $loop->iteration }}
                    </td>
                    
                    {{-- Información de la evaluación - OPTIMIZADA --}}
                    <td style="padding: 0.4rem;">
                        <div class="fw-bold text-truncate mb-1" title="{{ $evaluacion->description ?? ''}}">
                            {{ Str::limit($evaluacion->description, 50, '...') }}
                        </div>
                        <div class="text-muted small">
                            {{-- Línea compacta: Asignatura + Grado/Sección --}}
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-truncate" style="max-width: 55%;" title="{{ $asignatura->name ?? 'N/A' }}">
                                    {{ $asignatura->code ?? 'N/A' }}
                                </span>
                                <span class="text-nowrap">
                                    {{ $grado->name ?? '' }} {{ $seccion->name ?? '' }}
                                </span>
                            </div>
                            
                            {{-- Línea compacta: Profesor + Estadísticas --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-truncate" style="max-width: 50%;" title="{{ $profesor->fullname ?? 'N/A' }}">
                                    {{ Str::limit($profesor->fullname ?? 'N/A', 20, '...') }}
                                </span>
                                <span class="text-nowrap">
                                    <span class="{{ $notas_count == 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                        {{ $notas_count }}
                                    </span>
                                    <span class="text-dark mx-1">|</span>
                                    <span class="fw-bold">{{ number_format($promedio, 1) }}</span>
                                </span>
                            </div>
                        </div>
                    </td>
                    
                    {{-- Estado - MÁS COMPACTO --}}
                    <td class="text-center" style="padding: 0.4rem;">
                        @if($evaluacion->status_execution)
                            <span class="badge bg-success rounded-circle p-1" title="Ejecutada" style="width: 24px; height: 24px;">
                                <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                            </span>
                        @else
                            <span class="badge bg-warning text-dark rounded-circle p-1" title="Pendiente" style="width: 24px; height: 24px;">
                                <i class="fas fa-clock" style="font-size: 0.7rem;"></i>
                            </span>
                        @endif
                    </td>
                    
                    {{-- Acción - MÁS COMPACTO --}}
                    <td class="text-center" style="padding: 0.4rem;">
                        @if ($evaluacion->status_execution)
                        <button class="btn btn-success btn-sm rounded-circle p-0" 
                                wire:click="change({{$evaluacion->id}},false)" 
                                wire:loading.attr="disabled"
                                title="Marcar como Pendiente"
                                style="width: 26px; height: 26px;">
                            <i class="fas fa-undo" style="font-size: 0.7rem;"></i>
                        </button>
                        @else
                        <button class="btn btn-warning btn-sm rounded-circle p-0" 
                                wire:click="change({{$evaluacion->id}},true)" 
                                wire:loading.attr="disabled"
                                title="Marcar como Ejecutada"
                                style="width: 26px; height: 26px;">
                            <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                        </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                            <p class="mb-2 small fw-semibold">No se encontraron evaluaciones</p>
                            <button class="btn btn-outline-primary btn-sm" wire:click="resetFilters">
                                <i class="fas fa-redo me-1"></i> Limpiar
                            </button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@section('stylesheets')
@parent
<style>
    /* Eliminar completamente el scroll horizontal */
    .table-responsive {
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Asegurar que la tabla no exceda el ancho */
    .table {
        min-width: auto !important;
        table-layout: fixed;
    }
    
    /* Columnas MÁS COMPACTAS */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 40px;
        min-width: 40px;
        max-width: 40px;
        padding: 0.4rem;
    }
    
    .table th:nth-child(2),
    .table td:nth-child(2) {
        width: auto;
        min-width: 220px;
        padding: 0.4rem;
    }
    
    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 50px;
        min-width: 50px;
        max-width: 50px;
        padding: 0.4rem;
    }
    
    .table th:nth-child(4),
    .table td:nth-child(4) {
        width: 50px;
        min-width: 50px;
        max-width: 50px;
        padding: 0.4rem;
    }
    
    /* Reducir padding general de la tabla */
    .table > :not(caption) > * > * {
        padding: 0.4rem 0.3rem;
    }
    
    /* Mejorar la legibilidad en móvil */
    @media (max-width: 576px) {
        .table td:nth-child(2) .small {
            font-size: 0.7rem;
        }
        
        .table td:nth-child(2) .fw-bold {
            font-size: 0.8rem;
        }
        
        /* Aún más compacto en móviles muy pequeños */
        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 35px;
            min-width: 35px;
            max-width: 35px;
        }
        
        .table th:nth-child(3),
        .table td:nth-child(3),
        .table th:nth-child(4),
        .table td:nth-child(4) {
            width: 45px;
            min-width: 45px;
            max-width: 45px;
        }
        
        .btn-sm.rounded-circle {
            width: 24px !important;
            height: 24px !important;
        }
        
        .badge.rounded-circle {
            width: 22px !important;
            height: 22px !important;
        }
    }
    
    /* Estados hover mejorados */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .table-danger:hover {
        background-color: rgba(220, 53, 69, 0.05) !important;
    }
    
    /* Badges circulares más compactos */
    .badge.rounded-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    /* Botones más compactos */
    .btn-sm.rounded-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 1px solid transparent;
    }
    
    /* Mejor contraste para texto pequeño */
    .text-muted {
        opacity: 0.8;
    }
</style>
@endsection

@section('scripts')
@parent
@endsection