<div class="row">
    <!-- General Statistics Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Preguntas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $generalStats['total_questions'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Respuestas Registradas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $generalStats['total_responses'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sesiones Completadas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $generalStats['completed_sessions'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nueva tarjeta para mostrar la precisión de los estudiantes -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Precisión Estudiantes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $generalStats['student_accuracy'] }}%
                        </div>
                        @if ($generalStats['grado_name'] ?? false)
                            <div class="font-weight-bold text-muted">{{ $generalStats['grado_name'] }}</div>
                        @endif
                        <div class="text-xs text-muted mt-1">
                            {{ $generalStats['correct_answers'] }} <strong>Correctas</strong> /
                            {{ $generalStats['total_answered'] }} <strong>Total</strong>
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


<!-- Charts Row -->
<div class="row">

    <!-- Recent Sessions -->
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Sesiones Recientes</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm small table-striped">
                        <thead>
                            <tr class="alert-secondary">
                                <th>Estudiante</th>
                                <th>Área</th>
                                <th>Progreso</th>
                                <!-- <th>Estado</th> -->
                                <th>Iniciado</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach ($recentSessions as $session)
                                <tr>
                                    <td>{{ $session->estudiant->full_name ?? 'N/A' }}</td>
                                    <td>{{ $session->pensum->full_name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-info" style="width: {{ $session->progreso }}%">
                                            </div>
                                        </div>
                                        <small>{{ $session->progreso }}%</small>
                                    </td>
                                    <!--
                                    <td>
                                        @if ($session->completado_at)
<span class="badge badge-success">Completado</span>
@else
<span class="badge badge-warning">En Progreso</span>
@endif
                                    </td>
                                    -->
                                    <td class="text-xs">
                                        {{ $session->iniciado_at ? \Carbon\Carbon::parse($session->iniciado_at)->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions by Type Progress Bars -->
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Preguntas por Tipo</h6>
            </div>
            <div class="card-body">
                @php
                    $maxTypeCount = $questionsByType->max('count') ?: 1;
                @endphp
                @foreach ($questionsByType as $index => $type)
                    @php
                        $percentage = ($type->count / $maxTypeCount) * 100;
                        $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-weight-bold">{{ $type->type }}</span>
                            <span class="badge badge-{{ $color }}">{{ $type->count }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $color }}" role="progressbar"
                                style="width: {{ $percentage }}%" aria-valuenow="{{ $type->count }}"
                                aria-valuemin="0" aria-valuemax="{{ $maxTypeCount }}">
                                {{ $type->count }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Questions by Difficulty Progress Bars -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Preguntas por Dificultad</h6>
            </div>
            <div class="card-body">
                @php
                    $maxDifficultyCount = $questionsByDifficulty->max('count') ?: 1;
                    $difficultyColors = [
                        'fácil' => 'success',
                        'medio' => 'warning',
                        'difícil' => 'danger',
                        'muy difícil' => 'dark',
                    ];
                @endphp
                @foreach ($questionsByDifficulty as $difficulty)
                    @php
                        $percentage = ($difficulty->count / $maxDifficultyCount) * 100;
                        $color = $difficultyColors[strtolower($difficulty->difficulty)] ?? 'primary';
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-weight-bold">{{ ucfirst($difficulty->difficulty) }}</span>
                            <span class="badge badge-{{ $color }}">{{ $difficulty->count }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $color }}" role="progressbar"
                                style="width: {{ $percentage }}%" aria-valuenow="{{ $difficulty->count }}"
                                aria-valuemin="0" aria-valuemax="{{ $maxDifficultyCount }}">
                                {{ $difficulty->count }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<!-- Pensum Progress with filter and pagination -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Resumen de Preguntas por Área de Formación</h6>
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center">

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped small w-100">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-xs">Área de Formación</th>
                                <th class="text-xs text-center">Preguntas</th>
                                <th class="text-xs text-center">Sesiones</th>
                                <th class="text-xs text-center">Completadas</th>
                                <th class="text-xs text-center">% Finalización</th>
                                <th class="text-xs text-center">Precisión</th>
                                <th class="text-xs text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse ($pensumProgress as $pensum)
                                <tr>
                                    <td class="font-weight-bold text-sm">{{ $pensum->fullname }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $pensum->total_questions }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $pensum->total_sessions }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $pensum->completed_sessions }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress mr-2" style="width: 60px; height: 8px;">
                                                <div class="progress-bar
                                                    @if ($pensum->completion_percentage >= 80) bg-success
                                                    @elseif($pensum->completion_percentage >= 50) bg-warning
                                                    @else bg-danger @endif"
                                                    style="width: {{ $pensum->completion_percentage ?? 0 }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ number_format($pensum->completion_percentage ?? 0, 1) }}%</small>
                                        </div>
                                    </td>

                                    <!-- Added precision display with color coding -->
                                    <td class="text-center text-xs">
                                        @php
                                            $precision = $pensum->precision_by_pensum;
                                        @endphp
                                        @if ($precision !== null)
                                            @if ($precision >= 80)
                                                <span class="badge badge-success badge-pill"
                                                    style="font-size: 0.8rem">{{ $precision }}%</span>
                                            @elseif($precision >= 60)
                                                <span class="badge badge-warning badge-pill"
                                                    style="font-size: 0.8rem">{{ $precision }}%</span>
                                            @else
                                                <span class="badge badge-danger badge-pill"
                                                    style="font-size: 0.8rem">{{ $precision }}%</span>
                                            @endif
                                        @else
                                            <span class="text-muted" style="font-size: 0.8rem">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($pensum->completion_percentage >= 80)
                                            <span class="badge badge-success">Excelente</span>
                                        @elseif($pensum->completion_percentage >= 50)
                                            <span class="badge badge-warning">Regular</span>
                                        @elseif($pensum->completion_percentage > 0)
                                            <span class="badge badge-danger">Bajo</span>
                                        @else
                                            <span class="badge badge-secondary">Sin datos</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No hay datos disponibles
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination controls -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        @if ($pensumProgress && method_exists($pensumProgress, 'firstItem'))
                            Mostrando {{ $pensumProgress->firstItem() ?? 0 }} a {{ $pensumProgress->lastItem() ?? 0 }}
                            de {{ $pensumProgress->total() }} resultados
                        @else
                            Mostrando {{ $pensumProgress->count() }} resultados
                        @endif
                    </div>
                    <div>
                        @if ($pensumProgress && method_exists($pensumProgress, 'hasPages') && $pensumProgress->hasPages())
                            <nav aria-label="Pagination Navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($pensumProgress->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">Anterior</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <button type="button" class="page-link"
                                                wire:click="previousPage('pensumPage')">
                                                Anterior
                                            </button>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $start = max($pensumProgress->currentPage() - 2, 1);
                                        $end = min($start + 4, $pensumProgress->lastPage());
                                        $start = max($end - 4, 1);
                                    @endphp

                                    @if ($start > 1)
                                        <li class="page-item">
                                            <button type="button" class="page-link"
                                                wire:click="gotoPage(1, 'pensumPage')">
                                                1
                                            </button>
                                        </li>
                                        @if ($start > 2)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    @for ($page = $start; $page <= $end; $page++)
                                        @if ($page == $pensumProgress->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="gotoPage({{ $page }}, 'pensumPage')">
                                                    {{ $page }}
                                                </button>
                                            </li>
                                        @endif
                                    @endfor

                                    @if ($end < $pensumProgress->lastPage())
                                        @if ($end < $pensumProgress->lastPage() - 1)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="page-item">
                                            <button type="button" class="page-link"
                                                wire:click="gotoPage({{ $pensumProgress->lastPage() }}, 'pensumPage')">
                                                {{ $pensumProgress->lastPage() }}
                                            </button>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($pensumProgress->hasMorePages())
                                        <li class="page-item">
                                            <button type="button" class="page-link"
                                                wire:click="nextPage('pensumPage')">
                                                Siguiente
                                            </button>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">Siguiente</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
