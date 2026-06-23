@php
    $plan = \App\Models\app\Inicial\Eispecialk::with(['grado', 'seccion', 'profesor'])->find($editingId);
    $activities = $plan ? $plan->getOrderedActivities() : collect();
    $strategies = $plan ? $plan->getOrderedStrategies() : collect();
@endphp

@if($plan)
<div class="container-fluid">
    <!-- Plan Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-secondary">
                <div class="card-header alert-secondary text-dark">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-info-circle mr-2"></i>Información General del Plan Especial
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary">Grado/Año:</strong>
                                <span class="ml-2">{{ $plan->grado->name }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Sección:</strong>
                                <span class="ml-2">{{ $plan->seccion->name }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Profesor:</strong>
                                <span class="ml-2">{{ $plan->profesor->fullname ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary">Fecha Inicial:</strong>
                                <span class="ml-2">{{ \Carbon\Carbon::parse($plan->finicial)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Fecha Final:</strong>
                                <span class="ml-2">{{ \Carbon\Carbon::parse($plan->ffinal)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Tiempo de Ejecución:</strong>
                                <span class="ml-2">{{ $plan->tiempo_ejecucion }} semana{{ $plan->tiempo_ejecucion != 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Justification and Observations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-clipboard-list mr-2"></i>Justificación y Observaciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-success">
                            <i class="fas fa-file-alt mr-1"></i>Justificación:
                        </h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0 text-justify">{{ $plan->justificacion }}</p>
                        </div>
                    </div>
                    
                    @if($plan->observacion)
                        <div class="mb-3">
                            <h6 class="font-weight-bold text-info">
                                <i class="fas fa-comment mr-1"></i>Observación del Coordinador de Evaluación:
                            </h6>
                            <div class="bg-light p-3 rounded border-left border-info">
                                <p class="mb-0 text-justify">{{ $plan->observacion }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-2x text-info mb-2"></i>
                    <h4 class="text-info font-weight-bold">{{ $activities->count() }}</h4>
                    <p class="text-muted mb-0">Actividades Registradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning font-weight-bold">{{ $strategies->count() }}</h4>
                    <p class="text-muted mb-0">Estrategias Registradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                    <h4 class="text-success font-weight-bold">{{ $plan->tiempo_ejecucion }}</h4>
                    <p class="text-muted mb-0">Semanas de Duración</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities -->
    @if($activities->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-tasks mr-2"></i>Actividades Especiales ({{ $activities->count() }} elementos)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="60">Orden</th>
                                        <th>Área/Componente</th>
                                        <th>Objetivo</th>
                                        <th>Aprendizaje Esperado</th>
                                        <th>Indicadores</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $activity)
                                        <tr>
                                            <td>
                                                <span class="badge badge-info">{{ $activity->order ?? 'S/N' }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    @php
                                                        $evaluacion = $activity->pevaluacion;
                                                        $asignaturaName = $evaluacion->pensum->asignatura->name ?? 'N/A';
                                                        $asignaturaCode = $evaluacion->pensum->asignatura->code ?? '';
                                                    @endphp
                                                    <strong class="text-primary">{{ $asignaturaName }}</strong>
                                                    @if($asignaturaCode)
                                                        <br>
                                                        <small class="text-muted">[{{ $asignaturaCode }}] {{ $activity->componente }}</small>
                                                    @else
                                                        <br>
                                                        <small class="text-muted">{{ $activity->componente }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($activity->objetivo, 100) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($activity->aprendizaje_esperado, 100) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($activity->indicadores, 100) }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Strategies -->
    @if($strategies->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-users mr-2"></i>Estrategias Especiales ({{ $strategies->count() }} elementos)
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            // Agrupar estrategias por día para mejor visualización
                            $strategiesByDay = [];
                            foreach ($strategies as $strategy) {
                                $day = $strategy->day_of_week;
                                if (!isset($strategiesByDay[$day])) {
                                    $strategiesByDay[$day] = [
                                        'name' => \App\Models\app\Inicial\Eispecialstrategy::WEEK_DAYS[$day] ?? $day,
                                        'strategies' => []
                                    ];
                                }
                                $strategiesByDay[$day]['strategies'][] = $strategy;
                            }
                        @endphp

                        @foreach($strategiesByDay as $dayData)
                            <div class="card mb-4 border-left border-warning">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 font-weight-bold text-warning">
                                        <i class="fas fa-calendar-day mr-2"></i>{{ $dayData['name'] }}
                                        <span class="badge badge-warning ml-2">{{ count($dayData['strategies']) }} estrategias</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach($dayData['strategies'] as $strategy)
                                        <div class="card mb-3 border-0 bg-light">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="font-weight-bold text-dark mb-0">
                                                        <span class="badge badge-success mr-2">{{ $strategy->order ?? 'S/N' }}</span>
                                                        {{ $strategy->momento_rutina_diaria }}
                                                    </h6>
                                                </div>
                                                
                                                @if($strategy->estrategia)
                                                    <div class="bg-white p-3 rounded border">
                                                        <strong class="text-primary">Estrategia:</strong>
                                                        <p class="mb-0 mt-1 text-justify">{{ $strategy->estrategia }}</p>
                                                    </div>
                                                @endif

                                                @if($strategy->description)
                                                    <div class="mt-2">
                                                        <strong class="text-muted">Descripción adicional:</strong>
                                                        <p class="mb-0 mt-1 small text-muted">{{ $strategy->description }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- No Content Messages -->
    @if($activities->count() == 0 && $strategies->count() == 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-light">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Plan sin contenido adicional</h5>
                        <p class="text-muted">Este plan especial no tiene actividades ni estrategias registradas aún.</p>
                        <div class="mt-3">
                            <button type="button" class="btn btn-info mr-2" 
                                    wire:click="openModal('activity', {{ $plan->id }})">
                                <i class="fas fa-tasks mr-1"></i>Agregar Actividades
                            </button>
                            <button type="button" class="btn btn-warning" 
                                    wire:click="openModal('strategy', {{ $plan->id }})">
                                <i class="fas fa-users mr-1"></i>Agregar Estrategias
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" 
                            wire:click="openModal('edit', {{ $plan->id }})">
                        <i class="fas fa-edit mr-1"></i>Editar Plan
                    </button>
                    <button type="button" class="btn btn-info" 
                            wire:click="openModal('activity', {{ $plan->id }})">
                        <i class="fas fa-tasks mr-1"></i>Gestionar Actividades
                    </button>
                    <button type="button" class="btn btn-success" 
                            wire:click="openModal('strategy', {{ $plan->id }})">
                        <i class="fas fa-users mr-1"></i>Gestionar Estrategias
                    </button>
                </div>
                
                <div class="btn-group">
                    <a href="{{ route('inicials.eispecialks.format.index', $plan->id) }}" 
                       target="_blank" class="btn btn-dark">
                        <i class="fas fa-file-pdf mr-1"></i>Generar PDF
                    </a>
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times mr-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@else
    <div class="text-center py-4">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h5 class="text-muted">Plan especial no encontrado</h5>
        <p class="text-muted">El plan especial solicitado no existe o ha sido eliminado.</p>
        <button type="button" class="btn btn-secondary" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cerrar
        </button>
    </div>
@endif