<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <h6 class="m-0 font-weight-bold text-primary p-2 m-2">Resumen de Preguntas por Área de Formación</h6>
            <div class="card-header py-3 d-flex justify-content-between align-items-center">

                <!-- Added grado filter for questions section -->
                <div class="d-flex align-items-center">


                    <label class="mr-2 mb-0 text-sm font-weight-bold text-muted">Estado Diagnóstico:</label>
                    <select wire:model="questionsStatusFilter" class="form-control form-control-sm" style="width: 180px;">
                        <option value="">Todos los estados</option>
                        <option value="1">Activo para diagnóstico</option>
                        <option value="0">Inactivo para diagnóstico</option>
                    </select>
                </div>

                <!-- Added bulk deactivation button -->
                <div class="d-flex align-items-center">
                    <button wire:click="confirmBulkDeactivatePensums" class="btn btn-warning btn-sm"
                        title="Desactivar todas las áreas de formación para diagnóstico">
                        {{-- Removed loading states from bulk deactivation button --}}
                        <i class="fas fa-pause-circle mr-1"></i>
                        Desactivar Todas
                    </button>
                    @if ($totalActiveDiagnosticPensums > 0)
                        <span class="badge badge-light ml-2 shadow-sm" title="Áreas activas actualmente"
                            style="font-size: 0.85rem; vertical-align: middle;">
                            <i class="fas fa-check-circle text-success mr-1"></i>
                            {{ $totalActiveDiagnosticPensums }} Activas
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <!-- Made table more compact with table-sm and smaller text -->
                    <table class="table table-bordered table-sm small table-striped" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-xs">Área de Formación</th>
                                <th class="text-xs text-center">Total</th>
                                <th class="text-xs text-center">Activas</th>
                                <th class="text-xs text-center">Tipos</th>
                                <th class="text-xs text-center">Respuestas</th>
                                <!-- Added precision column -->
                                <th class="text-xs text-center">Precisión</th>
                                <th class="text-xs text-center">Estado</th>
                                <!-- Added action column for questions modal -->
                                <th class="text-xs text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <!-- Fixed foreach error by ensuring $questionsPensums is always a collection -->
                            @if ($questionsPensums && method_exists($questionsPensums, 'count'))
                                @forelse($questionsPensums as $pensum)
                                    <tr>
                                        <td class="text-xs">
                                            <strong>{{ $pensum->full_name }}</strong>
                                        </td>
                                        <td class="text-center text-xs">
                                            <span class="badge badge-primary badge-pill"
                                                style="font-size: 0.8rem">{{ $pensum->total_questions ?? 0 }}</span>
                                        </td>
                                        <td class="text-center text-xs">
                                            <span class="badge badge-success badge-pill"
                                                style="font-size: 0.8rem">{{ $pensum->active_questions ?? 0 }}</span>
                                        </td>
                                        <td class="text-xs">
                                            @if ($pensum->question_types)
                                                @foreach (explode(',', $pensum->question_types) as $type)
                                                    <span class="badge badge-info badge-sm mr-1"
                                                        style="font-size: 0.8rem">{{ trim($type) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-xs">
                                            <span class="badge badge-warning badge-pill"
                                                style="font-size: 0.8rem">{{ $pensum->total_responses ?? 0 }}</span>
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
                                        <td class="text-center text-xs">
                                            @if ($pensum->active_questions > 0)
                                                <span class="badge badge-success"
                                                    style="font-size: 0.8rem">P:{{ $pensum->active_questions }}</span>
                                            @else
                                                <span class="badge badge-secondary" style="font-size: 0.8rem">Sin
                                                    Preguntas</span>
                                            @endif

                                            @if ($pensum->status_active_diagnostic > 0)
                                                <span class="badge badge-success"
                                                    style="font-size: 0.8rem">Activa</span>
                                            @else
                                                <span class="badge badge-secondary" style="font-size: 0.8rem">
                                                    Desactivada
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Added action button to view questions -->
                                        <td class="text-left text-xs">
                                            <div class="btn-group" role="group">
                                                @if ($pensum->active_questions > 0 || $pensum->total_questions > 0)
                                                    <button wire:click="showPensumQuestions({{ $pensum->id }})"
                                                        class="btn btn-sm btn-outline-primary btn-sm"
                                                        title="Ver preguntas">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif

                                                @if ($pensum->status_active_diagnostic)
                                                    <button wire:click="confirmDeactivatePensum({{ $pensum->id }})"
                                                        class="btn btn-sm btn-outline-warning btn-sm"
                                                        title="Desactivar diagnóstico">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                @else
                                                    <button wire:click="confirmActivatePensum({{ $pensum->id }})"
                                                        class="btn btn-sm btn-outline-success btn-sm"
                                                        title="{{ $pensum->active_questions > 0 ? 'Activar para diagnóstico' : 'No se puede activar: sin preguntas activas' }}"
                                                        {{ $pensum->active_questions == 0 ? 'disabled' : '' }}>
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted text-xs py-3">
                                            No se encontraron áreas de formación
                                            @if ($filterGradoId || $questionsStatusFilter)
                                                para los filtros seleccionados
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="8" class="text-center text-muted text-xs py-3">
                                        Error: No se pudieron cargar las áreas de formación
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Fixed pagination to check if $questionsPensums exists and has pagination methods -->
                @if ($questionsPensums && method_exists($questionsPensums, 'hasPages') && $questionsPensums->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-sm text-muted">
                            Mostrando {{ $questionsPensums->firstItem() }} a {{ $questionsPensums->lastItem() }}
                            de {{ $questionsPensums->total() }} resultados
                        </div>
                        <div>
                            <nav aria-label="Paginación de preguntas">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($questionsPensums->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-chevron-left"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <button wire:click="previousPage('questionsPage')" class="page-link">
                                                {{-- Removed loading states from pagination buttons --}}
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $start = max(1, $questionsPensums->currentPage() - 2);
                                        $end = min($questionsPensums->lastPage(), $questionsPensums->currentPage() + 2);
                                    @endphp

                                    {{-- First Page --}}
                                    @if ($start > 1)
                                        <li class="page-item">
                                            <button wire:click="gotoPage(1, 'questionsPage')" class="page-link">
                                                1
                                            </button>
                                        </li>
                                        @if ($start > 2)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    {{-- Page Numbers --}}
                                    @for ($page = $start; $page <= $end; $page++)
                                        @if ($page == $questionsPensums->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <button wire:click="gotoPage({{ $page }}, 'questionsPage')"
                                                    class="page-link">
                                                    {{ $page }}
                                                </button>
                                            </li>
                                        @endif
                                    @endfor

                                    {{-- Last Page --}}
                                    @if ($end < $questionsPensums->lastPage())
                                        @if ($end < $questionsPensums->lastPage() - 1)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="page-item">
                                            <button
                                                wire:click="gotoPage({{ $questionsPensums->lastPage() }}, 'questionsPage')"
                                                class="page-link">
                                                {{ $questionsPensums->lastPage() }}
                                            </button>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($questionsPensums->hasMorePages())
                                        <li class="page-item">
                                            <button wire:click="nextPage('questionsPage')" class="page-link">
                                                <i class="fas fa-chevron-right"></i>
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
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Added success notification modal -->
@if (session()->has('success') || session()->has('error'))
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if (session()->has('success'))
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Operación Exitosa
                        @else
                            <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                            Error
                        @endif
                    </h5>
                    <button type="button" class="close" wire:click="clearNotifications">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="clearNotifications">
                        <i class="fas fa-check mr-1"></i>
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Added questions modal -->
@if ($showQuestionsModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-question-circle text-primary mr-2"></i>
                        Preguntas - {{ $selectedPensumForQuestions->full_name ?? 'Área de Formación' }}
                    </h5>
                    <button type="button" class="close" wire:click="closeQuestionsModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    @if (count($selectedPensumQuestions) > 0)
                        <div class="row mb-2">
                            <div class="col-12">
                                <div class="alert alert-info alert-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>{{ count($selectedPensumQuestions) }}</strong> preguntas encontradas en
                                    esta área de formación
                                </div>
                            </div>
                        </div>

                        @foreach ($selectedPensumQuestions as $index => $question)
                            <div class="card mb-2 shadow-sm">
                                <div class="card-header py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">
                                            <span class="font-weight-bold">Pregunta {{ $loop->iteration }}</span>
                                        </h6>
                                        <div>
                                            <span
                                                class="badge badge-{{ $question->tipo_pregunta == 'multiple' ? 'success' : ($question->tipo_pregunta == 'open' ? 'warning' : 'info') }}">
                                                {{ ucfirst($question->tipo_pregunta) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body py-2">
                                    <p class="mb-3"><strong>{{ $question->pregunta }}</strong></p>

                                    @if ($question->tipo_pregunta == 'multiple' && count($question->options) > 0)
                                        <div class="ml-3">
                                            <h6 class="text-muted mb-2">Opciones de respuesta:</h6>
                                            <div class="row">
                                                @php
                                                    $maxValue = $question->options->max('valor');
                                                    $correctOption = $question->options
                                                        ->where('valor', $maxValue)
                                                        ->first();
                                                @endphp
                                                @foreach ($question->options as $option)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" disabled>
                                                            <label
                                                                class="form-check-label text-sm {{ $option->valor == 1 ? 'text-success font-weight-bold' : '' }}">
                                                                {{ $option->opcion }}
                                                                @if ($option->valor == 1)
                                                                    <i class="fas fa-check-circle text-success ml-1"
                                                                        title="Respuesta correcta"></i>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif($question->tipo_pregunta == 'scale')
                                        <div class="ml-3">
                                            <h6 class="text-muted mb-2">Escala de respuesta:</h6>
                                            @if (count($question->options) > 0)
                                                <div
                                                    class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                                    @foreach ($question->options->sortBy('orden') as $option)
                                                        <div class="text-center">
                                                            <div class="btn btn-outline-secondary btn-sm mb-1"
                                                                disabled>
                                                                {{ $option->valor ?? $option->orden }}
                                                            </div>
                                                            <div class="text-xs text-muted">{{ $option->opcion }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-light">
                                                    <i class="fas fa-slider-h mr-2"></i>
                                                    Pregunta de escala (opciones no configuradas)
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($question->tipo_pregunta == 'open')
                                        <div class="ml-3">
                                            <h6 class="text-muted mb-2">Respuesta abierta:</h6>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" placeholder="El estudiante escribirá su respuesta aquí..." disabled></textarea>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-2">
                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay preguntas disponibles</h5>
                            <p class="text-muted">Esta área de formación no tiene preguntas activas configuradas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Updated confirmation modal with validation message -->
@if ($showActivationModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                        Confirmar {{ $activationAction == 'activate' ? 'Activación' : 'Desactivación' }}
                    </h5>
                    <button type="button" class="close" wire:click="closeActivationModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        ¿Está seguro que desea
                        <strong>{{ $activationAction == 'activate' ? 'activar' : 'desactivar' }}</strong>
                        el área de formación para diagnóstico?
                    </p>

                    @if ($selectedPensumForActivation)
                        <div class="alert alert-info">
                            <strong>Área de Formación:</strong> {{ $selectedPensumForActivation->full_name }}
                        </div>

                        @if ($activationAction == 'activate' && !$this->pensumHasQuestions($selectedPensumForActivation->id))
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Advertencia:</strong> Esta área de formación no tiene preguntas activas
                                asociadas.
                                No se podrá realizar diagnósticos hasta que se agreguen preguntas.
                            </div>
                        @endif
                    @endif

                    @if ($activationAction == 'activate')
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle mr-2"></i>
                            Al activar esta área, estará disponible para que los estudiantes realicen el diagnóstico.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle mr-2"></i>
                            Al desactivar esta área, los estudiantes no podrán realizar nuevos diagnósticos.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeActivationModal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>
                    <button type="button"
                        class="btn btn-{{ $activationAction == 'activate' ? 'success' : 'warning' }}"
                        wire:click="processPensumActivation">
                        <i class="fas fa-{{ $activationAction == 'activate' ? 'play' : 'pause' }} mr-1"></i>
                        {{ $activationAction == 'activate' ? 'Activar' : 'Desactivar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Added bulk deactivation confirmation modal -->
@if ($showBulkDeactivationModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                        Confirmar Desactivación Masiva
                    </h5>
                    <button type="button" class="close" wire:click="closeBulkDeactivationModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>¡Atención!</strong> Esta acción desactivará <strong>TODAS</strong> las áreas de
                        formación activas para diagnóstico.
                    </div>

                    <p class="mb-3">
                        ¿Está seguro que desea <strong>desactivar todas las áreas de formación</strong> para
                        diagnóstico?
                    </p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Al desactivar todas las áreas, los estudiantes no podrán realizar nuevos diagnósticos hasta que
                        se reactiven individualmente.
                    </div>

                    <div class="alert alert-secondary">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <strong>Nota:</strong> Esta acción solo afecta el estado de diagnóstico
                        (status_active_diagnostic = false). Las áreas seguirán activas para otras funciones.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeBulkDeactivationModal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" wire:click="processBulkDeactivation">
                        {{-- Removed loading states from bulk deactivation modal button --}}
                        <i class="fas fa-pause-circle mr-1"></i>
                        Desactivar Todas
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
