<div>
    <!-- Header Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div>
                        <h4 class="mb-1 font-weight-bold">Planes de Evaluación</h4>
                        <p class="text-muted mb-0">Gestión de evaluaciones pedagógicas por momentos</p>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" wire:click="openModal('create')">
                    <i class="fas fa-plus mr-2"></i>Nuevo Plan
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label small font-weight-bold">Buscar</label>
                        <input type="text" class="form-control" wire:model.debounce.300ms="search"
                            placeholder="Buscar en observaciones...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label class="form-label small font-weight-bold">Filtrar por Grado</label>
                        {!! Form::select('filterGrado', $list_grado, old('filterGrado'), [
                            'wire:model' => 'filterGrado',
                            'class' => 'form-control',
                            'id' => 'filterGrado',
                            'placeholder' => 'Selecciones',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label class="form-label small font-weight-bold">Filtrar por Sección</label>
                        {!! Form::select('filterSeccion', $list_seccion, old('filterSeccion'), [
                            'wire:model' => 'filterSeccion',
                            'class' => 'form-control',
                            'id' => 'filterSeccion',
                            'placeholder' => 'Selecciones',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label class="form-label small font-weight-bold">Filtrar por Momento</label>
                        {!! Form::select('filterLapso', $list_lapso, old('filterLapso'), [
                            'wire:model' => 'filterLapso',
                            'class' => 'form-control',
                            'id' => 'filterLapso',
                            'placeholder' => 'Selecciones',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-block"
                        wire:click="$set('search', ''); $set('filterGrado', ''); $set('filterSeccion', ''); $set('filterLapso', '')">
                        <i class="fas fa-times mr-1"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluations List -->
    @if ($eievaluationks->count() > 0)
        <div class="row">
            @foreach ($eievaluationks as $index => $evaluation)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm h-100 {{ $loop->even ? 'bg-light' : '' }}">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary badge-pill mr-3 p-2">{{ $index + 1 }}</span>
                                    <div>
                                        <h5 class="mb-1 font-weight-bold">
                                            {{ $evaluation->grado->name }} - Sección {{ $evaluation->seccion->name }}
                                        </h5>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($evaluation->finicial)->format('d/m/Y') }} -
                                            {{ \Carbon\Carbon::parse($evaluation->ffinal)->format('d/m/Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $evaluation->lapso->name }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $evaluation->profesor->fullname }}
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('view', {{ $evaluation->id }})">
                                            <i class="fas fa-eye mr-2 text-info"></i>Ver Detalles
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('edit', {{ $evaluation->id }})">
                                            <i class="fas fa-edit mr-2"></i>Editar Plan
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('position', {{ $evaluation->id }})">
                                            <i class="fas fa-list mr-2"></i>Gestionar Posiciones
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" target="_blank"
                                            href="{{ route('inicials.eievaluationks.format.index', $evaluation->id) }}">
                                            <i class="fas fa-file-pdf mr-2"></i>Generar PDF
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="confirm('¿Está seguro de eliminar este plan?') || event.stopImmediatePropagation()"
                                            wire:click.prevent="delete({{ $evaluation->id }})">
                                            <i class="fas fa-trash mr-2"></i>Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Observaciones -->
                            <div class="mb-3">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-clipboard-list mr-1"></i>Observaciones del Docente:
                                </h6>
                                <p class="text-muted mb-0">{{ Str::limit($evaluation->observaciones, 300) }}</p>
                                @if (strlen($evaluation->observaciones) > 300)
                                    <button type="button" class="btn btn-link btn-sm p-0 mt-1"
                                        wire:click="openModal('view', {{ $evaluation->id }})">
                                        Ver más...
                                    </button>
                                @endif
                            </div>

                            @if ($evaluation->recomendacion)
                                <div class="mb-3">
                                    <h6 class="font-weight-bold text-success mb-2">
                                        <i class="fas fa-lightbulb mr-1"></i>Recomendación
                                        <small class="text-muted">[Coord. Evaluación]</small>:
                                    </h6>
                                    <p class="text-muted mb-0">{{ Str::limit($evaluation->recomendacion, 100) }}</p>
                                    @if (strlen($evaluation->recomendacion) > 100)
                                        <button type="button" class="btn btn-link btn-sm p-0 mt-1"
                                            wire:click="openModal('view', {{ $evaluation->id }})">
                                            Ver más...
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <!-- Asistencia -->
                            <div class="mb-3">
                                <h6 class="font-weight-bold text-warning mb-2">
                                    <i class="fas fa-users mr-1"></i>Control de Asistencia:
                                </h6>
                                <p class="text-muted mb-0">{{ $evaluation->asistencia }}</p>
                            </div>

                            <!-- Stats -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-info mr-2">
                                            <i class="fas fa-list mr-1"></i>
                                            {{ $evaluation->getOrderedEvaluationps()->count() }} Posiciones
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Creado: {{ $evaluation->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-info"
                                        wire:click="openModal('view', {{ $evaluation->id }})"
                                        title="Ver detalles completos">
                                        <i class="fas fa-eye mr-1"></i>Detalles
                                    </button>
                                    <button type="button" class="btn btn-outline-primary"
                                        wire:click="openModal('edit', {{ $evaluation->id }})" title="Editar plan">
                                        <i class="fas fa-edit mr-1"></i>Editar
                                    </button>
                                    <button type="button" class="btn btn-outline-success"
                                        wire:click="openModal('position', {{ $evaluation->id }})"
                                        title="Gestionar posiciones">
                                        <i class="fas fa-list mr-1"></i>Posiciones
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $eievaluationks->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No hay planes de evaluación registrados</h5>
                <p class="text-muted mb-4">Comienza creando tu primer plan de evaluación para organizar las actividades
                    pedagógicas.</p>
                <button type="button" class="btn btn-primary" wire:click="openModal('create')">
                    <i class="fas fa-plus mr-2"></i>Crear Primer Plan
                </button>
            </div>
        </div>
    @endif

    <!-- Modals -->
    @include('livewire.inicial.modal.eievaluationk.main-modal')

    <!-- Loading Indicator -->
    <div wire:loading class="position-fixed"
        style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>
</div>
