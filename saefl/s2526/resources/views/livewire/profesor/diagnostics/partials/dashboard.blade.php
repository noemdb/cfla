<!-- Dashboard Content -->
<div class="row">
    <!-- Indicador de área seleccionada -->
    @if ($selectedPensumId)
        <div class="col-12 mb-3">
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle mr-2 text-info"></i>
                    <span>Mostrando datos para:
                        <strong>{{ $profesor->pensums->find($selectedPensumId)->asignatura->full_name ?? 'Área seleccionada' }}</strong></span>
                    <button wire:click="$set('selectedPensumId', null)" class="btn btn-sm btn-outline-info ml-auto">
                        <i class="fas fa-times mr-1"></i>Ver todas
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 hover-shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Preguntas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_questions']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-chart-line mr-1"></i>
                            Banco de preguntas
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 hover-shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Sesiones
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_sessions']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-users mr-1"></i>
                            Estudiantes evaluados
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 hover-shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Sesiones Completadas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['completed_sessions']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Diagnósticos finalizados
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 hover-shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Precisión Estudiantes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['student_accuracy'] }}%
                        </div>
                        <div class="text-xs text-muted mt-1">
                            {{ $stats['correct_answers'] }}/{{ $stats['total_answered'] }} correctas
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Overview -->
@if ($stats['total_sessions'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen de Progreso</h6>
                    <small class="text-muted">Actualizado hace {{ now()->diffForHumans() }}</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-sm font-weight-bold">Sesiones Completadas</span>
                                    <span
                                        class="text-sm text-muted">{{ $stats['completed_sessions'] }}/{{ $stats['total_sessions'] }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $stats['total_sessions'] > 0 ? ($stats['completed_sessions'] / $stats['total_sessions']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>

                            @if (isset($stats['active_sessions']))
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-sm font-weight-bold">Sesiones Activas</span>
                                        <span class="text-sm text-muted">{{ $stats['active_sessions'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: {{ $stats['total_sessions'] > 0 ? ($stats['active_sessions'] / $stats['total_sessions']) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (isset($stats['student_accuracy']) && $stats['total_answered'] > 0)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-sm font-weight-bold">Precisión de Respuestas</span>
                                        <span class="text-sm text-muted">{{ $stats['student_accuracy'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar
                                            @if ($stats['student_accuracy'] >= 80) bg-success
                                            @elseif($stats['student_accuracy'] >= 60) bg-warning
                                            @else bg-danger @endif"
                                            role="progressbar" style="width: {{ $stats['student_accuracy'] }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="h4 mb-0 font-weight-bold text-primary">
                                    {{ $stats['total_questions'] > 0 ? round($stats['total_sessions'] / $stats['total_questions'], 1) : 0 }}
                                </div>
                                <div class="text-xs text-muted">Promedio sesiones por pregunta</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Preguntas Recientes</h6>
                <button wire:click="setActiveTab('questions')" class="btn btn-sm btn-outline-primary">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
            <div class="card-body">
                @if ($questions->count() > 0)
                    @foreach ($questions->take(5) as $question)
                        <div class="d-flex align-items-center mb-3 p-2 rounded hover-bg-light">
                            <div class="mr-3">
                                @if ($question->tipo_pregunta === 'multiple')
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-list-ul text-white"></i>
                                    </div>
                                @elseif($question->tipo_pregunta === 'open')
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-edit text-white"></i>
                                    </div>
                                @else
                                    <div class="icon-circle bg-info">
                                        <i class="fas fa-sliders-h text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-sm">{{ Str::limit($question->pregunta, 60) }}</div>
                                <small class="text-muted">
                                    <i
                                        class="fas fa-book mr-1"></i>{{ $question->pensum->asignatura->name ?? 'Sin asignatura' }}
                                    <span class="mx-2">•</span>
                                    <i
                                        class="fas fa-clock mr-1"></i>{{ $question->created_at->diffForHumans() ?? null }}
                                </small>
                            </div>
                            <div class="ml-2">
                                @if ($question->difficulty === 'easy')
                                    <span class="badge badge-success badge-sm">Fácil</span>
                                @elseif($question->difficulty === 'medium')
                                    <span class="badge badge-warning badge-sm">Medio</span>
                                @else
                                    <span class="badge badge-danger badge-sm">Difícil</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-question-circle fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No hay preguntas disponibles</p>
                        <button wire:click="openQuestionModal" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>Crear primera pregunta
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Sesiones Recientes</h6>
                <button wire:click="setActiveTab('sessions')" class="btn btn-sm btn-outline-primary">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
            <div class="card-body">
                @if ($sessions->count() > 0)
                    @foreach ($sessions->take(5) as $session)
                        <div class="d-flex align-items-center mb-3 p-2 rounded hover-bg-light">
                            <div class="mr-3">
                                @if ($session->completado_at)
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-check-circle text-white"></i>
                                    </div>
                                @elseif($session->activo)
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                @else
                                    <div class="icon-circle bg-danger">
                                        <i class="fas fa-times-circle text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-sm">
                                    {{ $session->estudiant->name ?? 'Estudiante' }}
                                    {{ $session->estudiant->lastname ?? '' }}
                                </div>
                                <small class="text-muted">
                                    <i
                                        class="fas fa-book mr-1"></i>{{ $session->pensum->asignatura->name ?? 'Sin asignatura' }}
                                    <span class="mx-2">•</span>
                                    {{-- <i class="fas fa-clock mr-1"></i>{{ $session->iniciado_at->diffForHumans() }} --}}
                                </small>
                                @if ($session->progreso > 0)
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $session->progreso }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-2">
                                @if ($session->completado_at)
                                    <span class="badge badge-success badge-sm">Completada</span>
                                @elseif($session->activo)
                                    <span class="badge badge-warning badge-sm">En progreso</span>
                                @else
                                    <span class="badge badge-danger badge-sm">Abandonada</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No hay sesiones disponibles</p>
                        <small class="text-muted">Las sesiones aparecerán cuando los estudiantes inicien
                            diagnósticos</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('stylesheet')
    @parent

    <style>
        .hover-shadow:hover {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .icon-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hover-bg-light:hover {
            background-color: #f8f9fc;
            transition: background-color 0.2s ease;
        }

        .badge-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
    </style>
@endsection
