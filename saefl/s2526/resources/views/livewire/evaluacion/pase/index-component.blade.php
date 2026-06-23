<div>

    <div class="py-2">
        <div class="d-flex justify-content-between alert alert-info mb-0">
            <div>
                <h3>Gestión de Pases Escolares
                    <small class="text-muted small d-block">Este módulo esta en etapa de validación y
                        aprobación.</small>
                </h3>
                <div>
                    Planes de estudio:
                    @foreach ($pestudios as $pestudio)
                        <span class="text-muted pl-2">{{ $pestudio->name ?? null }}</span> ||
                    @endforeach
                </div>
            </div>
            <div>
                <!-- Botón para crear nuevo pase -->
                <button class="btn btn-primary btn-sm" wire:click="openCreateModal">
                    <i class="fas fa-plus-circle mr-1"></i> Nuevo Pase
                </button>
            </div>

        </div>
    </div>


    <!-- Filtros y Búsqueda -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search" class="font-weight-bold">Buscar</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="search" wire:model.debounce.300ms="search"
                                placeholder="Estudiante, profesor, descripción...">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="selectedPestudio" class="font-weight-bold">Plan de Estudio</label>
                        <select class="form-control" id="selectedPestudio" wire:model="selectedPestudio">
                            <option value="">Todos los planes</option>
                            @foreach ($pestudios as $pestudio)
                                <option value="{{ $pestudio->id }}">{{ $pestudio->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="selectedStatus" class="font-weight-bold">Estado</label>
                        <select class="form-control" id="selectedStatus" wire:model="selectedStatus">
                            <option value="">Todos los estados</option>
                            @foreach ($statusOptions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="perPage" class="font-weight-bold">Por página</label>
                        <select class="form-control" id="perPage" wire:model="perPage">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Indicadores de filtros activos -->
            @if ($search || $selectedPestudio || $selectedStatus)
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info py-2 small mb-0">
                            <strong>Filtros activos:</strong>
                            @if ($search)
                                <span class="badge badge-light mr-1">
                                    <i class="fas fa-search mr-1"></i>Búsqueda: "{{ $search }}"
                                </span>
                            @endif
                            @if ($selectedPestudio)
                                @php
                                    $selectedPestudioName =
                                        $pestudios->where('id', $selectedPestudio)->first()->name ?? 'N/A';
                                @endphp
                                <span class="badge badge-light mr-1">
                                    <i class="fas fa-book mr-1"></i>Plan: {{ $selectedPestudioName }}
                                </span>
                            @endif
                            @if ($selectedStatus)
                                <span class="badge badge-light mr-1">
                                    <i class="fas fa-tag mr-1"></i>Estado:
                                    {{ $statusOptions[$selectedStatus] ?? $selectedStatus }}
                                </span>
                            @endif
                            <button wire:click="clearFilters" class="btn btn-sm btn-outline-danger float-right">
                                <i class="fas fa-times mr-1"></i> Limpiar filtros
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Tarjeta Principal -->
    <div class="card">


        <div class="card-body">
            <!-- Contador de resultados -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted">
                    <i class="fas fa-list mr-1"></i>
                    Total: <strong>{{ $pases->total() }}</strong> pase(s) encontrado(s)
                </div>
                @if ($pases->total() > 0)
                    <div class="text-muted small">
                        Página {{ $pases->currentPage() }} de {{ $pases->lastPage() }}
                    </div>
                @endif
            </div>

            <!-- Mensajes de estado -->
            @if ($pases->total() == 0)
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No se encontraron pases escolares</h5>
                    <p class="mb-0">
                        @if ($search || $selectedPestudio || $selectedStatus)
                            Intenta ajustar los filtros de búsqueda
                        @else
                            No hay pases escolares registrados para tu gestión
                        @endif
                    </p>
                </div>
            @else
                <!-- Tabla de Pases -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm small">
                        <thead class="thead-dark">
                            <tr>
                                <th width="50">N</th>
                                <th class="d-none d-lg-table-cell">Estudiante</th>
                                <th class="d-none d-lg-table-cell">Tipo</th>
                                <th class="d-none d-lg-table-cell">Motivo</th>
                                <th class="d-none d-lg-table-cell">Descripción</th>
                                <th class="d-none d-lg-table-cell">Fecha/Hora</th>
                                <th class="d-none d-lg-table-cell">Estado</th>
                                <th width="220">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pases as $index => $pase)
                                @php
                                    $estudiant = $pase->estudiant;
                                    $full_inscripcion = $estudiant ? $estudiant->full_inscripcion : null;
                                    $profesor = $pase->profesor;
                                    $pensum = $pase->pensum;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <strong>{{ $pases->firstItem() + $index }}</strong>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <div class="font-weight-bold">
                                            <i class="fas fa-user-graduate mr-1 text-primary"></i>
                                            {{ $estudiant ? $estudiant->fullname : 'N/A' }}
                                        </div>
                                        <hr class="m-1 p-0">
                                        <div class="text-muted small">
                                            <i
                                                class="fas fa-id-card mr-1"></i>{{ $full_inscripcion ?? 'Sin inscripción' }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-chalkboard-teacher mr-1"></i>
                                            Prof: {{ $profesor ? $profesor->fullname : 'N/A' }}<br>
                                            <i class="fas fa-book mr-1"></i>
                                            {{ $pensum ? $pensum->asignatura->name : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge badge-info">
                                            {{ $typeOptions[$pase->type] ?? $pase->type }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge badge-secondary">
                                            {{ $motiveOptions[$pase->motive] ?? $pase->motive }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                            title="{{ $pase->description }}">
                                            {{ \Str::limit($pase->description, 50) }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if ($pase->date_time)
                                            <div class="small">
                                                <i class="fas fa-calendar-alt mr-1 text-success"></i>
                                                {{ $pase->date_time->format('d-m-Y') }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $pase->date_time->format('h:i a') }}
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span
                                            class="badge badge-{{ $pase->status_notifications ? 'success' : 'secondary' }}">
                                            {{ $statusOptions[$pase->status] ?? $pase->status }}
                                        </span>
                                        @if ($pase->status_notifications)
                                            <div class="text-success small mt-1">
                                                <i class="fas fa-check-circle mr-1"></i>Notificado
                                            </div>
                                        @else
                                            <div class="text-warning small mt-1">
                                                <i class="fas fa-clock mr-1"></i>Pendiente
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- PDF -->
                                            <a title="Generar PDF" class="btn btn-dark"
                                                href="{{ route('permissions.pases.pdf.certificate', $pase->id) }}"
                                                target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>

                                            <!-- Cambiar Estado - Dropdown -->
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-info dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    title="Cambiar estado"
                                                    {{ $pase->status_notifications ? 'disabled' : '' }}>
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <h6 class="dropdown-header">Cambiar estado a:</h6>
                                                    @foreach ($statusOptions as $key => $value)
                                                        @if ($key != $pase->status)
                                                            <a class="dropdown-item" href="#"
                                                                wire:click="confirmChangeStatus({{ $pase->id }}, '{{ $key }}')">
                                                                <i class="fas fa-arrow-right mr-1 text-primary"></i>
                                                                {{ $value }}
                                                            </a>
                                                        @else
                                                            <span class="dropdown-item text-muted disabled">
                                                                <i class="fas fa-check mr-1 text-success"></i>
                                                                Actual: {{ $value }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#"
                                                        wire:click="openStatusModal({{ $pase->id }})">
                                                        <i class="fas fa-cog mr-1 text-warning"></i>
                                                        Gestionar estado avanzado...
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Editar -->
                                            <button title="Editar"
                                                class="btn btn-warning {{ $pase->status_notifications ? 'disabled' : '' }}"
                                                wire:click="openEditModal({{ $pase->id }})"
                                                {{ $pase->status_notifications ? 'disabled' : '' }}>
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Enviar Notificación -->
                                            <button title="Enviar notificación"
                                                class="btn btn-success {{ $pase->status_notifications ? 'disabled' : '' }}"
                                                wire:click="alertQuestion({{ $pase->id }}, 'sendNotification')"
                                                {{ $pase->status_notifications ? 'disabled' : '' }}>
                                                <i class="fas fa-paper-plane"></i>
                                            </button>

                                            <!-- Vista Previa -->
                                            <a title="Vista previa" class="btn btn-info"
                                                href="{{ route('evaluacions.permissions.pases.view', $pase->id) }}"
                                                target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Eliminar -->
                                            <button title="Eliminar"
                                                class="btn btn-danger {{ $pase->status_notifications ? 'disabled' : '' }}"
                                                wire:click="alertConfirm({{ $pase->id }})"
                                                {{ $pase->status_notifications ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Mostrando {{ $pases->firstItem() }} a {{ $pases->lastItem() }} de {{ $pases->total() }}
                        registros
                    </div>
                    {{ $pases->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Crear -->
    @if ($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <form wire:submit.prevent="create">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Registrar Nuevo Pase Escolar
                            </h5>
                            <button type="button" class="close text-white" wire:click="closeCreateModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            @include('livewire.evaluacion.pase.form.fields')
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Registrar Pase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para Editar -->
    @if ($showEditModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <form wire:submit.prevent="update">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">
                                <i class="fas fa-edit mr-2"></i>
                                Editar Pase Escolar
                            </h5>
                            <button type="button" class="close" wire:click="closeEditModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            @include('livewire.evaluacion.pase.form.fields')
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeEditModal">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i> Actualizar Pase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para Cambiar Estado -->
    @if ($showStatusModal && $paseForStatus)
        <div class="modal fade show d-block" tabindex="-1" role="dialog"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <form wire:submit.prevent="updateStatus">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Cambiar Estado del Pase
                            </h5>
                            <button type="button" class="close text-white" wire:click="closeStatusModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-light">
                                        <h6 class="mb-2">Información del Pase:</h6>
                                        <p class="mb-1">
                                            <strong>Estudiante:</strong>
                                            {{ $paseForStatus->estudiant->fullname ?? 'N/A' }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Estado actual:</strong>
                                            <span class="badge badge-secondary">
                                                {{ $statusOptions[$paseForStatus->status] ?? $paseForStatus->status }}
                                            </span>
                                        </p>
                                        <p class="mb-0">
                                            <strong>Fecha:</strong>
                                            {{ $paseForStatus->date_time ? $paseForStatus->date_time->format('d-m-Y h:i a') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="new_status" class="font-weight-bold">Nuevo Estado</label>
                                <select class="form-control @error('new_status') is-invalid @enderror" id="new_status"
                                    wire:model="new_status" required>
                                    <option value="">Seleccione un estado</option>
                                    @foreach ($statusOptions as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ $key == $paseForStatus->status ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('new_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($paseForStatus->status_notifications)
                                <div class="alert alert-warning small">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Este pase ya ha sido notificado. Los cambios de estado podrían requerir notificación
                                    adicional.
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeStatusModal">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save mr-1"></i> Actualizar Estado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
