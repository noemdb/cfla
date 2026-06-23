<!-- Mejoré completamente el modal de sesiones con mejor diseño y funcionalidad -->
@if ($SessionModalReport && $selectedSession)
    <div class="modal-backdrop fade show"
        style="z-index: 1040; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(2px);"></div>
    <div class="modal fade show d-block" style="z-index: 1050;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content shadow-lg border-0" style="max-height: 90vh;">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Detalles de Sesión Diagnóstica
                        <span class="badge bg-light text-primary ms-2">
                            ID: {{ $selectedSession->id }}
                        </span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeSessionModal"></button>
                </div>

                <div class="modal-body p-4" style="max-height: calc(90vh - 200px); overflow-y: auto;">
                    <!-- Mejoré el diseño de la información del estudiante y sesión -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100"
                                style="border-left: 4px solid #4e73df !important;">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-user me-2"></i>Información del Estudiante
                                    </h6>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Nombre Completo</small>
                                                <strong>{{ $selectedSession->estudiant->name ?? 'N/A' }}
                                                    {{ $selectedSession->estudiant->lastname ?? '' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">ID Estudiante</small>
                                                <strong>{{ $selectedSession->estudiant_id }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100"
                                style="border-left: 4px solid #1cc88a !important;">
                                <div class="card-body">
                                    <h6 class="card-title text-success mb-3">
                                        <i class="fas fa-book me-2"></i>Información de la Sesión
                                    </h6>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Área de Formación</small>
                                                <strong>{{ $selectedSession->pensum->asignatura->name ?? 'N/A' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Estado</small>
                                                @if ($selectedSession->completado_at)
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i>Completada
                                                    </span>
                                                @elseif($selectedSession->activo)
                                                    <span class="badge bg-warning px-3 py-2">
                                                        <i class="fas fa-clock me-1"></i>En progreso
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger px-3 py-2">
                                                        <i class="fas fa-times-circle me-1"></i>Abandonada
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mejoré el diseño del progreso con mejor visualización -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light border-0">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-chart-line me-2"></i>Progreso de la Sesión
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-bold">Progreso General</span>
                                                    <span
                                                        class="badge bg-primary fs-6">{{ $selectedSession->progreso }}%</span>
                                                </div>
                                                <div class="progress shadow-sm"
                                                    style="height: 15px; border-radius: 10px;">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated
                                                    @if ($selectedSession->progreso >= 100) bg-success
                                                    @elseif($selectedSession->progreso >= 50) bg-warning
                                                    @else bg-danger @endif"
                                                        role="progressbar"
                                                        style="width: {{ $selectedSession->progreso }}%;">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row text-center">
                                                <div class="col-3">
                                                    <div class="border-end">
                                                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                                            Iniciada</div>
                                                        <div class="small text-muted">
                                                            {{ $selectedSession->iniciado_at->format('d/m/Y H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="border-end">
                                                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                                            Completada</div>
                                                        <div class="small text-muted">
                                                            {{ $selectedSession->completado_at ? $selectedSession->completado_at->format('d/m/Y H:i') : 'Pendiente' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="border-end">
                                                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                                            Preguntas</div>
                                                        <div class="small text-muted">
                                                            {{ $selectedSession->total_preguntas ?? 0 }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                                        Duración</div>
                                                    <div class="small text-muted">
                                                        @if ($selectedSession->completado_at)
                                                            {{ $selectedSession->iniciado_at->diffInMinutes($selectedSession->completado_at) }}
                                                            min
                                                        @else
                                                            {{ $selectedSession->iniciado_at->diffInMinutes(now()) }}
                                                            min
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="session-status-icon mb-3">
                                                    @if ($selectedSession->completado_at)
                                                        <i class="fas fa-check-circle fa-4x text-success"></i>
                                                    @elseif($selectedSession->activo)
                                                        <i class="fas fa-clock fa-4x text-warning"></i>
                                                    @else
                                                        <i class="fas fa-times-circle fa-4x text-danger"></i>
                                                    @endif
                                                </div>
                                                <div class="fw-bold fs-5">
                                                    @if ($selectedSession->completado_at)
                                                        Sesión Completada
                                                    @elseif($selectedSession->activo)
                                                        En Progreso
                                                    @else
                                                        Sesión Abandonada
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mejoré la tabla de respuestas con mejor diseño y funcionalidad -->
                    @if ($selectedSession->answers && $selectedSession->answers->count() > 0)
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-list-alt me-2"></i>
                                                Respuestas del Estudiante
                                            </h6>
                                            <span class="badge bg-primary">{{ $selectedSession->answers->count() }}
                                                respuestas</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 5%;" class="text-center">#</th>
                                                        <th style="width: 45%;">Pregunta</th>
                                                        <th style="width: 35%;">Respuesta</th>
                                                        <th style="width: 10%;" class="text-center">Valor</th>
                                                        <th style="width: 5%;" class="text-center">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($selectedSession->answers as $index => $answer)
                                                        <tr class="border-bottom">
                                                            <td class="text-center align-middle">
                                                                <span
                                                                    class="badge bg-light text-dark fw-bold">{{ $index + 1 }}</span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="question-preview">
                                                                    <div class="fw-medium mb-1 text-dark">
                                                                        {{ Str::limit($answer->question->pregunta ?? 'Pregunta no disponible', 120) }}
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-tag me-1"></i>
                                                                        @if ($answer->question->tipo_pregunta === 'multiple')
                                                                            <span
                                                                                class="badge bg-primary bg-opacity-10 text-primary">Opción
                                                                                Múltiple</span>
                                                                        @elseif($answer->question->tipo_pregunta === 'open')
                                                                            <span
                                                                                class="badge bg-success bg-opacity-10 text-success">Respuesta
                                                                                Abierta</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-info bg-opacity-10 text-info">Escala</span>
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="answer-content">
                                                                    @if ($answer->question->tipo_pregunta === 'multiple' && $answer->selectedOption)
                                                                        <div
                                                                            class="selected-option p-2 bg-success bg-opacity-10 border-start border-success border-3 rounded">
                                                                            <i
                                                                                class="fas fa-check-circle text-success me-1"></i>
                                                                            <span
                                                                                class="fw-medium">{{ $answer->selectedOption->opcion }}</span>
                                                                        </div>
                                                                    @elseif($answer->respuesta)
                                                                        <div
                                                                            class="open-answer p-2 bg-info bg-opacity-10 border-start border-info border-3 rounded">
                                                                            <i
                                                                                class="fas fa-quote-left text-info me-1"></i>
                                                                            <span>{{ Str::limit($answer->respuesta, 150) }}</span>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted fst-italic">Sin
                                                                            respuesta</span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                @if ($answer->valor_numerico !== null)
                                                                    <span
                                                                        class="badge fs-6 px-3 py-2
                                                                @if ($answer->valor_numerico > 0) bg-success
                                                                @else bg-secondary @endif">
                                                                        {{ $answer->valor_numerico }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                @if ($answer->completado_at)
                                                                    <i class="fas fa-check-circle text-success fs-5"
                                                                        title="Respondida"></i>
                                                                @else
                                                                    <i class="fas fa-clock text-warning fs-5"
                                                                        title="Pendiente"></i>
                                                                @endif
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
                    @else
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center py-5">
                                        <i class="fas fa-clipboard-question fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted mb-2">No hay respuestas registradas</h5>
                                        <p class="text-muted">El estudiante aún no ha respondido ninguna pregunta en
                                            esta sesión.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Mejoré el footer del modal con mejor diseño y funcionalidad -->
                <div class="modal-footer bg-light border-top" style="position: sticky; bottom: 0; z-index: 10;">
                    <div class="d-flex justify-content-between w-100">
                        <div class="d-flex gap-2">
                            @if ($selectedSession->completado_at)
                                <button type="button" class="btn btn-success"
                                    onclick="generateReport({{ $selectedSession->id }})">
                                    <i class="fas fa-file-pdf me-1"></i>Generar Reporte
                                </button>
                                <button type="button" class="btn btn-info"
                                    onclick="exportSession({{ $selectedSession->id }})">
                                    <i class="fas fa-download me-1"></i>Exportar Datos
                                </button>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeSessionModal">
                            <i class="fas fa-times me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@section('stylesheet')
    @parent
    <style>
        /* Agregué estilos mejorados para el modal de sesiones */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .question-preview {
            line-height: 1.4;
        }

        .answer-content {
            max-width: 350px;
        }

        .selected-option,
        .open-answer {
            border-radius: 6px !important;
            font-size: 0.9rem;
        }

        .session-status-icon {
            padding: 1rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }

        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        .border-3 {
            border-width: 3px !important;
        }

        .border-start {
            border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
        }

        .fs-6 {
            font-size: 1rem !important;
        }

        .fs-5 {
            font-size: 1.25rem !important;
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        function generateReport(sessionId) {
            // Implementar generación de reporte
            console.log('Generando reporte para sesión:', sessionId);
        }

        function exportSession(sessionId) {
            // Implementar exportación de datos
            console.log('Exportando datos de sesión:', sessionId);
        }
    </script>
@endsection
