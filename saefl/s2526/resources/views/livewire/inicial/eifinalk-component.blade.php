<div>
    <div class="container-fluid py-2 px-1">
        <div class="d-flex justify-content-between align-items-center alert alert-secondary mb-4">
            <h2 class="h4 font-weight-bold text-dark mb-0">
                <i class="{{ $icon_menus['eifinalks'] }} fa-1x pr-2"></i>
                Gestión de Informes Finales - Educación Inicial

            </h2>
            <button type="button" class="btn btn-primary" wire:click="openModal">
                <i class="fas fa-plus"></i> Nuevo Informe
            </button>
        </div>

        {{-- Mensajes flash --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Modal --}}
        @includeWhen($showModal, 'livewire.inicial.modal.eifinalk.create')

        {{-- Nav Tabs --}}
        <ul class="nav nav-tabs nav-fill" id="eifinalkTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'informesList' ? 'active' : '' }}"
                    id="informesList-tab"
                    wire:click="$set('activeTab', 'informesList')"
                    data-toggle="tab"
                    href="#informesList"
                    role="tab"
                    aria-controls="informesList"
                    aria-selected="{{ $activeTab === 'informesList' ? 'true' : 'false' }}">
                    <i class="fas fa-file-alt mr-2"></i>Informes registrados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'estudiantsList' ? 'active' : '' }}"
                    id="estudiantsList-tab"
                    wire:click="$set('activeTab', 'estudiantsList')"
                    data-toggle="tab"
                    href="#estudiantsList"
                    role="tab"
                    aria-controls="estudiantsList"
                    aria-selected="{{ $activeTab === 'estudiantsList' ? 'true' : 'false' }}">
                    <i class="fas fa-users mr-2"></i>Listado de estudiantes
                </a>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content border border-top-0" id="eifinalkTabContent">
            {{-- Tab 1: Informes registrados --}}
            <div class="tab-pane fade {{ $activeTab === 'informesList' ? 'show active' : '' }}"
                id="informesList"
                role="tabpanel"
                aria-labelledby="informesList-tab">

                <div class="p-2">
                    {{-- Filtros --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                {{-- Filtro de Plan de evaluación --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="filter_pevaluacion">Plan de evaluación</label>
                                        <select wire:model="filter_pevaluacion" class="form-control" id="filter_pevaluacion">
                                            <option value="">Todas las evaluaciones</option>
                                            @foreach($list_pevaluacion as $id => $name)
                                                <option value="{{ $id }}">{{ $name ?? 'Sin objetivo' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Filtro de Estudiante --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="filter_estudiant">Estudiante</label>
                                        <input type="text"
                                            wire:model.live.debounce.300ms="filter_estudiant"
                                            class="form-control"
                                            id="filter_estudiant"
                                            placeholder="Buscar por nombre o cédula...">
                                    </div>
                                </div>

                                {{-- Filtro de Título --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="filter_title">Título</label>
                                        <input type="text"
                                            wire:model.live.debounce.300ms="filter_title"
                                            class="form-control"
                                            id="filter_title"
                                            placeholder="Buscar por título...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de informes --}}
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Título</th>
                                <th>Orden</th>
                                <th>P.Evaluación</th>
                                <th>Estudiante</th>
                                <th>Áreas y Aprendizajes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($eifinalks ?? [] as $item)
                                <tr>
                                    <td>{{ $item->resumen_titulo ?? $item->title }}</td>
                                    <td>{{ $item->order ?? null }}</td>
                                    <td>{{ optional($item->pevaluacion)->description ?? '-' }}</td>
                                    <td>{{ optional($item->estudiant)->fullname ?? '-' }}</td>
                                    <td>
                                        @if($item->expectations->count() > 0)
                                            <div class="small">
                                                @foreach($item->expectations->groupBy(function($expectation) {
                                                    return $expectation->area ? $expectation->area->name : 'Sin área';
                                                }) as $areaName => $expectations)
                                                    <div class="mb-1">
                                                        <strong class="text-primary">{{ $areaName }}</strong>
                                                        <ul class="list-unstyled ml-3 mb-0">
                                                            @foreach($expectations as $expectation)
                                                                <li>
                                                                    <i class="fas fa-check text-success small"></i>
                                                                    {{ Str::limit($expectation->description, 50) }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Sin aprendizajes seleccionados</span>
                                        @endif
                                    </td>
                                    <td id="actions-{{ $item->id }}">

                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="{{ route('inicials.eifinalks.print', $item->id) }}"
                                            class="btn btn-info btn-sm"
                                            title="Imprimir"
                                            target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <button wire:click="edit({{ $item->id }})" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $item->id }})" class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Sin informes registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Tab 2: Listado de estudiantes --}}
            <div class="tab-pane fade {{ $activeTab === 'estudiantsList' ? 'show active' : '' }}"
                id="estudiantsList"
                role="tabpanel"
                aria-labelledby="estudiantsList-tab">
                <div class="p-2">
                    {{-- Filtros --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                {{-- Filtro de Plan de evaluación --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="filter_pevaluacion_estudiantes">Plan de evaluación</label>
                                        <select wire:model="filter_pevaluacion_estudiantes" class="form-control" id="filter_pevaluacion_estudiantes">
                                            <option value="">Seleccione un plan de evaluación</option>
                                            @foreach($list_pevaluacion as $id => $name)
                                                <option value="{{ $id }}">{{ $name ?? 'Sin objetivo' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Filtro de Estudiante --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="filter_estudiant_search">Buscar estudiante</label>
                                        <input type="text"
                                            wire:model.live.debounce.300ms="filter_estudiant_search"
                                            class="form-control"
                                            id="filter_estudiant_search"
                                            placeholder="Buscar por nombre o cédula...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de estudiantes --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cédula</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($estudiantes ?? [] as $estudiante)
                                    <tr>
                                        <td>{{ $estudiante->ci_estudiant }}</td>
                                        <td>{{ $estudiante->fullname }}</td>
                                        <td>
                                            @if($estudiante->hasEifinalkForLapso($lapso_id))
                                                <span class="badge badge-success">Con informe</span>
                                            @else
                                                <span class="badge badge-warning">Sin informe</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($estudiante->hasEifinalkForLapso($lapso_id))
                                                <a href="{{ route('inicials.eifinalks.print-all-for-lapso', ['estudiant' => $estudiante, 'lapso' => $filter_pevaluacion_estudiantes ? $pevaluacions->firstWhere('id', $filter_pevaluacion_estudiantes)->lapso_id : null]) }}"
                                                    class="btn btn-info btn-sm"
                                                    title="Ver informe"
                                                    target="_blank">
                                                    <i class="fas fa-eye"></i> Ver informe
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            @if($filter_pevaluacion_estudiantes)
                                                No se encontraron estudiantes para el plan de evaluación seleccionado.
                                            @else
                                                Seleccione un plan de evaluación para ver los estudiantes.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
