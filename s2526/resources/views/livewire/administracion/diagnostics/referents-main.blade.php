<div>
    <!-- ============ HEADER Y NAVEGACIÓN MEJORADA ============ -->
    <div class="card shadow-sm border-0 mb-3 rounded-lg overflow-hidden">
        <div class="card-header bg-white p-0">
            <!-- Breadcrumb Visual Stepper -->
            <div class="d-flex align-items-stretch">
                <!-- Step 1: Referentes (Always Visible) -->
                <div class="flex-fill p-3 position-relative {{ $viewMode === 'referents' ? 'alert-primary border-bottom border-primary' : 'bg-light text-muted cursor-pointer' }}"
                    style="transition: all 0.3s; cursor: {{ $viewMode !== 'referents' ? 'pointer' : 'default' }}; border-bottom-width: 3px !important;"
                    @if ($viewMode !== 'referents') wire:click="backToReferents" @endif>
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 {{ $viewMode === 'referents' ? 'bg-primary text-white shadow-sm' : 'bg-secondary text-white-50' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="fas fa-book"></i>
                        </div>
                        <div style="min-width: 0;">
                            <h6
                                class="mb-0 font-weight-bold {{ $viewMode === 'referents' ? 'text-primary' : 'text-muted' }}">
                                Referentes Curriculares</h6>
                            <small
                                class="d-none d-md-block {{ $viewMode === 'referents' ? 'text-primary' : 'text-muted' }}">Catálogo
                                Institucional de Normativas y Planes</small>
                        </div>
                    </div>
                    <!-- Active Indicator Arrow -->
                    @if ($viewMode === 'referents')
                        <div class="position-absolute d-none d-md-block"
                            style="right: -10px; top: 50%; width: 20px; height: 20px; background: #cce5ff; transform: translateY(-50%) rotate(45deg); z-index: 10; border-top: 2px solid white; border-right: 2px solid white;">
                        </div>
                    @else
                        <i class="fas fa-chevron-right position-absolute text-muted"
                            style="right: 15px; top: 50%; transform: translateY(-50%); opacity: 0.5;"></i>
                    @endif
                </div>

                <!-- Step 2: Competencias (Visible if Referent Selected) -->
                @if ($selectedReferentId)
                    <div class="flex-fill p-3 position-relative {{ $viewMode === 'competencies' ? 'alert-info border-bottom border-info' : ($viewMode === 'indicators' ? 'bg-light text-muted cursor-pointer' : 'd-none') }}"
                        style="border-left: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; border-bottom-width: 3px !important;"
                        @if ($viewMode === 'indicators') wire:click="backToCompetencies" @endif>

                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 {{ $viewMode === 'competencies' ? 'bg-info text-white shadow-sm' : 'bg-secondary text-white-50' }}"
                                style="width: 32px; height: 32px; min-width: 32px;">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div style="min-width: 0;"> <!-- Fix for truncated text flex child -->
                                <h6 class="mb-0 font-weight-bold {{ $viewMode === 'competencies' ? 'text-info' : 'text-muted' }}"
                                    style="">
                                    Competencias
                                </h6>
                            </div>
                        </div>

                        @if ($viewMode === 'competencies')
                            <div class="position-absolute d-none d-md-block"
                                style="right: -10px; top: 50%; width: 20px; height: 20px; background: #d1ecf1; transform: translateY(-50%) rotate(45deg); z-index: 10; border-top: 2px solid white; border-right: 2px solid white;">
                            </div>
                        @elseif($viewMode === 'indicators')
                            <i class="fas fa-chevron-right position-absolute text-muted"
                                style="right: 15px; top: 50%; transform: translateY(-50%); opacity: 0.5;"></i>
                        @endif
                    </div>
                @endif

                <!-- Step 3: Indicadores (Visible if Competency Selected) -->
                @if ($selectedCompetencyId && $viewMode === 'indicators')
                    <div class="flex-fill p-3 alert-success position-relative border-bottom border-success"
                        style="border-left: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; border-bottom-width: 3px !important;">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 bg-success text-white shadow-sm"
                                style="width: 32px; height: 32px; min-width: 32px;">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div style="min-width: 0;">
                                <h6 class="mb-0 font-weight-bold text-truncate text-success" style="max-width: 200px;">
                                    Indicadores
                                </h6>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons (Right Aligned) -->
                <div class="ml-auto p-3 d-flex align-items-center bg-light border-left pl-4">
                    <!-- Back Button -->
                    @if ($viewMode !== 'referents')
                        <button wire:click="{{ $viewMode === 'indicators' ? 'backToCompetencies' : 'backToReferents' }}"
                            class="btn btn-outline-secondary btn-sm mr-2 shadow-sm" title="Ir Atrás">
                            <i class="fas fa-arrow-left mr-1"></i> Atrás
                        </button>
                    @endif

                    @if ($viewMode === 'referents')
                        <button wire:click="createReferent" class="btn btn-primary shadow-sm btn-sm">
                            <i class="fas fa-plus-circle mr-1"></i><span class="d-none d-md-inline">Nuevo
                                Referente</span>
                        </button>
                    @elseif($viewMode === 'competencies')
                        <button wire:click="createCompetency" class="btn btn-info shadow-sm btn-sm">
                            <i class="fas fa-plus-circle mr-1"></i><span class="d-none d-md-inline">Nueva
                                Competencia</span>
                        </button>
                    @elseif($viewMode === 'indicators')
                        <button wire:click="createIndicator" class="btn btn-success shadow-sm btn-sm">
                            <i class="fas fa-plus-circle mr-1"></i><span class="d-none d-md-inline">Nuevo
                                Indicador</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============ BARRA DE BÚSQUEDA Y FILTROS ============ -->
        <div class="card-body p-2">
            @if ($viewMode === 'referents')
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label for="search" class="small mb-1">Buscar referente</label>
                            <div class="input-group input-group-sm">
                                <input wire:model.debounce.300ms="search" type="text" id="search"
                                    class="form-control form-control-sm" placeholder="Nombre, código, descripción...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="filterActive" class="small mb-1">Estado</label>
                            <select wire:model="filterActive" id="filterActive" class="form-control form-control-sm">
                                <option value="all">Todos</option>
                                <option value="active">Solo Activos</option>
                                <option value="inactive">Solo Inactivos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- ✅ Mostrar estadísticas del filtro actual -->
                        <div class="alert alert-info small mb-0 p-2">
                            <i class="fas fa-filter mr-1"></i>
                            @if ($filterActive === 'all')
                                Mostrando <strong>todos</strong> los referentes
                            @elseif($filterActive === 'active')
                                Mostrando solo referentes <strong class="text-success">activos</strong>
                            @else
                                Mostrando solo referentes <strong class="text-secondary">inactivos</strong>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ============ MENSAJES DE FLASH ============ -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- ============ MODALES ============ -->
    @include('livewire.administracion.diagnostics.modals.competency-modal')
    @include('livewire.administracion.diagnostics.modals.indicator-modal')
    @include('livewire.administracion.diagnostics.modals.referent-detail-modal')
    @include('livewire.administracion.diagnostics.modals.referent-modal')

    <!-- ============ CONTENIDO SEGÚN MODO DE VISTA ============ -->
    @if ($viewMode === 'referents')

        <!-- TABLA DE REFERENTES -->

        <div class="card card-outline card-secondary">
            <div class="card-body p-0 table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm small mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50" class="text-center">ID</th>
                            <th>Nombre</th>
                            <th width="150" class="text-center">Plan de Estudio</th>
                            <th width="120" class="text-center">Código</th>
                            <th width="80" class="text-center">Versión</th>
                            <th width="100" class="text-center">Estado</th>
                            <th>Descripción</th>
                            <th width="120" class="text-center">Competencias</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($referents as $referent)
                            <tr class="{{ $referent->active ? 'text-dark' : 'text-muted bg-light' }}">
                                <td class="text-center align-middle">{{ $referent->id }}</td>
                                <td class="align-middle font-weight-bold">
                                    @if ($referent->active)
                                        <i class="fas fa-check-circle text-success mr-1"></i>
                                    @endif
                                    {{ $referent->name }}
                                </td>
                                <td class="align-middle text-center">
                                    @if ($referent->pestudio)
                                        <span class="badge badge-light border">{{ $referent->pestudio->name }}</span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-light border">{{ $referent->code }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-info">v{{ $referent->version }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    @if ($referent->active)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td class="align-middle text-truncate" style="max-width: 250px;">
                                    {{ $referent->description }}
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-primary">
                                        {{ $referent->competencies_count }} comp.
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="openDetailModal({{ $referent->id }})"
                                            class="btn btn-secondary btn-xs" title="Ver Detalle Completo">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button wire:click="showReferentDetail({{ $referent->id }})"
                                            class="btn btn-info btn-xs" title="Gestionar Competencias">
                                            <i class="fas fa-tasks"></i>
                                        </button>
                                        <button wire:click="editReferent({{ $referent->id }})"
                                            class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="toggleReferentActive({{ $referent->id }})"
                                            class="btn btn-{{ $referent->active ? 'secondary' : 'success' }} btn-xs"
                                            title="{{ $referent->active ? 'Desactivar' : 'Activar' }}">
                                            <i class="fas fa-{{ $referent->active ? 'ban' : 'check' }}"></i>
                                        </button>
                                        <button wire:click="deleteReferent({{ $referent->id }})"
                                            class="btn btn-danger btn-xs" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                    No hay referentes registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (method_exists($referents, 'links'))
                <div class="card-footer clearfix p-2">
                    <div class="float-right">
                        {{ $referents->links() }}
                    </div>
                </div>
            @endif

        </div>
    @elseif($viewMode === 'competencies')
        <!-- TABLA DE COMPETENCIAS -->
        <div class="card card-outline card-info">
            <div class="card-header p-2 table-warning text-dark alert-secondary ">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-tasks mr-1"></i>
                        <span class="font-weight-bold">Competencias del Referente:</span>
                        <span class="font-weight-light">{{ $currentReferent->name ?? 'Referente' }}</span>
                    </h6>
                    <small>
                        Total: {{ $competencies->total() }} competencias
                    </small>
                </div>

                <!-- Filters Row -->
                <div class="form-row align-items-center bg-white p-2 border rounded">
                    <div class="col-auto">
                        <i class="fas fa-filter text-muted"></i>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select wire:model="compFilterGrado" class="form-control form-control-sm">
                            <option value="">Filtrar por Grado...</option>
                            @foreach ($compFilterGradosList as $grado)
                                <option value="{{ $grado['id'] }}">{{ $grado['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <select wire:model="compFilterPensum" class="form-control form-control-sm">
                            <option value="">Filtrar por Asignatura...</option>
                            @foreach ($compFilterPensumsList as $pensum)
                                <option value="{{ $pensum['id'] }}">{{ $pensum['fullname'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto ml-auto">
                        <button wire:click="resetCompFilters" class="btn btn-xs btn-outline-secondary"
                            title="Limpiar Filtros">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm small mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50" class="text-center">ID</th>
                            <th>Nombre</th>
                            <th width="150" class="text-center">Área de Formación</th>
                            <th>Descripción</th>
                            <th width="100" class="text-center">Indicadores</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($competencies as $competency)
                            <tr>
                                <td class="text-center align-middle">{{ $competency->id }}</td>
                                <td class="align-middle font-weight-bold">{{ $competency->name }}</td>
                                <td class="text-center align-middle">
                                    @if ($competency->pensum)
                                        <span class="badge badge-light">{{ $competency->pensum->fullname }}</span>
                                    @else
                                        <span class="badge badge-secondary">Transversal</span>
                                    @endif
                                </td>
                                <td class="align-middle text-truncate" style="max-width: 250px;">
                                    {{ $competency->description }}
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-info">
                                        {{ $competency->indicators_count }} ind.
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="showCompetencyDetail({{ $competency->id }})"
                                            class="btn btn-success btn-xs" title="Ver Indicadores">
                                            <i class="fas fa-tasks"></i>
                                        </button>
                                        <button wire:click="editCompetency({{ $competency->id }})"
                                            class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="deleteCompetency({{ $competency->id }})"
                                            class="btn btn-danger btn-xs" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-tasks fa-2x mb-2 d-block text-secondary"></i>
                                    No hay competencias registradas para este referente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($competencies, 'links') && $competencies->hasPages())
                <div class="card-footer clearfix p-2">
                    <div class="float-right">
                        {{ $competencies->links() }}
                    </div>
                </div>
            @endif
        </div>
    @elseif($viewMode === 'indicators')
        <!-- TABLA DE INDICADORES -->
        <div class="card card-outline card-success">
            <div class="card-header p-2 alert-success text-dark">
                <h6 class="card-title mb-0">
                    <i class="fas fa-bullseye mr-1"></i>
                    <span class="font-weight-bold">Indicadores de la Competencia:</span>
                    <span class="font-weight-light">{{ $currentCompetency->name ?? 'Competencia' }} </span>
                    <small class="float-right">
                        Nivel esperado:
                        @php $levelInfo = $this->getLevelLabel($indicator_expected_level ?? 3) @endphp
                        <span class="badge badge-{{ $levelInfo['class'] }}">{{ $levelInfo['label'] }}</span>
                    </small>
                </h6>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm small mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50" class="text-center">ID</th>
                            <th width="120" class="text-center">Código</th>
                            <th>Descripción</th>
                            <th width="120" class="text-center">Nivel Esperado</th>
                            <th width="120" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($indicators as $indicator)
                            @php $levelInfo = $this->getLevelLabel($indicator->expected_level) @endphp
                            <tr>
                                <td class="text-center align-middle">{{ $indicator->id }}</td>
                                <td class="text-center align-middle font-weight-bold">
                                    <span class="badge badge-light border">{{ $indicator->code }}</span>
                                </td>
                                <td class="align-middle">{{ $indicator->description }}</td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-{{ $levelInfo['class'] }}">
                                        {{ $levelInfo['label'] }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="editIndicator({{ $indicator->id }})"
                                            class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="deleteIndicator({{ $indicator->id }})"
                                            class="btn btn-danger btn-xs" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <i class="fas fa-bullseye fa-2x mb-2 d-block text-secondary"></i>
                                    No hay indicadores registrados para esta competencia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (method_exists($indicators, 'links') && $indicators->hasPages())
                <div class="card-footer clearfix p-2">
                    <div class="float-right">
                        {{ $indicators->links() }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Indicador de carga global para cualquier acción -->
    <div wire:loading.flex style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <div class="alert alert-light shadow-lg">
            <div class="flex items-center font-weight-bold text-dark">
                <div class="spinner-border spinner-border-sm mr-2"></div>
                <span>Procesando...</span>
            </div>
        </div>
    </div>
</div>
