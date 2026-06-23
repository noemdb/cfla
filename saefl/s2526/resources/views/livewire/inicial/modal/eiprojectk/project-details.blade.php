@php
    $project = \App\Models\app\Inicial\Eiprojectk::with(['grado', 'seccion', 'profesor'])->find($editingId);
    $reviews = $project ? $project->getOrderedViews() : collect();
    $summaries = $project ? $project->getOrderedSummaries() : collect();
    $strategies = $project ? $project->getOrderedStrategies() : collect();
    
    // Obtener días de la semana y momentos desde el modelo de estrategias
    $weekDays = \App\Models\app\Inicial\Eiprojectkstrategy::WEEK_DAYS ?? [
        'lunes' => 'Lunes',
        'martes' => 'Martes', 
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes'
    ];
    
    $moments = \App\Models\app\Inicial\Eiprojectkstrategy::LIST_MOMENT ?? [
        'Recibimiento' => 'Recibimiento',
        'Momento Cívico' => 'Momento Cívico',
        'Aseo-Desayuno-Aseo' => 'Aseo-Desayuno-Aseo',
        'Periodo: Planificación' => 'Periodo: Planificación',
        'Periodo: Trabajo Libre' => 'Periodo: Trabajo Libre',
        'Periodo: Orden y limpieza' => 'Periodo: Orden y limpieza',
        'Periodo: Intercambio y Recuento' => 'Periodo: Intercambio y Recuento',
        'Periodo: Trabajos en Pequeños Grupos' => 'Periodo: Trabajos en Pequeños Grupos',
        'Periodo: Actividades Colectivas' => 'Periodo: Actividades Colectivas',
        'Periodo: Despedida' => 'Periodo: Despedida',
    ];
@endphp

