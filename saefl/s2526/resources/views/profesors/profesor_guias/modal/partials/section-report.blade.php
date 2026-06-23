@if(isset($report) && $report)
<!-- Contenido del Reporte de Sección -->
<div class="section-report-content" id="report-{{ $report->id }}">
    
    <!-- Cabecera del Reporte -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body bg-gradient-light rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="font-weight-bold text-dark mb-1">
                        {{ $report->section->grado->name ?? 'Grado' }} - Sección {{ $report->section->name }}
                    </h4>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-alt mr-1"></i> Diagnóstico ID: {{ $report->diagnostic_id }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-users mr-1"></i> Estudiantes: {{ $report->students_count }}
                    </p>
                </div>
                <div class="col-md-4 text-md-right">
                    <div class="d-inline-block p-3 border rounded bg-white shadow">
                        <div class="small text-uppercase text-muted font-weight-bold">Precisión Global</div>
                        <div class="display-4 font-weight-bold mb-0 
                            {{ $report->global_precision_avg >= 75 ? 'text-success' : 
                               ($report->global_precision_avg >= 50 ? 'text-warning' : 'text-danger') }}">
                            {{ number_format($report->global_precision_avg, 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Síntesis Global -->
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4 border-0 h-100">
                <div class="card-header bg-white font-weight-bold text-uppercase small text-primary">
                    <i class="fas fa-chart-line mr-2"></i> Síntesis del Grupo
                </div>
                <div class="card-body">
                    <!-- Resumen Global -->
                    @if(isset($report->globalResult->global_summary))
                    <div class="alert alert-light border-left-primary border-left-4">
                        <p class="mb-0 font-italic">
                            <i class="fas fa-quote-left text-primary mr-2"></i>
                            {{ $report->globalResult->global_summary }}
                        </p>
                    </div>
                    @endif

                    <!-- Distribución por Niveles -->
                    <h6 class="font-weight-bold small text-uppercase text-muted mb-3">
                        <i class="fas fa-chart-pie mr-2"></i> Distribución por Niveles
                    </h6>
                    
                    @php
                        $dist = $report->globalResult->precision_distribution ?? [
                            'HIGH' => 0,
                            'MEDIUM' => 0,
                            'LOW' => 0,
                        ];
                        $total = array_sum($dist) ?: 1;
                    @endphp
                    
                    <div class="mb-4">
                        <div class="progress shadow-sm mb-3" style="height: 28px; border-radius: 14px;">
                            <div class="progress-bar bg-success progress-bar-striped" 
                                 role="progressbar" 
                                 style="width: {{ ($dist['HIGH'] / $total) * 100 }}%"
                                 title="Alto: {{ $dist['HIGH'] }} estudiantes">
                                {{ $dist['HIGH'] > 0 ? "ALTO ({$dist['HIGH']})" : '' }}
                            </div>
                            <div class="progress-bar bg-warning progress-bar-striped" 
                                 role="progressbar" 
                                 style="width: {{ ($dist['MEDIUM'] / $total) * 100 }}%"
                                 title="Medio: {{ $dist['MEDIUM'] }} estudiantes">
                                {{ $dist['MEDIUM'] > 0 ? "MEDIO ({$dist['MEDIUM']})" : '' }}
                            </div>
                            <div class="progress-bar bg-danger progress-bar-striped" 
                                 role="progressbar" 
                                 style="width: {{ ($dist['LOW'] / $total) * 100 }}%"
                                 title="Bajo: {{ $dist['LOW'] }} estudiantes">
                                {{ $dist['LOW'] > 0 ? "BAJO ({$dist['LOW']})" : '' }}
                            </div>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-2 bg-success-soft rounded">
                                    <div class="h4 font-weight-bold text-success">{{ $dist['HIGH'] }}</div>
                                    <div class="small text-muted">Alto Desempeño</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-warning-soft rounded">
                                    <div class="h4 font-weight-bold text-warning">{{ $dist['MEDIUM'] }}</div>
                                    <div class="small text-muted">Desempeño Medio</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-danger-soft rounded">
                                    <div class="h4 font-weight-bold text-danger">{{ $dist['LOW'] }}</div>
                                    <div class="small text-muted">Requiere Apoyo</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estilos de Aprendizaje -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-brain fa-2x text-info mb-3"></i>
                                    <div class="small text-uppercase text-muted font-weight-bold">
                                        Estilo de Pensamiento
                                    </div>
                                    <div class="h5 font-weight-bold text-dark mt-2">
                                        {{ $report->profile->dominant_processing_style ?? 'No determinado' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-lightbulb fa-2x text-warning mb-3"></i>
                                    <div class="small text-uppercase text-muted font-weight-bold">
                                        Estilo de Aprendizaje
                                    </div>
                                    <div class="h5 font-weight-bold text-dark mt-2">
                                        {{ $report->profile->dominant_learning_style ?? 'No determinado' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perfil Pedagógico -->
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4 border-0 h-100">
                <div class="card-header bg-white font-weight-bold text-uppercase small text-info">
                    <i class="fas fa-user-check mr-2"></i> Perfil Pedagógico
                </div>
                <div class="card-body">
                    <!-- Fortalezas -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-success">
                            <i class="fas fa-plus-circle mr-1"></i> Fortalezas del Grupo
                        </h6>
                        <div class="p-3 bg-success-soft rounded border-left-success border-left-3">
                            @if(isset($report->profile->strengths))
                                @if(is_array($report->profile->strengths))
                                    @foreach($report->profile->strengths as $strength)
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-check text-success mt-1 mr-2"></i>
                                        <span>{{ $strength }}</span>
                                    </div>
                                    @endforeach
                                @else
                                    {{ $report->profile->strengths }}
                                @endif
                            @else
                                <span class="text-muted italic">Sin fortalezas identificadas</span>
                            @endif
                        </div>
                    </div>

                    <!-- Necesidades -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-danger">
                            <i class="fas fa-exclamation-circle mr-1"></i> Necesidades de Apoyo
                        </h6>
                        <div class="p-3 bg-danger-soft rounded border-left-danger border-left-3">
                            @if(isset($report->profile->needs))
                                @if(is_array($report->profile->needs))
                                    @foreach($report->profile->needs as $need)
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-times text-danger mt-1 mr-2"></i>
                                        <span>{{ $need }}</span>
                                    </div>
                                    @endforeach
                                @else
                                    {{ $report->profile->needs }}
                                @endif
                            @else
                                <span class="text-muted italic">Sin necesidades identificadas</span>
                            @endif
                        </div>
                    </div>

                    <!-- Resumen Cognitivo -->
                    @if(isset($report->profile->cognitive_summary))
                    <div class="mt-4">
                        <h6 class="font-weight-bold text-primary">
                            <i class="fas fa-info-circle mr-1"></i> Resumen Cognitivo
                        </h6>
                        <p class="small text-muted text-justify">
                            {{ $report->profile->cognitive_summary }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Rendimiento por Áreas -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white font-weight-bold text-uppercase small text-dark">
            <i class="fas fa-book mr-2"></i> Rendimiento por Área de Formación
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small font-weight-bold">
                        <tr>
                            <th class="px-4 py-3">Área</th>
                            <th class="py-3 text-center">Precisión</th>
                            <th class="py-3">Debilidades</th>
                            <th class="py-3">Fortalezas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->areaResults as $area)
                        <tr>
                            <td class="px-4 font-weight-bold">{{ $area->area_name }}</td>
                            <td class="text-center" style="width: 200px;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="mr-3">
                                        <span class="font-weight-bold 
                                            {{ $area->precision_avg >= 75 ? 'text-success' : 
                                               ($area->precision_avg >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($area->precision_avg, 1) }}%
                                        </span>
                                    </div>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar 
                                            {{ $area->precision_avg >= 75 ? 'bg-success' : 
                                               ($area->precision_avg >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                            role="progressbar"
                                            style="width: {{ $area->precision_avg }}%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="small">
                                @php $weaknesses = $area->insights->where('type', 'weakness')->take(2); @endphp
                                @if($weaknesses->count() > 0)
                                <ul class="pl-3 mb-0">
                                    @foreach($weaknesses as $insight)
                                    <li class="text-danger mb-1">
                                        <small>{{ $insight->description }}</small>
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="text-muted italic">Sin debilidades críticas</span>
                                @endif
                            </td>
                            <td class="small">
                                @php $strengths = $area->insights->where('type', 'strength')->take(2); @endphp
                                @if($strengths->count() > 0)
                                <ul class="pl-3 mb-0">
                                    @foreach($strengths as $insight)
                                    <li class="text-success mb-1">
                                        <small>{{ $insight->description }}</small>
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="text-muted italic">Sin fortalezas registradas</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recomendaciones y Hallazgos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4 border-0 h-100">
                <div class="card-header bg-white font-weight-bold text-uppercase small text-dark">
                    <i class="fas fa-bullseye mr-2"></i> Recomendaciones
                </div>
                <div class="card-body">
                    @if($report->recommendations->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($report->recommendations->take(5) as $rec)
                        <div class="list-group-item border-0 px-0 py-2">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-chevron-circle-right text-primary mt-1 mr-2"></i>
                                <div>
                                    <small>{{ $rec->recommendation }}</small>
                                    <div>
                                        <span class="badge badge-light text-muted small">
                                            Para: {{ $rec->type }}
                                        </span>
                                        @if($rec->priority == 'HIGH')
                                        <span class="badge badge-danger small ml-1">Alta prioridad</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>No hay recomendaciones disponibles</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm mb-4 border-0 h-100">
                <div class="card-header bg-white font-weight-bold text-uppercase small text-dark">
                    <i class="fas fa-balance-scale mr-2"></i> Hallazgos Críticos
                </div>
                <div class="card-body">
                    @if(isset($report->contrast->gaps))
                    <div class="alert alert-warning border-left-warning border-left-3">
                        <p class="mb-0 small">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ $report->contrast->gaps }}
                        </p>
                    </div>
                    @endif
                    
                    @if(isset($report->contrast->critical_subjects))
                    <h6 class="font-weight-bold text-danger small mb-3">
                        <i class="fas fa-fire mr-1"></i> Atención Urgente en:
                    </h6>
                    <div class="d-flex flex-wrap">
                        @foreach($report->contrast->critical_subjects as $subject)
                        <span class="badge badge-danger px-3 py-2 mr-2 mb-2">
                            {{ $subject }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning text-center">
    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
    <h5 class="font-weight-bold">Reporte no disponible</h5>
    <p>No se pudo cargar la información del reporte.</p>
</div>
@endif