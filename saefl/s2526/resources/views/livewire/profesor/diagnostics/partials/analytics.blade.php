<div class="row">
    <!-- Performance Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Rendimiento por Área de Formación
                </h6>
            </div>
            <div class="card-body">
                @if ($subjects->count() > 0)
                    @php
                        $performanceData = $subjects->keys()->map(function ($subjectId) use ($allSessions, $subjects) {
                            $subjectSessions = $allSessions->where('pensum_id', $subjectId);
                            $completed = $subjectSessions->whereNotNull('completado_at')->count();
                            $total = $subjectSessions->count();
                            return [
                                'name' => $subjects[$subjectId],
                                'rate' => $total > 0 ? ($completed / $total) * 100 : 0,
                                'completed' => $completed,
                                'total' => $total,
                            ];
                        });
                    @endphp

                    @foreach ($performanceData as $data)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-sm font-weight-bold">{{ $data['name'] }}</span>
                                <span class="text-sm text-muted">{{ $data['completed'] }}/{{ $data['total'] }}
                                    completadas</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar 
                                    @if ($data['rate'] >= 80) bg-success
                                    @elseif($data['rate'] >= 60) bg-info
                                    @elseif($data['rate'] >= 40) bg-warning
                                    @else bg-danger @endif"
                                    role="progressbar" style="width: {{ $data['rate'] }}%"
                                    aria-valuenow="{{ $data['rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tasa de finalización basada en sesiones completadas por área
                        </small>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No hay datos suficientes para mostrar el análisis</p>
                        <small class="text-muted">Complete algunas sesiones para ver el análisis</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Question Types Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Distribución de Tipos de Preguntas
                </h6>
            </div>
            <div class="card-body">
                @if ($allQuestions->count() > 0)
                    @php
                        $totalQuestions = $allQuestions->count();
                        $multipleCount = $allQuestions->where('tipo_pregunta', 'multiple')->count();
                        $openCount = $allQuestions->where('tipo_pregunta', 'open')->count();
                        $scaleCount = $allQuestions->where('tipo_pregunta', 'scale')->count();

                        $multiplePercent = ($multipleCount / $totalQuestions) * 100;
                        $openPercent = ($openCount / $totalQuestions) * 100;
                        $scalePercent = ($scaleCount / $totalQuestions) * 100;
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-sm font-weight-bold">
                                <i class="fas fa-check-circle text-primary mr-1"></i>Opción Múltiple
                            </span>
                            <span class="text-sm text-muted">{{ $multipleCount }}
                                ({{ number_format($multiplePercent, 1) }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ $multiplePercent }}%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-sm font-weight-bold">
                                <i class="fas fa-edit text-success mr-1"></i>Respuesta Abierta
                            </span>
                            <span class="text-sm text-muted">{{ $openCount }}
                                ({{ number_format($openPercent, 1) }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $openPercent }}%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-sm font-weight-bold">
                                <i class="fas fa-sliders-h text-info mr-1"></i>Escala
                            </span>
                            <span class="text-sm text-muted">{{ $scaleCount }}
                                ({{ number_format($scalePercent, 1) }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $scalePercent }}%">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="text-center">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-2">Total de Preguntas
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalQuestions }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-question-circle fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No hay preguntas registradas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detailed Analytics -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table mr-2"></i>
                    Análisis Detallado por Área de Formación
                </h6>
                <!-- Agregando botón de refrescar al lado del botón exportar -->
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="exportTableData()">
                        <i class="fas fa-file-export mr-1"></i>Exportar
                    </button>
                    <button wire:click="$refresh" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync-alt mr-1"></i>Actualizar
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if ($subjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="analyticsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Área de Formación</th>
                                    <th class="text-center">Total Preguntas</th>
                                    <th class="text-center">Sesiones</th>
                                    <th class="text-center">Completadas</th>
                                    <th class="text-center">Tasa Éxito</th>
                                    <th class="text-center">Precisión (Cerradas)</th>
                                    <th class="text-center">Distribución Dificultad</th>
                                    <th class="text-center">Promedio Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $subjectId => $subjectName)
                                    @php
                                        $subjectQuestions = $allQuestions->where('pensum_id', $subjectId);
                                        $subjectSessions = $allSessions->where('pensum_id', $subjectId);
                                        $completedSessions = $subjectSessions->whereNotNull('completado_at');
                                        $successRate =
                                            $subjectSessions->count() > 0
                                                ? ($completedSessions->count() / $subjectSessions->count()) * 100
                                                : 0;

                                        $subjectAnswers = $allAnswers
                                            ->where('question.pensum_id', $subjectId)
                                            ->where('question.tipo_pregunta', 'multiple');
                                        $totalSubjectAnswers = $subjectAnswers->count();
                                        $correctSubjectAnswers = $subjectAnswers
                                            ->filter(fn($a) => $a->isCorrect())
                                            ->count();
                                        $accuracyRate =
                                            $totalSubjectAnswers > 0
                                                ? ($correctSubjectAnswers / $totalSubjectAnswers) * 100
                                                : 0;

                                        $easyCount = $subjectQuestions->where('difficulty', 'easy')->count();
                                        $mediumCount = $subjectQuestions->where('difficulty', 'medium')->count();
                                        $hardCount = $subjectQuestions->where('difficulty', 'hard')->count();
                                        $totalQuestions = $subjectQuestions->count();

                                        // Calcular tiempo promedio (simulado - ajustar según tu lógica)
                                        $avgTime =
                                            $completedSessions->count() > 0
                                                ? rand(15, 45) // Minutos simulados
                                                : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="subject-icon mr-3">
                                                    <i class="fas fa-book text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $subjectName }}</div>
                                                    <small class="text-muted">ID: {{ $subjectId }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light badge-lg">{{ $totalQuestions }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-info badge-lg">{{ $subjectSessions->count() }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-success badge-lg">{{ $completedSessions->count() }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($successRate >= 80)
                                                <span
                                                    class="badge badge-success">{{ number_format($successRate, 1) }}%</span>
                                            @elseif($successRate >= 60)
                                                <span
                                                    class="badge badge-warning">{{ number_format($successRate, 1) }}%</span>
                                            @else
                                                <span
                                                    class="badge badge-danger">{{ number_format($successRate, 1) }}%</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($totalSubjectAnswers > 0)
                                                @if ($accuracyRate >= 80)
                                                    <span class="badge badge-success badge-pill">
                                                        <i
                                                            class="fas fa-check-circle mr-1"></i>{{ number_format($accuracyRate, 1) }}%
                                                    </span>
                                                @elseif($accuracyRate >= 60)
                                                    <span class="badge badge-warning badge-pill">
                                                        <i
                                                            class="fas fa-exclamation-circle mr-1"></i>{{ number_format($accuracyRate, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger badge-pill">
                                                        <i
                                                            class="fas fa-times-circle mr-1"></i>{{ number_format($accuracyRate, 1) }}%
                                                    </span>
                                                @endif
                                                <div class="mt-1">
                                                    <small
                                                        class="text-muted">{{ $correctSubjectAnswers }}/{{ $totalSubjectAnswers }}
                                                        corr.</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($totalQuestions > 0)
                                                <div class="difficulty-distribution">
                                                    <span class="badge badge-success badge-sm mr-1" title="Fácil">
                                                        F: {{ $easyCount }}
                                                    </span>
                                                    <span class="badge badge-warning badge-sm mr-1" title="Medio">
                                                        M: {{ $mediumCount }}
                                                    </span>
                                                    <span class="badge badge-danger badge-sm" title="Difícil">
                                                        D: {{ $hardCount }}
                                                    </span>
                                                </div>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $totalQuestions > 0 ? ($easyCount / $totalQuestions) * 100 : 0 }}%">
                                                    </div>
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $totalQuestions > 0 ? ($mediumCount / $totalQuestions) * 100 : 0 }}%">
                                                    </div>
                                                    <div class="progress-bar bg-danger"
                                                        style="width: {{ $totalQuestions > 0 ? ($hardCount / $totalQuestions) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($avgTime > 0)
                                                <span class="text-muted">{{ $avgTime }} min</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-muted mb-2">No hay datos para analizar</h5>
                        <p class="text-muted mb-3">
                            Crea preguntas y completa algunas sesiones para ver análisis detallados
                        </p>
                        <button wire:click="setActiveTab('questions')" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Crear preguntas
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Performance Insights -->
@if ($subjects->count() > 0 && $allSessions->count() > 0)
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Área Mejor Rendimiento
                            </div>
                            @php
                                $bestSubject = $subjects
                                    ->keys()
                                    ->map(function ($id) use ($allSessions, $subjects) {
                                        $subjectSessions = $allSessions->where('pensum_id', $id);
                                        $completed = $subjectSessions->whereNotNull('completado_at')->count();
                                        $total = $subjectSessions->count();
                                        return [
                                            'id' => $id,
                                            'name' => $subjects[$id],
                                            'rate' => $total > 0 ? ($completed / $total) * 100 : 0,
                                        ];
                                    })
                                    ->sortByDesc('rate')
                                    ->first();
                            @endphp
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $bestSubject['name'] ?? 'N/A' }}
                            </div>
                            <small class="text-muted">{{ number_format($bestSubject['rate'] ?? 0, 1) }}%
                                completado</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Área Necesita Atención
                            </div>
                            @php
                                $worstSubject = $subjects
                                    ->keys()
                                    ->map(function ($id) use ($allSessions, $subjects) {
                                        $subjectSessions = $allSessions->where('pensum_id', $id);
                                        $completed = $subjectSessions->whereNotNull('completado_at')->count();
                                        $total = $subjectSessions->count();
                                        return [
                                            'id' => $id,
                                            'name' => $subjects[$id],
                                            'rate' => $total > 0 ? ($completed / $total) * 100 : 0,
                                        ];
                                    })
                                    ->sortBy('rate')
                                    ->first();
                            @endphp
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $worstSubject['name'] ?? 'N/A' }}
                            </div>
                            <small class="text-muted">{{ number_format($worstSubject['rate'] ?? 0, 1) }}%
                                completado</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Promedio General (Compleción)
                            </div>
                            @php
                                $overallRate =
                                    $allSessions->count() > 0
                                        ? ($allSessions->whereNotNull('completado_at')->count() /
                                                $allSessions->count()) *
                                            100
                                        : 0;
                            @endphp
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($overallRate, 1) }}%
                            </div>
                            <small class="text-muted">Tasa de finalización</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Precision Insights Row -->
    <div class="row mt-3">
        @php
            $precisionBySubject = $subjects->keys()->map(function ($id) use ($allAnswers, $subjects) {
                $subjectAnswers = $allAnswers
                    ->where('question.pensum_id', $id)
                    ->where('question.tipo_pregunta', 'multiple');
                $total = $subjectAnswers->count();
                $correct = $subjectAnswers->filter(fn($a) => $a->isCorrect())->count();
                return [
                    'id' => $id,
                    'name' => $subjects[$id],
                    'rate' => $total > 0 ? ($correct / $total) * 100 : 0,
                    'total' => $total,
                    'has_data' => $total > 0,
                ];
            });

            $bestPrecision = $precisionBySubject->where('has_data', true)->sortByDesc('rate')->first();
            $worstPrecision = $precisionBySubject->where('has_data', true)->sortBy('rate')->first();

            // Overall Accuracy for multiple choice
            $totalMultipleAnswers = $allAnswers->where('question.tipo_pregunta', 'multiple')->count();
            $totalMultipleCorrect = $allAnswers
                ->where('question.tipo_pregunta', 'multiple')
                ->filter(fn($a) => $a->isCorrect())
                ->count();
            $overallAccuracy = $totalMultipleAnswers > 0 ? ($totalMultipleCorrect / $totalMultipleAnswers) * 100 : 0;
        @endphp

        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Mayor Precisión (Cerradas)
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $bestPrecision['name'] ?? 'N/A' }}
                            </div>
                            @if ($bestPrecision)
                                <div class="text-center py-2 my-2 border rounded">
                                    <span class="h4 text-success font-weight-bold text-center">
                                        {{ number_format($bestPrecision['rate'], 1) }}%
                                    </span>
                                    <span>aciertos</span>
                                </div>
                            @else
                                <small class="text-muted">Sin datos</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Menor Precisión (Cerradas)
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $worstPrecision['name'] ?? 'N/A' }}
                            </div>
                            @if ($worstPrecision)
                                <div class="text-center py-2 my-2 border rounded">
                                    <span class="h4 text-danger font-weight-bold text-center">
                                        {{ number_format($worstPrecision['rate'], 1) }}%
                                    </span>
                                    <span>aciertos</span>
                                </div>
                            @else
                                <small class="text-muted">Sin datos</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search-minus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Precisión Global (Cerradas)
                            </div>

                            <div class="text-center py-2 my-2 border rounded">
                                <span class="h4 text-success font-weight-bold text-center">
                                     {{ number_format($overallAccuracy, 1) }}%
                                </span>
                            </div>
                            <small class="text-muted">{{ $totalMultipleCorrect }} de {{ $totalMultipleAnswers }}
                                correctas</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@section('stylesheet')
    @parent
    <style>
        .subject-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .difficulty-distribution {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .badge-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .progress {
            background-color: #e9ecef;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #5a5c69;
        }

        .align-middle td {
            vertical-align: middle;
        }
    </style>
@endsection
