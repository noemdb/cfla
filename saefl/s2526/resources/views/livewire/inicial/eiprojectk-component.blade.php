<div>
    <!-- Header Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div>
                        <h4 class="mb-1 font-weight-bold">Proyectos de Aula</h4>
                        <p class="text-muted mb-0">Gestión de proyectos educativos</p>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" wire:click="openModal('create')">
                    <i class="fas fa-plus mr-2"></i>Nuevo Proyecto
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
                            placeholder="Buscar en diagnóstico...">
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

    <!-- Projects List -->
    @if ($eiprojectks->count() > 0)
        <div class="row">
            @foreach ($eiprojectks as $index => $project)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm h-100 {{ $loop->even ? 'bg-light' : '' }}">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary badge-pill mr-3 p-2">{{ $index + 1 }}</span>
                                    <div>
                                        <h5 class="mb-1 font-weight-bold">
                                            {{ $project->grado->name }} - Sección {{ $project->seccion->name }}
                                        </h5>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($project->finicial)->format('d/m/Y') }} -
                                            {{ \Carbon\Carbon::parse($project->ffinal)->format('d/m/Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $project->tiempo_ejecucion }}
                                            semana{{ $project->tiempo_ejecucion != 1 ? 's' : '' }}
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
                                            wire:click.prevent="openModal('view', {{ $project->id }})">
                                            <i class="fas fa-eye mr-2 text-info"></i>Ver Detalles
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('edit', {{ $project->id }})">
                                            <i class="fas fa-edit mr-2"></i>Editar Proyecto
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('review', {{ $project->id }})">
                                            <i class="fas fa-search mr-2"></i>Gestionar Revisión
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="openModal('summary', {{ $project->id }})">
                                            <i class="fas fa-list mr-2"></i>Gestionar Resumen
                                        </a>
                                        <a class="dropdown-item" href="#"
                                           wire:click.prevent="openModal('strategy', {{ $project->id }})">
                                           <i class="fas fa-users mr-2"></i>Gestionar Estrategias
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="confirm('¿Está seguro de eliminar este proyecto?') || event.stopImmediatePropagation()"
                                            wire:click.prevent="delete({{ $project->id }})">
                                            <i class="fas fa-trash mr-2"></i>Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Diagnóstico -->
                            <div class="mb-3">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-stethoscope mr-1"></i>Diagnóstico:
                                </h6>
                                <p class="text-muted mb-0">{{ Str::limit($project->diagnostico, 300) }}</p>
                                @if (strlen($project->diagnostico) > 300)
                                    <button type="button" class="btn btn-link btn-sm p-0 mt-1"
                                        wire:click="openModal('view', {{ $project->id }})">
                                        Ver más...
                                    </button>
                                @endif
                            </div>

                            <!-- Stats -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-info mr-2">
                                            <i class="fas fa-search mr-1"></i>
                                            {{ $project->getOrderedViews()->count() }} Revisiones
                                        </span>
                                        <span class="badge badge-success">
                                            <i class="fas fa-list mr-1"></i>
                                            {{ $project->getOrderedSummaries()->count() }} Resúmenes
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-info"
                                        wire:click="openModal('view', {{ $project->id }})"
                                        title="Ver detalles completos">
                                        <i class="fas fa-eye mr-1"></i>Detalles
                                    </button>
                                    <button type="button" class="btn btn-outline-primary"
                                        wire:click="openModal('edit', {{ $project->id }})" title="Editar proyecto">
                                        <i class="fas fa-edit mr-1"></i>Editar
                                    </button>
                                    <button type="button" class="btn btn-outline-warning"
                                        wire:click="openModal('review', {{ $project->id }})"
                                        title="Gestionar revisiones">
                                        <i class="fas fa-search mr-1"></i>Revisión
                                    </button>
                                    <button type="button" class="btn btn-outline-success"
                                        wire:click="openModal('summary', {{ $project->id }})"
                                        title="Gestionar resúmenes">
                                        <i class="fas fa-list mr-1"></i>Resumen
                                    </button>
                                    <button type="button" class="btn btn-outline-warning"
                                        wire:click="openModal('strategy', {{ $project->id }})"
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
            {{ $eiprojectks->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No hay proyectos registrados</h5>
                <p class="text-muted mb-4">Comienza creando tu primer proyecto de aula para organizar las actividades
                    educativas.</p>
                <button type="button" class="btn btn-primary" wire:click="openModal('create')">
                    <i class="fas fa-plus mr-2"></i>Crear Primer Proyecto
                </button>
            </div>
        </div>
    @endif

    <!-- Modals -->
    @include('livewire.inicial.modal.eiprojectk.main-modal')

    <!-- Loading Indicator -->
    <div wire:loading class="position-fixed"
        style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>
</div>
