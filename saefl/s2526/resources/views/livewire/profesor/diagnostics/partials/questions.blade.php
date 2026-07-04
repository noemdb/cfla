<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-question-circle mr-2"></i>
                            Gestión de Preguntas
                        </h6>
                    </div>
                    <div class="col-auto">
                        <button wire:click="openQuestionModal" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Nueva Pregunta
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="form-label text-muted small mb-1">Buscar preguntas</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input wire:model.debounce.300ms="search" type="text"
                                    class="form-control border-left-0" placeholder="Escriba para buscar...">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="form-label text-muted small mb-1">Tipo</label>
                            <select wire:model="filterType" class="form-control">
                                <option value="">Todos los tipos</option>
                                <option value="multiple">Opción Múltiple</option>
                                <option value="open">Respuesta Abierta</option>
                                <option value="scale">Escala</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="form-label text-muted small mb-1">Área de Formación</label>
                            <select wire:model="filterSubject" class="form-control">
                                <option value="">Todas las áreas</option>
                                @foreach ($subjects as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        {{-- <div class="form-group mb-0">
                            <label class="form-label text-muted small mb-1">Ordenar por</label>
                            <select wire:model="sortBy" class="form-control">
                                <option value="created_at">Fecha creación</option>
                                <option value="pregunta">Pregunta</option>
                                <option value="difficulty">Dificultad</option>
                                <option value="orden">Orden</option>
                            </select>
                        </div> --}}
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group mb-0 w-100">
                            <div class="btn-group w-100" role="group">
                                {{-- <button wire:click="$toggle('sortDirection')" class="btn btn-outline-secondary btn-sm"
                                    title="Cambiar orden">
                                    <i class="fas fa-sort-amount-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                </button> --}}
                                <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm"
                                    title="Limpiar filtros">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Summary -->
                @if ($search || $filterType || $filterSubject)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-light border-0 py-2">
                                <small class="text-muted">
                                    <i class="fas fa-filter mr-1"></i>
                                    Mostrando {{ $questions->total() }} resultado(s)
                                    @if ($search)
                                        para "<strong>{{ $search }}</strong>"
                                    @endif
                                    @if ($filterType)
                                        • Tipo: <strong>{{ ucfirst($filterType) }}</strong>
                                    @endif
                                    @if ($filterSubject)
                                        • Área: <strong>{{ $subjects[$filterSubject] ?? 'Seleccionada' }}</strong>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Questions Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40%;">Pregunta</th>
                                <th style="width: 15%;" class="text-center">Tipo</th>
                                <th style="width: 20%;">Área de Formación</th>
                                <th style="width: 10%;" class="text-center">Dificultad</th>
                                <th style="width: 10%;" class="text-center">Fecha</th>
                                <th style="width: 5%;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                                <tr class="question-row hover-shadow">
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div
                                                class="question-icon mr-2 mt-1 icon-circle border-left-{{ $question->tipo_pregunta === 'multiple' ? 'primary' : ($question->tipo_pregunta === 'open' ? 'success' : ($question->tipo_pregunta === 'scale' ? 'info' : 'warning')) }}">
                                                @if ($question->tipo_pregunta === 'multiple')
                                                    <i class="fas fa-list-ul text-primary"></i>
                                                @elseif($question->tipo_pregunta === 'open')
                                                    <i class="fas fa-edit text-success"></i>
                                                @else
                                                    <i class="fas fa-sliders-h text-info"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="question-text font-weight-medium">
                                                    {{ Str::limit($question->pregunta, 120) }}
                                                </div>
                                                @if ($question->options->count() > 0)
                                                    <small class="text-muted">
                                                        <i class="fas fa-list mr-1"></i>
                                                        {{ $question->options->count() }} opciones
                                                    </small>
                                                @endif
                                                @if ($question->weighing)
                                                    <small class="text-muted ml-2">
                                                        <i class="fas fa-weight mr-1"></i>
                                                        Peso: {{ $question->weighing }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($question->tipo_pregunta === 'multiple')
                                            <span class="badge badge-primary badge-pill badge-sm">Opción Múltiple</span>
                                        @elseif($question->tipo_pregunta === 'open')
                                            <span class="badge badge-success badge-pill badge-sm">Respuesta
                                                Abierta</span>
                                        @elseif($question->tipo_pregunta === 'scale')
                                            <span class="badge badge-info badge-pill badge-sm">Escala</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-book text-muted mr-2"></i>
                                            <div>
                                                <div class="font-weight-medium text-sm">
                                                    {{ $question->pensum->full_name ?? 'Sin asignatura' }}
                                                </div>
                                                @if ($question->pensum->asignatura->code ?? null)
                                                    <small
                                                        class="text-muted">{{ $question->pensum->asignatura->code }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($question->difficulty === 'easy')
                                            <span class="badge badge-success badge-sm">
                                                <i class="fas fa-smile mr-1"></i>Fácil
                                            </span>
                                        @elseif($question->difficulty === 'medium')
                                            <span class="badge badge-warning badge-sm">
                                                <i class="fas fa-meh mr-1"></i>Medio
                                            </span>
                                        @else
                                            <span class="badge badge-danger badge-sm">
                                                <i class="fas fa-frown mr-1"></i>Difícil
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="text-sm font-weight-medium">
                                            {{ $question->created_at->format('d/m/Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $question->created_at->format('H:i') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button wire:click="openQuestionModal({{ $question->id }})"
                                                class="btn btn-sm btn-outline-primary" title="Editar pregunta">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="deleteQuestion({{ $question->id }})"
                                                class="btn btn-sm btn-outline-danger" title="Eliminar pregunta">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-question-circle fa-3x text-gray-300 mb-3"></i>
                                            <h5 class="text-muted mb-2">No se encontraron preguntas</h5>
                                            @if ($search || $filterType || $filterSubject)
                                                <p class="text-muted mb-3">
                                                    Intenta ajustar los filtros o crear una nueva pregunta
                                                </p>
                                                <button wire:click="resetFilters"
                                                    class="btn btn-outline-secondary btn-sm mr-2">
                                                    <i class="fas fa-sync-alt mr-1"></i>Limpiar filtros
                                                </button>
                                            @else
                                                <p class="text-muted mb-3">
                                                    Comienza creando tu primera pregunta diagnóstica
                                                </p>
                                            @endif
                                            <button wire:click="openQuestionModal" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus mr-1"></i>Crear pregunta
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($questions && is_object($questions) && method_exists($questions, 'hasPages'))
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-sm text-gray-600">
                            Mostrando {{ $questions->firstItem() ?? 0 }} a {{ $questions->lastItem() ?? 0 }}
                            de {{ $questions->total() }} preguntas
                        </div>
                        <div>
                            @if ($questions->hasPages())
                                <nav aria-label="Pagination Navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        @if ($questions->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fas fa-chevron-left"></i>
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="previousPage('page')" wire:loading.attr="disabled"
                                                    wire:target="previousPage">
                                                    <span wire:loading.remove wire:target="previousPage">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                    <span wire:loading wire:target="previousPage">
                                                        <i class="fas fa-spinner fa-spin"></i>
                                                    </span>
                                                </button>
                                            </li>
                                        @endif

                                        @php
                                            $start = max($questions->currentPage() - 2, 1);
                                            $end = min($start + 4, $questions->lastPage());
                                            $start = max($end - 4, 1);
                                        @endphp

                                        @if ($start > 1)
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="gotoPage(1, 'page')" wire:loading.attr="disabled"
                                                    wire:target="gotoPage">
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
                                            @if ($page == $questions->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <button type="button" class="page-link"
                                                        wire:click="gotoPage({{ $page }}, 'page')"
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

                                        @if ($end < $questions->lastPage())
                                            @if ($end < $questions->lastPage() - 1)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="gotoPage({{ $questions->lastPage() }}, 'page')"
                                                    wire:loading.attr="disabled" wire:target="gotoPage">
                                                    {{ $questions->lastPage() }}
                                                </button>
                                            </li>
                                        @endif

                                        @if ($questions->hasMorePages())
                                            <li class="page-item">
                                                <button type="button" class="page-link"
                                                    wire:click="nextPage('page')" wire:loading.attr="disabled"
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

@section('stylesheet')
    @parent
    <style>
        .question-row:hover {
            background-color: #f8f9fc;
            transition: background-color 0.2s ease;
        }

        .question-text {
            line-height: 1.4;
            color: #2d3748;
        }

        .question-icon {
            width: 20px;
            text-align: center;
        }

        .badge-pill {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .empty-state {
            padding: 2rem 1rem;
        }

        .font-weight-medium {
            font-weight: 500;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .align-middle td {
            vertical-align: middle;
        }

        .btn-group .btn {
            border-radius: 0.25rem;
        }

        .btn-group .btn:not(:last-child) {
            margin-right: 0.25rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #e3e6f0;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .alert-light {
            background-color: #f8f9fc;
            border-color: #e3e6f0;
        }

        /* Agregando estilos adicionales para consistencia */
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

        /* Asegurando que no haya fondos negros no deseados */
        .card,
        .card-body,
        .card-header {
            background-color: #ffffff;
        }

        .table {
            background-color: #ffffff;
        }

        .table tbody tr {
            background-color: #ffffff;
        }
    </style>
@endsection
