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