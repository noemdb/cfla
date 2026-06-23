<div>
    <!-- Pestañas de días de la semana -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-week mr-2"></i>Estrategias por Día de la Semana
                </h6>
                <div class="text-right">
                    <small>
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ count($weekDays) }} días × {{ count($moments) }} momentos =
                        {{ count($weekDays) * count($moments) }} estrategias
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Nav tabs para días -->
            <ul class="nav nav-tabs nav-fill" role="tablist">
                @foreach ($weekDays as $day => $dayName)
                    @php
                        $progress = $this->getDayProgress($day);
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $activeDay === $day ? 'active' : '' }}" href="#"
                            wire:click.prevent="setActiveDay('{{ $day }}')" role="tab">
                            <div class="text-center">
                                <i class="fas fa-calendar-day mr-1"></i>
                                {{ $dayName }}
                                <br>
                                <small class="badge badge-{{ $progress['completed'] > 0 ? 'success' : 'secondary' }}">
                                    {{ $progress['completed'] }}/{{ $progress['total'] }}
                                </small>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <!-- Contenido del día activo -->
            <div class="p-4">
                <!-- Navegación de momentos -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-clock mr-2"></i>
                                Momentos de la Rutina - {{ $weekDays[$activeDay] }}
                            </h6>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" wire:click="previousMoment"
                                    {{ $activeMoment === array_key_first($moments) ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" wire:click="nextMoment"
                                    {{ $activeMoment === array_key_last($moments) ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Pills de momentos -->
                        <div class="row">
                            @foreach ($moments as $moment => $momentName)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <button type="button"
                                        class="btn btn-block btn-sm {{ $activeMoment === $moment ? 'btn-primary' : 'btn-outline-secondary' }}
                                                {{ !empty($strategies[$activeDay][$moment]['estrategia']) ? 'border-success' : '' }}"
                                        wire:click="setActiveMoment('{{ $moment }}')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-truncate">{{ $momentName }}</span>
                                            @if (!empty($strategies[$activeDay][$moment]['estrategia']))
                                                <i class="fas fa-check-circle text-success ml-1"></i>
                                            @endif
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Formulario del momento activo -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-edit mr-2"></i>
                                {{ $moments[$activeMoment] }} - {{ $weekDays[$activeDay] }}
                            </h6>
                            <div>
                                @if (!empty($strategies[$activeDay][$activeMoment]['id']))
                                    <span class="badge badge-light">
                                        <i class="fas fa-save mr-1"></i>Guardado
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-plus mr-1"></i>Nuevo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveCurrentStrategy">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            Estrategia para {{ $moments[$activeMoment] }} *
                                        </label>
                                        <textarea
                                            class="form-control @error('strategies.' . $activeDay . '.' . $activeMoment . '.estrategia') is-invalid @enderror"
                                            wire:model.defer="strategies.{{ $activeDay }}.{{ $activeMoment }}.estrategia" rows="6"
                                            placeholder="Describe la estrategia pedagógica para {{ strtolower($moments[$activeMoment]) }} del {{ strtolower($weekDays[$activeDay]) }}..."></textarea>
                                        @error('strategies.' . $activeDay . '.' . $activeMoment . '.estrategia')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">
                                            <i class="fas fa-sort-numeric-up mr-1"></i>
                                            Orden
                                        </label>
                                        <input type="number" min="1"
                                            class="form-control @error('strategies.' . $activeDay . '.' . $activeMoment . '.order') is-invalid @enderror"
                                            wire:model.defer="strategies.{{ $activeDay }}.{{ $activeMoment }}.order"
                                            placeholder="Orden">
                                        @error('strategies.' . $activeDay . '.' . $activeMoment . '.order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Botones de acción -->
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success btn-block mb-2">
                                            <i class="fas fa-save mr-1"></i>
                                            Guardar y Continuar
                                        </button>

                                        @if (!empty($strategies[$activeDay][$activeMoment]['id']))
                                            <button type="button" class="btn btn-outline-danger btn-block"
                                                wire:click="deleteStrategy('{{ $activeDay }}', '{{ $activeMoment }}')"
                                                onclick="confirm('¿Está seguro de eliminar esta estrategia?') || event.stopImmediatePropagation()">
                                                <i class="fas fa-trash mr-1"></i>Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Navegación entre momentos -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" wire:click="previousMoment"
                        {{ $activeMoment === array_key_first($moments) ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-left mr-1"></i>
                        Momento Anterior
                    </button>

                    <div class="text-center">
                        <small class="text-muted">
                            Momento {{ array_search($activeMoment, array_keys($moments)) + 1 }} de
                            {{ count($moments) }}
                        </small>
                    </div>

                    <button type="button" class="btn btn-outline-primary" wire:click="nextMoment"
                        {{ $activeMoment === array_key_last($moments) ? 'disabled' : '' }}>
                        Siguiente Momento
                        <i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Resumen general de progreso - ESTRUCTURA CORREGIDA -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-chart-pie mr-2"></i>Resumen General de Progreso
            </h6>
        </div>
        <div class="card-body">
            @php
                // CONTAR ESTRATEGIAS EXISTENTES POR DÍA (basado en day_of_week)
                $strategiesByDay = [];
                $totalStrategies = 0;
                $totalCompleted = 0;
                
                foreach ($weekDays as $day => $dayName) {
                    $strategiesByDay[$day] = [
                        'name' => $dayName,
                        'completed' => 0,
                        'total' => count($moments),
                        'percentage' => 0
                    ];
                    
                    // Contar estrategias completadas para este día
                    foreach ($moments as $moment => $momentName) {
                        if (!empty($strategies[$day][$moment]['estrategia'])) {
                            $strategiesByDay[$day]['completed']++;
                            $totalCompleted++;
                        }
                        $totalStrategies++;
                    }
                    
                    // Calcular porcentaje por día
                    $strategiesByDay[$day]['percentage'] = $strategiesByDay[$day]['total'] > 0 
                        ? ($strategiesByDay[$day]['completed'] / $strategiesByDay[$day]['total']) * 100 
                        : 0;
                }
                
                // Calcular progreso general
                $overallPercentage = $totalStrategies > 0 ? ($totalCompleted / $totalStrategies) * 100 : 0;
                
                // Contar días con progreso
                $daysWithProgress = 0;
                $daysCompleted = 0;
                foreach ($strategiesByDay as $dayData) {
                    if ($dayData['completed'] > 0) $daysWithProgress++;
                    if ($dayData['completed'] == $dayData['total']) $daysCompleted++;
                }
            @endphp

            <!-- Progreso por día -->
            <div class="row">
                @foreach ($strategiesByDay as $day => $dayData)
                    <div class="col-md-2 col-6 mb-3">
                        <div class="text-center">
                            <h6 class="mb-1">{{ $dayData['name'] }}</h6>
                            <div class="progress mb-2" style="height: 12px;">
                                <div class="progress-bar bg-{{ $dayData['percentage'] == 100 ? 'success' : ($dayData['percentage'] >= 50 ? 'warning' : 'secondary') }}" 
                                     role="progressbar"
                                     style="width: {{ $dayData['percentage'] }}%"
                                     title="{{ $dayData['completed'] }}/{{ $dayData['total'] }} momentos completados">
                                </div>
                            </div>
                            <small class="text-muted d-block">
                                {{ $dayData['completed'] }}/{{ $dayData['total'] }}
                            </small>
                            <small class="text-{{ $dayData['percentage'] == 100 ? 'success' : ($dayData['percentage'] >= 50 ? 'warning' : 'muted') }} font-weight-bold">
                                {{ number_format($dayData['percentage'], 0) }}%
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Progreso general -->
            <div class="mt-4 pt-3 border-top">
                <!-- Barra de progreso general -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-primary">Progreso General del Plan:</strong>
                        <span class="badge badge-{{ $overallPercentage >= 100 ? 'success' : ($overallPercentage >= 50 ? 'warning' : 'secondary') }} badge-lg">
                            {{ number_format($overallPercentage, 1) }}%
                        </span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-gradient-{{ $overallPercentage >= 100 ? 'success' : ($overallPercentage >= 50 ? 'warning' : 'secondary') }}" 
                             role="progressbar"
                             style="width: {{ $overallPercentage }}%">
                            {{ $totalCompleted }}/{{ $totalStrategies }} momentos con estrategia
                        </div>
                    </div>
                </div>

                <!-- Estadísticas detalladas -->
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border rounded p-2 bg-light">
                            <div class="h5 mb-1 text-primary">{{ $totalCompleted }}</div>
                            <small class="text-muted">Momentos con Estrategia</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border rounded p-2 bg-light">
                            <div class="h5 mb-1 text-info">{{ $totalStrategies - $totalCompleted }}</div>
                            <small class="text-muted">Momentos Pendientes</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border rounded p-2 bg-light">
                            <div class="h5 mb-1 text-success">{{ $daysCompleted }}</div>
                            <small class="text-muted">Días Completados</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border rounded p-2 bg-light">
                            <div class="h5 mb-1 text-warning">{{ $daysWithProgress - $daysCompleted }}</div>
                            <small class="text-muted">Días en Progreso</small>
                        </div>
                    </div>
                </div>

                <!-- Detalle por día -->
                <div class="mt-3">
                    <h6 class="text-muted mb-2">Detalle por Día:</h6>
                    <div class="row">
                        @foreach ($strategiesByDay as $day => $dayData)
                            <div class="col-md-4 mb-2">
                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                    <span class="small font-weight-bold">{{ $dayData['name'] }}</span>
                                    <div class="text-right">
                                        <span class="badge badge-{{ $dayData['percentage'] == 100 ? 'success' : ($dayData['percentage'] > 0 ? 'warning' : 'secondary') }}">
                                            {{ $dayData['completed'] }}/{{ $dayData['total'] }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $dayData['total'] - $dayData['completed'] }} pendientes
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recomendaciones -->
                <div class="mt-3">
                    <h6 class="text-muted mb-2">Recomendaciones:</h6>
                    @if ($totalCompleted == 0)
                        <div class="alert alert-secondary py-2 small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Comienza definiendo estrategias para los momentos del primer día.
                        </div>
                    @elseif ($overallPercentage < 30)
                        <div class="alert alert-warning py-2 small">
                            <i class="fas fa-lightbulb mr-1"></i>
                            ¡Buen comienzo! Tienes {{ $totalCompleted }} momentos con estrategia definida.
                        </div>
                    @elseif ($overallPercentage < 70)
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-tasks mr-1"></i>
                            ¡Progreso constante! Continúa completando los momentos pendientes.
                        </div>
                    @elseif ($overallPercentage < 100)
                        <div class="alert alert-success py-2 small">
                            <i class="fas fa-trophy mr-1"></i>
                            ¡Excelente! Estás cerca de completar todas las estrategias.
                        </div>
                    @else
                        <div class="alert alert-success py-2 small">
                            <i class="fas fa-check-circle mr-1"></i>
                            ¡Felicidades! Has completado todas las estrategias del plan.
                        </div>
                    @endif
                    
                    @if ($daysCompleted < count($weekDays))
                        <div class="alert alert-light py-2 small">
                            <i class="fas fa-calendar-check mr-1"></i>
                            <strong>Días por completar:</strong> 
                            @php
                                $incompleteDays = [];
                                foreach ($strategiesByDay as $day => $dayData) {
                                    if ($dayData['percentage'] < 100) {
                                        $incompleteDays[] = $dayData['name'];
                                    }
                                }
                            @endphp
                            {{ implode(', ', $incompleteDays) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Botones de acción principales -->
    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cerrar
        </button>

        <div>
            <button type="button" class="btn btn-success mr-2" wire:click="saveStrategies">
                <i class="fas fa-save mr-1"></i>Guardar Todas las Estrategias
            </button>
        </div>
    </div>
</div>

@section('stylesheet')
    @parent
    <style>
        .nav-tabs .nav-link {
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            padding: 0.75rem 0.5rem;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color: #007bff;
            background-color: #f8f9fa;
        }

        .nav-tabs .nav-link:hover {
            border-bottom-color: #6c757d;
        }

        .progress {
            height: 8px;
        }

        .progress-bar {
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-block {
            text-align: left;
        }

        .card-header.bg-info {
            background-color: #17a2b8 !important;
        }

        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
    </style>
@endsection