@if($project)
<div class="container-fluid">
    <!-- Project Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-light">
                <div class="card-header bg-light text-dark">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-info-circle mr-2"></i>Información General del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary">Grado/Año:</strong>
                                <span class="ml-2">{{ $project->grado->name }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Sección:</strong>
                                <span class="ml-2">{{ $project->seccion->name }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Profesor:</strong>
                                <span class="ml-2">{{ $project->profesor->fullname ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary">Fecha Inicial:</strong>
                                <span class="ml-2">{{ \Carbon\Carbon::parse($project->finicial)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Fecha Final:</strong>
                                <span class="ml-2">{{ \Carbon\Carbon::parse($project->ffinal)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Tiempo de Ejecución:</strong>
                                <span class="ml-2">{{ $project->tiempo_ejecucion }} semana{{ $project->tiempo_ejecucion != 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnosis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-stethoscope mr-2"></i>Diagnóstico del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0 text-justify">{{ $project->diagnostico }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <i class="fas fa-search fa-2x text-info mb-2"></i>
                    <h4 class="text-info font-weight-bold">{{ $reviews->count() }}</h4>
                    <p class="text-muted mb-0">Revisiones</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-list fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning font-weight-bold">{{ $summaries->count() }}</h4>
                    <p class="text-muted mb-0">Resúmenes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <h4 class="text-success font-weight-bold">{{ $strategies->count() }}</h4>
                    <p class="text-muted mb-0">Estrategias</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary font-weight-bold">{{ $project->tiempo_ejecucion }}</h4>
                    <p class="text-muted mb-0">Semanas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    @if($reviews->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-search mr-2"></i>Revisiones del Proyecto ({{ $reviews->count() }} elementos)
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($reviews as $review)
                            <div class="card mb-3 border-left border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="font-weight-bold text-info mb-0">
                                            <span class="badge badge-info mr-2">{{ $review->order ?? 'S/N' }}</span>
                                            {{ $review->eleccion_tema_nombre }}
                                        </h6>
                                    </div>
                                    
                                    <div class="row small">
                                        <div class="col-md-6 mb-2">
                                            <div class="bg-light p-2 rounded">
                                                <strong class="text-primary">Posibles Temas:</strong>
                                                <p class="mb-0 mt-1">{{ $review->posibles_temas_interes }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="bg-light p-2 rounded">
                                                <strong class="text-success">¿Qué saben?:</strong>
                                                <p class="mb-0 mt-1">{{ $review->que_sabe }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="bg-light p-2 rounded">
                                                <strong class="text-warning">¿Qué desean aprender?:</strong>
                                                <p class="mb-0 mt-1">{{ $review->que_desean_aprender }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="bg-light p-2 rounded">
                                                <strong class="text-danger">¿Qué necesitamos?:</strong>
                                                <p class="mb-0 mt-1">{{ $review->que_necesitamos }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <div class="bg-light p-2 rounded">
                                                <strong class="text-purple">¿Quiénes nos pueden apoyar?:</strong>
                                                <p class="mb-0 mt-1">{{ $review->quienes_nos_pueden_apoyar }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Summaries -->
    @if($summaries->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-list mr-2"></i>Resúmenes del Proyecto ({{ $summaries->count() }} elementos)
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
                                    @foreach($summaries as $summary)
                                        <tr>
                                            <td>
                                                <span class="badge badge-warning">{{ $summary->order ?? 'S/N' }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    @php
                                                        $evaluacion = $summary->pevaluacion;
                                                        $asignaturaName = $evaluacion->pensum->asignatura->name ?? 'N/A';
                                                        $asignaturaCode = $evaluacion->pensum->asignatura->code ?? '';
                                                    @endphp
                                                    <strong class="text-primary">{{ $asignaturaName }}</strong>
                                                    @if($asignaturaCode)
                                                        <br>
                                                        <small class="text-muted">[{{ $asignaturaCode }}] {{ $summary->componente }}</small>
                                                    @else
                                                        <br>
                                                        <small class="text-muted">{{ $summary->componente }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($summary->objetivo, 100) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($summary->aprendizaje_esperado, 100) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($summary->indicadores, 100) }}</small>
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

    <!-- Strategies - INTEGRACIÓN DE EIPROJECTKSTRATEGIES -->
    @if($strategies->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-users mr-2"></i>Estrategias del Proyecto ({{ $strategies->count() }} elementos)
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Resumen de progreso por día -->
                        @php
                            // Calcular progreso por día
                            $progressByDay = [];
                            foreach ($weekDays as $day => $dayName) {
                                $dayStrategies = $strategies->where('day_of_week', $day);
                                $completed = $dayStrategies->count();
                                $total = count($moments);
                                $percentage = $total > 0 ? ($completed / $total) * 100 : 0;
                                
                                $progressByDay[$day] = [
                                    'name' => $dayName,
                                    'completed' => $completed,
                                    'total' => $total,
                                    'percentage' => $percentage
                                ];
                            }
                        @endphp

                        <!-- Barras de progreso por día -->
                        <div class="row mb-4">
                            @foreach($progressByDay as $day => $dayProgress)
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="text-center">
                                        <h6 class="mb-1 small">{{ $dayProgress['name'] }}</h6>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $dayProgress['percentage'] == 100 ? 'success' : ($dayProgress['percentage'] >= 50 ? 'warning' : 'secondary') }}" 
                                                 style="width: {{ $dayProgress['percentage'] }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $dayProgress['completed'] }}/{{ $dayProgress['total'] }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Estrategias organizadas por día y momento -->
                        @foreach($weekDays as $day => $dayName)
                            @php
                                $dayStrategies = $strategies->where('day_of_week', $day);
                            @endphp
                            
                            @if($dayStrategies->count() > 0)
                                <div class="card mb-4 border-left border-success">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold text-success">
                                            <i class="fas fa-calendar-day mr-2"></i>{{ $dayName }}
                                            <span class="badge badge-success ml-2">{{ $dayStrategies->count() }} estrategias</span>
                                        </h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            @foreach($moments as $moment => $momentName)
                                                @php
                                                    $strategy = $dayStrategies->where('momento_rutina_diaria', $moment)->first();
                                                @endphp
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 font-weight-bold text-dark">
                                                                <span class="badge badge-{{ $strategy ? 'success' : 'secondary' }} mr-2">
                                                                    {{ $strategy ? ($strategy->order ?? 'S/N') : '--' }}
                                                                </span>
                                                                {{ $momentName }}
                                                            </h6>
                                                            @if($strategy && $strategy->estrategia)
                                                                <div class="mt-2 bg-light p-3 rounded">
                                                                    <strong class="text-primary">Estrategia:</strong>
                                                                    <p class="mb-0 mt-1 text-justify">{{ $strategy->estrategia }}</p>
                                                                </div>
                                                            @else
                                                                <div class="mt-2">
                                                                    <p class="text-muted font-italic mb-0">
                                                                        <i class="fas fa-info-circle mr-1"></i>
                                                                        No se ha definido estrategia para este momento
                                                                    </p>
                                                                </div>
                                                            @endif

                                                            @if($strategy && $strategy->description)
                                                                <div class="mt-2">
                                                                    <strong class="text-muted small">Descripción adicional:</strong>
                                                                    <p class="mb-0 mt-1 small text-muted">{{ $strategy->description }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-3">
                                                            @if($strategy)
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-check"></i>
                                                                </span>
                                                            @else
                                                                <span class="badge badge-secondary">
                                                                    <i class="fas fa-times"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Mostrar día vacío -->
                                <div class="card mb-3 border-left border-secondary">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold text-muted">
                                            <i class="fas fa-calendar-day mr-2"></i>{{ $dayName }}
                                            <span class="badge badge-secondary ml-2">0 estrategias</span>
                                        </h6>
                                    </div>
                                    <div class="card-body text-center py-4">
                                        <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No hay estrategias definidas para este día</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No strategies message -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-light">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay estrategias registradas</h5>
                        <p class="text-muted">Este proyecto no tiene estrategias definidas para los momentos de rutina.</p>
                        <button type="button" class="btn btn-success" 
                                wire:click="openModal('strategy', {{ $project->id }})">
                            <i class="fas fa-plus mr-1"></i>Agregar Estrategias
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- No Content Messages -->
    @if($reviews->count() == 0 && $summaries->count() == 0 && $strategies->count() == 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-light">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Proyecto sin contenido adicional</h5>
                        <p class="text-muted">Este proyecto no tiene revisiones, resúmenes ni estrategias registradas aún.</p>
                        <div class="mt-3">
                            <button type="button" class="btn btn-info mr-2" 
                                    wire:click="openModal('review', {{ $project->id }})">
                                <i class="fas fa-search mr-1"></i>Agregar Revisiones
                            </button>
                            <button type="button" class="btn btn-warning mr-2" 
                                    wire:click="openModal('summary', {{ $project->id }})">
                                <i class="fas fa-list mr-1"></i>Agregar Resúmenes
                            </button>
                            <button type="button" class="btn btn-success" 
                                    wire:click="openModal('strategy', {{ $project->id }})">
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
                            wire:click="openModal('edit', {{ $project->id }})">
                        <i class="fas fa-edit mr-1"></i>Editar Proyecto
                    </button>
                    <button type="button" class="btn btn-info" 
                            wire:click="openModal('review', {{ $project->id }})">
                        <i class="fas fa-search mr-1"></i>Gestionar Revisión
                    </button>
                    <button type="button" class="btn btn-warning" 
                            wire:click="openModal('summary', {{ $project->id }})">
                        <i class="fas fa-list mr-1"></i>Gestionar Resumen
                    </button>
                    <button type="button" class="btn btn-success" 
                            wire:click="openModal('strategy', {{ $project->id }})">
                        <i class="fas fa-users mr-1"></i>Gestionar Estrategias
                    </button>
                </div>
                
                <div class="btn-group">
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
        <h5 class="text-muted">Proyecto no encontrado</h5>
        <p class="text-muted">El proyecto solicitado no existe o ha sido eliminado.</p>
        <button type="button" class="btn btn-secondary" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cerrar
        </button>
    </div>
@endif