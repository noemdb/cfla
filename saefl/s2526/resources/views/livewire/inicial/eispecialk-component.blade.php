<div>
    <!-- Header Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div>
                        <h4 class="mb-1 font-weight-bold">Planes Especiales</h4>
                        <p class="text-muted mb-0">Gestión de planificación pedagógica especializada</p>
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
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label class="form-label small font-weight-bold">Buscar</label>
                        <input type="text" class="form-control" wire:model.debounce.300ms="search"
                            placeholder="Buscar en justificación u observaciones...">
                    </div>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-block"
                        wire:click="$set('search', ''); $set('filterGrado', ''); $set('filterSeccion', '')">
                        <i class="fas fa-times mr-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans List -->
    @if ($eispecialks->count() > 0)
        <div class="row border-top pt-4">
            @foreach ($eispecialks as $index => $plan)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm h-100 {{ $loop->even ? 'bg-light' : '' }}">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary badge-pill mr-3 p-2">{{ $index + 1 }}</span>
                                    <div>
                                        <h5 class="mb-1 font-weight-bold">
                                            {{ $plan->grado->name }} - Sección {{ $plan->seccion->name }}
                                        </h5>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($plan->finicial)->format('d/m/Y') }} -
                                            {{ \Carbon\Carbon::parse($plan->ffinal)->format('d/m/Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $plan->tiempo_ejecucion }}
                                            semana{{ $plan->tiempo_ejecucion != 1 ? 's' : '' }}
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
                                            wire:click.prevent="openModal('view', {{ $plan->id }})">
                                            <i class="fas fa-eye mr-2 text-info"></i>Ver Detalles
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('edit', {{ $plan->id }})">
                                            <i class="fas fa-edit mr-2"></i>Editar Plan
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('activity', {{ $plan->id }})">
                                            <i class="fas fa-tasks mr-2"></i>Gestionar Actividades
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item"
                                            href="{{ route('inicials.eispecialks.format.index', $plan->id) }}"
                                            target="_blank">
                                            <i class="fas fa-file-pdf mr-2"></i>Generar PDF
                                        </a>

                                        <!-- En el dropdown menu agregar: -->
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('strategy', {{ $plan->id }})">
                                            <i class="fas fa-users mr-2"></i>Gestionar Estrategias
                                        </a>

                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="confirm('¿Está seguro de eliminar este plan?') || event.stopImmediatePropagation()"
                                            wire:click.prevent="delete({{ $plan->id }})">
                                            <i class="fas fa-trash mr-2"></i>Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Justificación -->
                            <div class="mb-3">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-clipboard-list mr-1"></i>Justificación:
                                </h6>
                                <p class="text-muted mb-0">{{ Str::limit($plan->justificacion, 300) }}</p>
                                @if (strlen($plan->justificacion) > 300)
                                    <button type="button" class="btn btn-link btn-sm p-0 mt-1"
                                        wire:click="openModal('view', {{ $plan->id }})">
                                        Ver más...
                                    </button>
                                @endif
                            </div>

                            @if ($plan->observacion)
                                <div class="mb-3">
                                    <h6 class="font-weight-bold text-info mb-2">
                                        <i class="fas fa-comment mr-1"></i>Observación
                                        <small class="text-muted">[Coord. Evaluación]</small>:
                                    </h6>
                                    <p class="text-muted mb-0">{{ Str::limit($plan->observacion, 100) }}</p>
                                    @if (strlen($plan->observacion) > 100)
                                        <button type="button" class="btn btn-link btn-sm p-0 mt-1"
                                            wire:click="openModal('view', {{ $plan->id }})">
                                            Ver más...
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <!-- Stats -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-success mr-2">
                                            <i class="fas fa-tasks mr-1"></i>
                                            {{ $plan->getOrderedActivities()->count() }} Actividades
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $plan->profesor->fullname }}
                                    </small>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-info"
                                        wire:click="openModal('view', {{ $plan->id }})"
                                        title="Ver detalles completos">
                                        <i class="fas fa-eye mr-1"></i>Detalles
                                    </button>
                                    <button type="button" class="btn btn-outline-primary"
                                        wire:click="openModal('edit', {{ $plan->id }})" title="Editar plan">
                                        <i class="fas fa-edit mr-1"></i>Editar
                                    </button>
                                    <button type="button" class="btn btn-outline-success"
                                        wire:click="openModal('activity', {{ $plan->id }})"
                                        title="Gestionar actividades">
                                        <i class="fas fa-tasks mr-1"></i>Actividades
                                    </button>

                                    <!-- En los botones de acción rápida agregar: -->
                                    <button type="button" class="btn btn-outline-warning"
                                        wire:click="openModal('strategy', {{ $plan->id }})"
                                        title="Gestionar estrategias">
                                        <i class="fas fa-users mr-1"></i>Estrategias
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
            {{ $eispecialks->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No hay planes especiales registrados</h5>
                <p class="text-muted mb-4">Comienza creando tu primer plan especial para organizar actividades
                    pedagógicas diferenciadas.</p>
                <button type="button" class="btn btn-primary" wire:click="openModal('create')">
                    <i class="fas fa-plus mr-2"></i>Crear Primer Plan
                </button>
            </div>
        </div>
    @endif

    <!-- Modals -->
    @include('livewire.inicial.modal.eispecialk.main-modal')

    <!-- Loading Indicator -->
    <div wire:loading class="position-fixed"
        style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>
</div>
