<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Gestión de Sesiones - Diagnósticos</h6>
                <div class="d-flex align-items-center">
                    </select>
                    <select wire:model="dateRange" class="form-control form-control-sm" style="width: auto;">
                        <option value="7">Últimos 7 días</option>
                        <option value="30">Últimos 30 días</option>
                        <option value="90">Últimos 90 días</option>
                        <option value="365">Último año</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <!-- Improved null check for filter information display -->
                @if (
                    $sessionsGradoFilter &&
                        isset($sessionsPaginated) &&
                        is_object($sessionsPaginated) &&
                        method_exists($sessionsPaginated, 'total'))
                    @php
                        $selectedGrado = null;
                        if ($profesor && $profesor->pensums) {
                            foreach ($profesor->pensums as $pensum) {
                                if ($pensum->grado_id == $sessionsGradoFilter) {
                                    $selectedGrado = $pensum->grado;
                                    break;
                                }
                            }
                        }
                        $totalStudents = $sessionsPaginated->total();
                    @endphp
                    @if ($selectedGrado)
                        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-filter mr-2"></i>
                            Mostrando <strong>{{ $totalStudents }}</strong> estudiantes para el grado
                            <strong>{{ $selectedGrado->name ?? 'N/A' }}</strong>
                            <button type="button" class="close" wire:click="$set('sessionsGradoFilter', '')"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr class="text-xs">
                                <th>Estudiante</th>
                                <th>Grados</th>
                                <th>Última Actividad</th>
                                <th>Tiempo Promedio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <!-- Updated to show student-based listing with consultation buttons -->
                            @if (isset($sessionsPaginated) && is_object($sessionsPaginated) && method_exists($sessionsPaginated, 'count'))
                                @forelse($sessionsPaginated as $studentData)
                                    <tr class="text-xs">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <div class="icon-circle bg-primary"
                                                        style="width: 25px; height: 25px;">
                                                        <i class="fas fa-user text-white" style="font-size: 10px;"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small font-weight-bold">
                                                        {{ $studentData->estudiant->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="small text-gray-500">
                                                        {{ $studentData->estudiant->gsemail ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-xs">
                                            @if ($studentData->grados && $studentData->grados->count() > 0)
                                                @foreach ($studentData->grados as $grado)
                                                    <span class="badge badge-secondary badge-sm mr-1">
                                                        {{ $grado->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-xs">
                                            {{ $studentData->last_session_date ? \Carbon\Carbon::parse($studentData->last_session_date)->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="text-xs">
                                            @if ($studentData->avg_duration_minutes)
                                                {{ round($studentData->avg_duration_minutes) }} min
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($studentData->completed_sessions == $studentData->total_sessions)
                                                <span class="badge badge-success badge-sm">Completado</span>
                                            @elseif($studentData->completed_sessions > 0)
                                                <span class="badge badge-warning badge-sm">En Progreso</span>
                                            @else
                                                <span class="badge badge-secondary badge-sm">Sin Completar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Added consultation buttons for professor view -->
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="showStudentDetails({{ $studentData->estudiant_id }})"
                                                    title="Ver Detalles del Estudiante">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    wire:click="showStudentSessions({{ $studentData->estudiant_id }})"
                                                    title="Ver Sesiones">
                                                    <i class="fas fa-list"></i>
                                                </button>

                                                @if ($filterDiagMainId)
                                                    @php
                                                        $existingReport = \App\Models\app\Instrument\DiagReport::where(
                                                            'estudiant_id',
                                                            $studentData->estudiant_id,
                                                        )
                                                            ->where('diag_main_id', $filterDiagMainId)
                                                            ->first();
                                                    @endphp

                                                    {{-- @if ($existingReport) --}}
                                                        <button type="button" class="btn btn-sm btn-success" {{(!$existingReport) ? 'disabled' : null}}
                                                            wire:click="getAIReport({{ $studentData->estudiant_id }}, {{ $filterDiagMainId }})"
                                                            title="Ver Reporte AI">
                                                            <i class="fas fa-file-alt"></i>
                                                        </button>
                                                    {{-- @endif --}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <br>No hay estudiantes disponibles
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                                        <br>Error al cargar los datos de estudiantes
                                        <br><small>Por favor, recarga la página</small>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Added pagination similar to evaluation view -->
                @if (isset($sessionsPaginated) && is_object($sessionsPaginated) && method_exists($sessionsPaginated, 'hasPages'))
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-sm text-gray-600">
                            Mostrando {{ $sessionsPaginated->firstItem() ?? 0 }} a
                            {{ $sessionsPaginated->lastItem() ?? 0 }}
                            de {{ $sessionsPaginated->total() }} estudiantes
                        </div>
                        <div>
                            @if ($sessionsPaginated->hasPages())
                                <nav aria-label="Pagination Navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        {{-- Previous Page Link --}}
                                        @if ($sessionsPaginated->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fas fa-chevron-left"></i>
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="previousPage('sessionsPage')"
                                                    wire:loading.attr="disabled" wire:target="previousPage">
                                                    <span wire:loading.remove wire:target="previousPage">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                    <span wire:loading wire:target="previousPage">
                                                        <i class="fas fa-spinner fa-spin"></i>
                                                    </span>
                                                </button>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @php
                                            $start = max($sessionsPaginated->currentPage() - 2, 1);
                                            $end = min($start + 4, $sessionsPaginated->lastPage());
                                            $start = max($end - 4, 1);
                                        @endphp

                                        @if ($start > 1)
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="gotoPage(1, 'sessionsPage')"
                                                    wire:loading.attr="disabled" wire:target="gotoPage">
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
                                            @if ($page == $sessionsPaginated->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <button type="button" class="page-link"
                                                        wire:click="gotoPage({{ $page }}, 'sessionsPage')"
                                                        wire:loading.attr="disabled" wire:target="gotoPage">
                                                        <span wire:loading.remove wire:target="gotoPage">
                                                            {{ $page }}
                                                        </span>
                                                        <span wire:loading wire:target="gotoPage">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </button>
                                                </li>
                                            @endif
                                        @endfor

                                        @if ($end < $sessionsPaginated->lastPage())
                                            @if ($end < $sessionsPaginated->lastPage() - 1)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="gotoPage({{ $sessionsPaginated->lastPage() }}, 'sessionsPage')"
                                                    wire:loading.attr="disabled" wire:target="gotoPage">
                                                    {{ $sessionsPaginated->lastPage() }}
                                                </button>
                                            </li>
                                        @endif

                                        {{-- Next Page Link --}}
                                        @if ($sessionsPaginated->hasMorePages())
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="nextPage('sessionsPage')" wire:loading.attr="disabled"
                                                    wire:target="nextPage">
                                                    <span wire:loading.remove wire:target="nextPage">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>
                                                    <span wire:loading wire:target="nextPage">
                                                        <i class="fas fa-spinner fa-spin"></i>
                                                    </span>
                                                </button>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Session Statistics -->
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Sesiones Totales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $generalStats['total_sessions'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tasa de Finalización
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ ($generalStats['total_sessions'] ?? 0) > 0 ? number_format((($generalStats['completed_sessions'] ?? 0) / $generalStats['total_sessions']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tiempo Promedio
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            45 min
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Added student details and sessions modals from evaluation view -->
<!-- Student Details Modal -->
@if ($showSessionDetailsModal && $selectedSessionData)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95% !important;">
            <div class="modal-content">
                <!-- Reducido padding del header -->
                <div class="modal-header bg-light text-dark py-2 font-weight-bold">
                    <h6 class="modal-title mb-0 font-weight-bold">
                        <i class="fas fa-user-graduate mr-1"></i>
                        Perfil Detallado del Estudiante
                    </h6>
                    <button type="button" class="close text-dark" wire:click="closeSessionDetailsModal">
                        <span>&times;</span>
                    </button>
                </div>
                <!-- Reducido padding del body -->
                <div class="modal-body p-3" style="max-height: 80vh; overflow-y: auto;">
                    <!-- Reducido margin bottom de información básica -->
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <!-- Reducido padding del card -->
                            <div class="card border-left-primary h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-primary font-weight-bold mb-2">
                                        <i class="fas fa-user mr-1"></i>Información del Estudiante
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Reducido margin bottom de párrafos -->
                                            <p class="mb-1 small"><strong>Nombre Completo:</strong></p>
                                            <p class="text-muted mb-2 small">
                                                {{ $selectedSessionData->estudiant->full_name ?? 'N/A' }}</p>

                                            <p class="mb-1 small"><strong>Identificación:</strong></p>
                                            <p class="text-muted mb-2 small">
                                                {{ $selectedSessionData->estudiant->ci_estudiant ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 small"><strong>Email:</strong></p>
                                            <p class="text-muted mb-2 small">
                                                {{ $selectedSessionData->estudiant->gsemail ?? ($selectedSessionData->estudiant->gsemail ?? 'N/A') }}
                                            </p>

                                            <p class="mb-1 small"><strong>Áreas de Formación:</strong></p>
                                            <p class="text-muted mb-2 small">
                                                {{ $selectedSessionData->unique_pensums ?? 0 }} área(s) activa(s)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Reducido padding del card de progreso -->
                            <div class="card border-left-success h-100">
                                <div class="card-body text-center p-3">
                                    <h6 class="text-success font-weight-bold mb-2">
                                        <i class="fas fa-chart-pie mr-1"></i>Progreso General
                                    </h6>
                                    <!-- Usar selectedSessionData en lugar de selectedSession -->
                                    <!-- Reducido height y margin de progress bar -->
                                    <div class="progress mb-2" style="height: 15px;">
                                        <div class="progress-bar bg-gradient-success"
                                            style="width: {{ $selectedSessionData->session_progress }}%">
                                            {{ $selectedSessionData->session_progress }}%
                                        </div>
                                    </div>
                                    <h5 class="text-success font-weight-bold mb-1">
                                        {{ $selectedSessionData->session_progress }}%</h5>
                                    <small class="text-muted">
                                        {{ $selectedSessionData->answered_questions }} de
                                        {{ $selectedSessionData->total_preguntas }} preguntas del grado
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reducido margin bottom de estadísticas -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <!-- Reducido padding de cards de estadísticas -->
                            <div class="card border-left-info text-center">
                                <div class="card-body p-2">
                                    <i class="fas fa-tasks text-info fa-lg mb-1"></i>
                                    <h5 class="text-info font-weight-bold mb-0">
                                        {{ $selectedSessionData->total_sessions }}</h5>
                                    <small class="text-muted">Sesiones Totales</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success text-center">
                                <div class="card-body p-2">
                                    <i class="fas fa-check-circle text-success fa-lg mb-1"></i>
                                    <h5 class="text-success font-weight-bold mb-0">
                                        {{ $selectedSessionData->completed_sessions }}</h5>
                                    <small class="text-muted">Sesiones Completadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-warning text-center">
                                <div class="card-body p-2">
                                    <i class="fas fa-clock text-warning fa-lg mb-1"></i>
                                    <h5 class="text-warning font-weight-bold mb-0">
                                        {{ $selectedSessionData->avg_duration_minutes ?? 0 }}</h5>
                                    <small class="text-muted">Minutos Promedio</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div
                                class="card border-left-{{ $selectedSessionData->completed_sessions == $selectedSessionData->total_sessions ? 'success' : ($selectedSessionData->activo ? 'primary' : 'secondary') }} text-center">
                                <div class="card-body p-2">
                                    <i
                                        class="fas fa-{{ $selectedSessionData->completed_sessions == $selectedSessionData->total_sessions ? 'trophy' : ($selectedSessionData->activo ? 'play' : 'pause') }} text-{{ $selectedSessionData->completed_sessions == $selectedSessionData->total_sessions ? 'success' : ($selectedSessionData->activo ? 'primary' : 'secondary') }} fa-lg mb-1"></i>
                                    <h6
                                        class="text-{{ $selectedSessionData->completed_sessions == $selectedSessionData->total_sessions ? 'success' : ($selectedSessionData->activo ? 'primary' : 'secondary') }} font-weight-bold mb-0">
                                        {{ $selectedSessionData->completed_sessions == $selectedSessionData->total_sessions ? 'Completado' : ($selectedSessionData->activo ? 'En Progreso' : 'Pausado') }}
                                    </h6>
                                    <small class="text-muted">Estado Actual</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desglose detallado por área de formación -->
                    @if (isset($selectedSessionData->pensum_stats) && count($selectedSessionData->pensum_stats) > 0)
                        <!-- Reducido margin bottom del card -->
                        <div class="card mb-2">
                            <!-- Reducido padding del header -->
                            <div class="card-header bg-light py-2">
                                <h6 class="text-dark font-weight-bold mb-0">
                                    <i class="fas fa-book-open mr-1"></i>Progreso por Área de Formación
                                </h6>
                                <small class="text-muted">Mostrando solo áreas que el estudiante ha intentado
                                    ({{ count($selectedSessionData->pensum_stats) }} de
                                    {{ $selectedSessionData->total_areas_disponibles ?? 'N/A' }} disponibles)</small>
                            </div>
                            <!-- Reducido padding del body -->
                            <div class="card-body p-3">
                                @foreach ($selectedSessionData->pensum_stats as $pensumId => $stats)
                                    <!-- Reducido margin y padding de cada área -->
                                    <div class="mb-2 pb-2 border-bottom">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <h6 class="font-weight-bold text-primary mb-0">
                                                    {{ $stats['pensum']->full_name ?? 'N/A' }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $stats['sessions_count'] }} sesión(es)
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <!-- Reducido height de progress bar -->
                                                <div class="progress mb-1" style="height: 12px;">
                                                    <div class="progress-bar
                                            @if ($stats['progress'] >= 100) bg-success
                                            @elseif($stats['progress'] >= 75) bg-info
                                            @elseif($stats['progress'] >= 50) bg-warning
                                            @else bg-danger @endif"
                                                        style="width: {{ $stats['progress'] }}%">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $stats['answered_questions'] }} /
                                                    {{ $stats['total_questions'] }} preguntas
                                                </small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h6
                                                    class="font-weight-bold mb-0
                                        @if ($stats['progress'] >= 100) text-success
                                        @elseif($stats['progress'] >= 75) text-info
                                        @elseif($stats['progress'] >= 50) text-warning
                                        @else text-danger @endif">
                                                    {{ $stats['progress'] }}%
                                                </h6>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <span
                                                    class="badge badge-{{ $stats['completed_sessions'] == $stats['sessions_count'] ? 'success' : 'warning' }}">
                                                    {{ $stats['completed_sessions'] }}/{{ $stats['sessions_count'] }}
                                                </span>
                                                <br><small class="text-muted">Completadas</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if (isset($selectedSessionData->areas_pendientes) && $selectedSessionData->areas_pendientes > 0)
                                    <!-- Reducido padding del alert -->
                                    <div class="alert alert-info py-2 mb-0">
                                        <small>
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>{{ $selectedSessionData->areas_pendientes }}</strong> área(s) de
                                            formación pendiente(s) por intentar
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Reducido padding y margin del historial -->
                    <div class="card">
                        <div class="card-header bg-light py-2">
                            <h6 class="text-dark font-weight-bold mb-0">
                                <i class="fas fa-history mr-1"></i>Historial de Actividad
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <p class="mb-1 font-weight-bold text-primary small">Primera Sesión:</p>
                                        <p class="text-muted mb-0 small">
                                            {{ $selectedSessionData->iniciado_at ? \Carbon\Carbon::parse($selectedSessionData->iniciado_at)->format('d/m/Y H:i:s') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-1 font-weight-bold text-info small">Última Actividad:</p>
                                        <p class="text-muted mb-0 small">
                                            {{ $selectedSessionData->last_session_date ? \Carbon\Carbon::parse($selectedSessionData->last_session_date)->format('d/m/Y H:i:s') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <p class="mb-1 font-weight-bold text-success small">Tasa de Finalización:</p>
                                        <p class="text-muted mb-0 small">
                                            {{ $selectedSessionData->total_sessions > 0 ? round(($selectedSessionData->completed_sessions / $selectedSessionData->total_sessions) * 100, 1) : 0 }}%
                                            ({{ $selectedSessionData->completed_sessions }} de
                                            {{ $selectedSessionData->total_sessions }})
                                        </p>
                                    </div>
                                    @if (
                                        $selectedSessionData->completed_sessions == $selectedSessionData->total_sessions &&
                                            $selectedSessionData->completado_at)
                                        <div class="mb-2">
                                            <p class="mb-1 font-weight-bold text-success small">Finalización Completa:
                                            </p>
                                            <p class="text-muted mb-0 small">
                                                {{ \Carbon\Carbon::parse($selectedSessionData->completado_at)->format('d/m/Y H:i:s') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif

<!-- Student Sessions Modal -->
@if ($showSessionAnswersModal && $selectedStudentData)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95% !important;">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark py-2">
                    <h5 class="modal-title mb-0 font-weight-bold">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Evaluación Detallada
                    </h5>
                    <button type="button" class="close text-dark" wire:click="closeSessionAnswersModal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body p-2" style="max-height: 75vh; overflow-y: auto;">
                    <div class="text-muted font-weight-bold">{{ $selectedStudentData->estudiant->full_name ?? 'N/A' }}
                    </div>
                    @if (count($selectedSessionAnswers) > 0)
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="card border-left-info shadow-sm">
                                    <div class="card-body p-2 text-center">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Áreas</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ count($selectedSessionAnswers) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-chart-bar fa-lg text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-success shadow-sm">
                                    <div class="card-body p-2 text-center">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Respuestas</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $selectedSessionAnswers->sum('answered_questions') }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-question-circle fa-lg text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-primary shadow-sm">
                                    <div class="card-body p-2 text-center">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Correctas</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $selectedSessionAnswers->sum('correct_answers') }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle fa-lg text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-warning shadow-sm">
                                    <div class="card-body p-2 text-center">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Precisión</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $selectedSessionAnswers->sum('answered_questions') > 0 ? round(($selectedSessionAnswers->sum('correct_answers') / $selectedSessionAnswers->sum('answered_questions')) * 100, 1) : 0 }}%
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-percentage fa-lg text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Áreas de formación con sistema de cards en lugar de accordion -->
                        @foreach ($selectedSessionAnswers as $pensumId => $pensumData)
                            <div class="card mb-3 shadow-sm border-left-primary">
                                <!-- Header del área más compacto -->
                                <div class="card-header bg-light py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <h6 class="mb-0 font-weight-bold text-primary">
                                                <i class="fas fa-book mr-1"></i>
                                                {{ $pensumData['pensum_name'] }}
                                            </h6>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="progress" style="height: 15px;">
                                                <div class="progress-bar @if ($pensumData['progress'] >= 100) bg-success @elseif($pensumData['progress'] >= 75) bg-info @elseif($pensumData['progress'] >= 50) bg-warning @else bg-danger @endif"
                                                    style="width: {{ $pensumData['progress'] }}%">
                                                    <small
                                                        class="font-weight-bold">{{ $pensumData['progress'] }}%</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <span class="badge badge-primary">
                                                {{ $pensumData['answered_questions'] }}/{{ $pensumData['total_questions'] }}
                                            </span>
                                            <br><small class="text-muted">Preguntas</small>
                                        </div>
                                        <!-- Agregar indicador de precisión por área -->
                                        <div class="col-md-2 text-center">
                                            <span
                                                class="badge badge-{{ $pensumData['accuracy'] >= 70 ? 'success' : ($pensumData['accuracy'] >= 50 ? 'warning' : 'danger') }}">
                                                {{ $pensumData['accuracy'] }}%
                                            </span>
                                            <br><small class="text-muted">Precisión</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sistema de cards para preguntas en lugar de accordion -->
                                <div class="card-body p-2">
                                    <div class="row">
                                        @foreach ($pensumData['answers'] as $index => $answer)
                                            <div class="col-md-6 mb-2">
                                                <!-- Mejorar indicador visual de respuesta correcta/incorrecta -->
                                                @php
                                                    $isCorrectAnswer = $answer->isCorrect();
                                                    $cardBorderClass =
                                                        $answer->question->tipo_pregunta == 'multiple'
                                                            ? ($isCorrectAnswer
                                                                ? 'success'
                                                                : 'danger')
                                                            : ($answer->question->tipo_pregunta == 'open'
                                                                ? 'info'
                                                                : 'warning');
                                                @endphp

                                                <div class="card border-left-{{ $cardBorderClass }} shadow-sm h-100">
                                                    <!-- Header de la pregunta -->
                                                    <div class="card-header p-2 bg-white">
                                                        <div
                                                            class="d-flex justify-content-between align-items-start mb-0 pb-0">
                                                            <div class="flex-grow-1">
                                                                <span
                                                                    class="badge badge-{{ $cardBorderClass }} badge-sm mr-0">
                                                                    {{ $index + 1 }}
                                                                </span>

                                                                <small
                                                                    class="text-dark font-weight-bold">{{ $answer->question->pregunta ?? null }}</small>

                                                                <div>
                                                                    [
                                                                    <small class="text-muted">
                                                                        <i
                                                                            class="fas fa-{{ $answer->question->tipo_pregunta == 'multiple' ? 'list' : ($answer->question->tipo_pregunta == 'open' ? 'edit' : 'sliders-h') }} mr-1"></i>
                                                                        {{ ucfirst($answer->question->tipo_pregunta) }}
                                                                    </small>
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($answer->completado_at)->format('d/m H:i') }}
                                                                    </small>
                                                                    ]
                                                                </div>

                                                            </div>
                                                            <div class="text-right ml-2">
                                                                <!-- Agregar indicador de corrección sutil -->
                                                                @if ($answer->question->tipo_pregunta == 'multiple')
                                                                    <div class="mb-1">
                                                                        <i
                                                                            class="fas fa-{{ $isCorrectAnswer ? 'check-circle' : 'times-circle' }} text-{{ $isCorrectAnswer ? 'success' : 'danger' }}"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Contenido de la respuesta -->
                                                    <div class="card-body p-2">
                                                        @if ($answer->question->tipo_pregunta == 'multiple')
                                                            <!-- Mostrar todas las opciones múltiples con nueva lógica de corrección -->
                                                            <div class="mb-2">
                                                                <h6 class="text-dark font-weight-bold mb-2 small">
                                                                    <i class="fas fa-list text-info mr-1"></i>Opciones:
                                                                </h6>

                                                                @foreach ($answer->question->options as $option)
                                                                    <div class="mb-1">
                                                                        @php
                                                                            $isCorrectOption = $option->valor == 1;
                                                                            $isSelectedOption =
                                                                                $answer->option_id == $option->id;
                                                                            $isCorrectSelection =
                                                                                $isSelectedOption && $isCorrectOption;
                                                                            $isIncorrectSelection =
                                                                                $isSelectedOption && !$isCorrectOption;
                                                                        @endphp

                                                                        <div
                                                                            class="px-2 py-1 border rounded-sm small
                                                            @if ($isCorrectSelection) bg-success text-white border-success
                                                            @elseif($isIncorrectSelection)
                                                                bg-danger text-white border-danger
                                                            @elseif($isCorrectOption)
                                                                bg-light text-success font-weight-bold
                                                            @else
                                                                bg-white border-light @endif">

                                                                            <div
                                                                                class="d-flex justify-content-between align-items-center">
                                                                                <div class="flex-grow-1">
                                                                                    @if ($isSelectedOption)
                                                                                        <i
                                                                                            class="fas fa-{{ $isCorrectSelection ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                                                                    @endif
                                                                                    <strong>{{ $option->opcion }}</strong>
                                                                                </div>
                                                                                <div class="ml-2">
                                                                                    @if ($isCorrectOption)
                                                                                        <span
                                                                                            class="badge badge-success badge-sm">
                                                                                            <i class="fas fa-star"></i>
                                                                                            Correcta
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif($answer->question->tipo_pregunta == 'open')
                                                            <!-- Respuesta abierta -->
                                                            <div class="mb-2">
                                                                <h6 class="text-dark font-weight-bold mb-1 small">
                                                                    <i
                                                                        class="fas fa-edit text-success mr-1"></i>Respuesta:
                                                                </h6>
                                                                <div class="p-2 bg-light border rounded">
                                                                    <p class="mb-0 small">
                                                                        {{ $answer->respuesta ?? 'Sin respuesta' }}</p>
                                                                </div>
                                                            </div>
                                                        @elseif($answer->question->tipo_pregunta == 'scale')
                                                            <!-- Escala -->
                                                            <div class="mb-2">
                                                                <h6 class="text-dark font-weight-bold mb-1 small">
                                                                    <i
                                                                        class="fas fa-sliders-h text-warning mr-1"></i>Escala:
                                                                </h6>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="progress flex-grow-1 mr-2"
                                                                        style="height: 18px;">
                                                                        <div class="progress-bar bg-warning"
                                                                            style="width: {{ ($answer->respuesta / 10) * 100 }}%">
                                                                            <small
                                                                                class="font-weight-bold">{{ $answer->respuesta }}/10</small>
                                                                        </div>
                                                                    </div>
                                                                    <span
                                                                        class="badge badge-warning">{{ $answer->respuesta }}</span>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($answer->observaciones)
                                                            <!-- Observaciones -->
                                                            <div class="mt-2 pt-2 border-top">
                                                                <h6 class="text-dark font-weight-bold mb-1 small">
                                                                    <i
                                                                        class="fas fa-comment text-secondary mr-1"></i>Observaciones:
                                                                </h6>
                                                                <p class="text-muted mb-0 small">
                                                                    {{ $answer->observaciones }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Estado vacío -->
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay respuestas registradas</h5>
                            <p class="text-muted mb-0 small">Este estudiante aún no ha completado ninguna evaluación.
                            </p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif
