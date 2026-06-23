<div>
    <div class="mt-2">
        <div class="">
            
            {{-- 🔍 Filtros principales --}}
            <div class="d-flex flex-wrap align-items-end" style="gap: .35rem;">

                {{-- 🔍 Buscar por CI o nombre --}}
                <div class="flex-grow-1 mr-1 mb-2" style="min-width: 200px;">
                    <div class="input-group input-group-sm">
                        <input wire:model.debounce.500ms="filter_ci"
                               type="text"
                               class="form-control"
                               placeholder="CI o nombre estudiante/representante...">
                        <div class="input-group-append" wire:click="resetFilters">
                            <span class="input-group-text"><i class="fas fa-times"></i></span>
                        </div>
                    </div>
                </div>

                {{-- 🧾 Plan de Pago --}}
                <div class="mr-1 mb-2" style="min-width: 210px;">
                    <select wire:model="planpago_id" class="form-control form-control-sm">
                        <option value="">-- Plan de Pago --</option>
                        @foreach ($list_planpagos as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 📌 Tipo de cuenta --}}
                <div class="mr-1 mb-2" style="min-width: 150px;">
                    <select wire:model="type" class="form-control form-control-sm">
                        <option value="">-- Tipo --</option>
                        <option value="GENERAL">GENERAL</option>
                        <option value="INDIVIDUAL">INDIVIDUAL</option>
                    </select>
                </div>

                {{-- ⚠ Estado incobrable --}}
                <div class="mr-1 mb-2" style="min-width: 170px;">
                    <select wire:model="status_bad" class="form-control form-control-sm">
                        <option value="">-- Estado cuenta --</option>
                        <option value="normal">Normal</option>
                        <option value="incobrable">Incobrable</option>
                    </select>
                </div>

                {{-- 📅 Fecha inicial --}}
                <div class="mr-1 mb-2" style="min-width: 150px;">
                    <input type="date"
                           class="form-control form-control-sm"
                           wire:model="finicial"
                           title="Fecha de vencimiento desde">
                </div>

                {{-- 📅 Fecha final --}}
                <div class="mr-1 mb-2" style="min-width: 150px;">
                    <input type="date"
                           class="form-control form-control-sm"
                           wire:model="ffinal"
                           title="Fecha de vencimiento hasta">
                </div>

                {{-- ➕ Botón Crear --}}
                <div style="min-width: 80px;" class="ml-auto">
                    <button class="btn btn-success btn-sm w-100"
                            wire:click="showCreateModal">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                </div>

            </div>


            <hr>

            {{-- Table --}}

            <div class="pb-0 mb-0">
                {{-- Statistics --}}
                <div class="row my-2">
                    <div class="col-md-3">
                        <span class="badge badge-primary">Total: {{ $statistics['total'] }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="badge badge-info">Individual: {{ $statistics['individual'] }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="badge badge-success">General: {{ $statistics['general'] }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="badge badge-danger">Incobrable: {{ $statistics['incobrable'] }}</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm small">
                    <thead>
                        <tr>
                            <th class="d-none d-sm-table-cell">N</th>
                            <th>Nombre</th>
                            <th>Plan de Pago</th>
                            <th>Tipo</th>
                            <th>F.Vencimiento</th>
                            <th>Descripción</th>
                            <th title="Número de cuentas de cobro">N.Cuentas</th>
                            <th title="Estudiante">Representante/Estudiante</th>
                            <th title="Estudiante">Incobrable</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuentaxpagars as $index => $cuentaxpagar)
                            @php
                                $estudiant = $cuentaxpagar->estudiant;
                                $representant = $estudiant ? $estudiant->representant : null;
                                $conceptopagos = $cuentaxpagar->conceptopagos;
                                $conceptopagos_exchange_ammount = $conceptopagos
                                    ? $conceptopagos->sum('exchange_ammount')
                                    : 0;
                            @endphp

                            <tr class="table-{{ empty($cuentaxpagar->administrativa->id) ? 'default' : 'success' }}">
                                <td class="d-none d-sm-table-cell">
                                    {{ ($cuentaxpagars->currentPage() - 1) * $cuentaxpagars->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    {{ $cuentaxpagar->name ?? '' }}
                                </td>
                                <td>
                                    {{ $cuentaxpagar->planpago->name ?? '' }}
                                </td>
                                <td>
                                    <span
                                        class="badge badge-{{ $cuentaxpagar->type == 'INDIVIDUAL' ? 'info' : 'success' }}">
                                        {{ $cuentaxpagar->type ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    {{ f_date($cuentaxpagar->date_expiration) ?? '' }}
                                </td>
                                <td>
                                    {{ \Str::limit($cuentaxpagar->description, 30) }}
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $conceptopagos ? $conceptopagos->count() : 0 }}
                                    </span>
                                    [{{ number_format($conceptopagos_exchange_ammount, 2) }}]
                                </td>
                                <td>
                                    @if ($estudiant && $representant)
                                        <small>
                                            {{ $representant->ci_representant ?? '' }} /
                                            {{ $estudiant->ci_estudiant ?? '' }}
                                        </small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-{{ $cuentaxpagar->status_bad == 'true' ? 'danger' : 'success' }}"
                                        wire:click="toggleStatusBad({{ $cuentaxpagar->id }})"
                                        wire:loading.attr="disabled" @if (!$cuentaxpagar->status_registro_pago) disabled @endif
                                        title="Cambiar estado de incobrable">
                                        {{ $cuentaxpagar->status_bad == 'true' ? 'SI' : 'NO' }}
                                    </button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        {{-- Detalles --}}
                                        <button class="btn btn-info btn-sm"
                                            wire:click="showInfo({{ $cuentaxpagar->id }})" title="Detalles">
                                            <i class="fas fa-info"></i>
                                        </button>

                                        {{-- Editar --}}
                                        <button class="btn btn-warning btn-sm"
                                            @if (!$cuentaxpagar->edit) disabled @endif
                                            wire:click="showEdit({{ $cuentaxpagar->id }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- Eliminar --}}
                                        @if ($cuentaxpagar->status_delete)
                                            <button class="btn btn-danger btn-sm"
                                                wire:click="confirmDelete({{ $cuentaxpagar->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-danger btn-sm" disabled title="No se puede eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">
                                    No se encontraron cuentas por pagar con los filtros aplicados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <select class="form-control form-control-sm" style="width: auto;" wire:model="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>

                <div>
                    {{ $cuentaxpagars->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Información --}}
    @if ($showInfoModal && $cuentaxpagarDetails)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
            role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle"></i> Detalles del Concepto de Cobro
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeModals">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Información General</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $cuentaxpagarDetails['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Plan de Pago:</strong></td>
                                        <td>{{ $cuentaxpagarDetails['planpago'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipo:</strong></td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $cuentaxpagarDetails['type'] == 'INDIVIDUAL' ? 'info' : 'success' }}">
                                                {{ $cuentaxpagarDetails['type'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha Vencimiento:</strong></td>
                                        <td>{{ $cuentaxpagarDetails['date_expiration'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Incobrable:</strong></td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $cuentaxpagarDetails['status_bad'] == 'Sí' ? 'danger' : 'success' }}">
                                                {{ $cuentaxpagarDetails['status_bad'] }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Fechas del Calendario</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Inicio:</strong></td>
                                        <td>{{ $cuentaxpagarDetails['date_calendar_start'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fin:</strong></td>
                                        <td>{{ $cuentaxpagarDetails['date_calendar_end'] }}</td>
                                    </tr>
                                </table>

                                @if ($cuentaxpagarDetails['estudiant'])
                                    <h6 class="font-weight-bold mt-3">Información del Estudiante</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>CI Estudiante:</strong></td>
                                            <td>{{ $cuentaxpagarDetails['estudiant']['ci_estudiant'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>{{ $cuentaxpagarDetails['estudiant']['name'] }}</td>
                                        </tr>
                                        @if ($cuentaxpagarDetails['representant'])
                                            <tr>
                                                <td><strong>CI Representante:</strong></td>
                                                <td>{{ $cuentaxpagarDetails['representant']['ci_representant'] }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nombre Representante:</strong></td>
                                                <td>{{ $cuentaxpagarDetails['representant']['name'] }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="font-weight-bold">Descripción</h6>
                                <p class="border p-2 rounded">{{ $cuentaxpagarDetails['description'] }}</p>
                            </div>
                        </div>

                        @if ($cuentaxpagarDetails['observations'])
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="font-weight-bold">Observaciones</h6>
                                    <p class="border p-2 rounded">{{ $cuentaxpagarDetails['observations'] }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="font-weight-bold">Cuentas de Cobro
                                    <span
                                        class="badge badge-secondary">{{ $cuentaxpagarDetails['conceptopagos_count'] }}</span>
                                    <span class="badge badge-primary">Total:
                                        {{ $cuentaxpagarDetails['total_amount'] }}</span>
                                </h6>
                                @if (count($cuentaxpagarDetails['conceptopagos']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Concepto</th>
                                                    <th>Descripción</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cuentaxpagarDetails['conceptopagos'] as $concepto)
                                                    <tr>
                                                        <td>{{ $concepto['name'] }}</td>
                                                        <td>{{ $concepto['description'] }}</td>
                                                        <td>{{ $concepto['amount'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No hay conceptos de pago asignados.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Edición --}}
    @if ($showEditModal && $selectedCuentaxpagar)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
            role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-edit"></i> Editar Cuenta por Pagar
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeModals">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="updateCuentaxpagar">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_name" class="font-weight-bold text-secondary">Nombre</label>
                                        <input type="text" class="form-control" id="edit_name"
                                            wire:model="editForm.name" required>
                                        @error('editForm.name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_planpago_id" class="font-weight-bold text-secondary">Plan de
                                            Pago</label>
                                        <select class="form-control" id="edit_planpago_id"
                                            wire:model="editForm.planpago_id" required>
                                            <option value="">Seleccione</option>
                                            @foreach ($list_planpagos as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('editForm.planpago_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_type" class="font-weight-bold text-secondary">Tipo</label>
                                        <select class="form-control" id="edit_type" wire:model="editForm.type"
                                            required>
                                            <option value="">Seleccione</option>
                                            <option value="GENERAL">GENERAL</option>
                                            <option value="INDIVIDUAL">INDIVIDUAL</option>
                                        </select>
                                        @error('editForm.type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="edit_estudiant_ctr"
                                        style="{{ $editForm['type'] == 'INDIVIDUAL' ? '' : 'display: none;' }}">
                                        <label for="edit_estudiant_id"
                                            class="font-weight-bold text-secondary">Estudiante</label>
                                        <select class="form-control" id="edit_estudiant_id"
                                            wire:model="editForm.estudiant_id">
                                            <option value="">Seleccione</option>
                                            @foreach ($list_estudiants as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('editForm.estudiant_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_date_expiration"
                                            class="font-weight-bold text-secondary">Fecha
                                            Vencimiento</label>
                                        <input type="date" class="form-control" id="edit_date_expiration"
                                            wire:model="editForm.date_expiration" required>
                                        @error('editForm.date_expiration')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_date_calendar_start"
                                            class="font-weight-bold text-secondary">Fecha Inicio Calendario</label>
                                        <input type="date" class="form-control" id="edit_date_calendar_start"
                                            wire:model="editForm.date_calendar_start" required>
                                        @error('editForm.date_calendar_start')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_date_calendar_end"
                                            class="font-weight-bold text-secondary">Fecha Fin Calendario</label>
                                        <input type="date" class="form-control" id="edit_date_calendar_end"
                                            wire:model="editForm.date_calendar_end" required>
                                        @error('editForm.date_calendar_end')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_status_bad"
                                            class="font-weight-bold text-secondary">Incobrable</label>
                                        <select class="form-control" id="edit_status_bad"
                                            wire:model="editForm.status_bad" required>
                                            <option value="">Seleccione</option>
                                            <option value="true">Sí</option>
                                            <option value="false">No</option>
                                        </select>
                                        @error('editForm.status_bad')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="edit_description"
                                            class="font-weight-bold text-secondary">Descripción</label>
                                        <textarea class="form-control" id="edit_description" wire:model="editForm.description" rows="3" required></textarea>
                                        @error('editForm.description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_observations"
                                            class="font-weight-bold text-secondary">Observaciones</label>
                                        <textarea class="form-control" id="edit_observations" wire:model="editForm.observations" rows="2"></textarea>
                                        @error('editForm.observations')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancelar</button>
                        <button type="button" class="btn btn-warning" wire:click="updateCuentaxpagar">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Creación --}}
    @if ($showCreateModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
            role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle"></i> Crear Nueva Cuenta por Pagar
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeModals">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="createCuentaxpagar">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="create_name" class="font-weight-bold text-secondary">Nombre
                                            *</label>
                                        <input type="text" class="form-control" id="create_name"
                                            wire:model="createForm.name" required>
                                        @error('createForm.name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="create_planpago_id" class="font-weight-bold text-secondary">Plan
                                            de Pago *</label>
                                        <select class="form-control" id="create_planpago_id"
                                            wire:model="createForm.planpago_id" required>
                                            <option value="">Seleccione</option>
                                            @foreach ($list_planpagos as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('createForm.planpago_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="create_type" class="font-weight-bold text-secondary">Tipo
                                            *</label>
                                        <select class="form-control" id="create_type" wire:model="createForm.type"
                                            required>
                                            <option value="">Seleccione</option>
                                            <option value="GENERAL">GENERAL</option>
                                            <option value="INDIVIDUAL">INDIVIDUAL</option>
                                        </select>
                                        @error('createForm.type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- En el archivo de vista --}}
                                    <div class="form-group" id="create_estudiant_ctr"
                                        style="{{ $createForm['type'] == 'INDIVIDUAL' ? '' : 'display: none;' }}">
                                        <label for="create_estudiant_id"
                                            class="font-weight-bold text-secondary">Estudiante *</label>

                                        {{-- Input de búsqueda --}}
                                        <div class="searchable-select">
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    placeholder="Buscar por cédula o nombre..."
                                                    wire:model.debounce.500ms="estudianteSearch"
                                                    wire:loading.attr="disabled" id="create_estudiant_search">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        wire:click="clearEstudianteSearch">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Loading indicator --}}
                                            <div wire:loading wire:target="estudianteSearch" class="mt-2">
                                                <div class="text-center">
                                                    <div class="spinner-border spinner-border-sm" role="status">
                                                        <span class="sr-only">Buscando...</span>
                                                    </div>
                                                    <small class="text-muted ml-2">Buscando estudiantes...</small>
                                                </div>
                                            </div>

                                            {{-- Resultados de búsqueda --}}
                                            @if ($estudianteSearch && !empty($searchEstudiantes))
                                                <div class="search-results mt-2 border rounded shadow-sm">
                                                    @foreach ($searchEstudiantes as $estudiante)
                                                        <div class="search-result-item p-2 border-bottom cursor-pointer"
                                                            wire:click="selectEstudiante({{ $estudiante['id'] }}, '{{ $estudiante['display'] }}')"
                                                            style="cursor: pointer;" title="Clic para seleccionar">
                                                            <div class="font-weight-bold">
                                                                {{ $estudiante['ci_estudiant'] }}</div>
                                                            <div class="text-muted small">{{ $estudiante['name'] }}
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if (count($searchEstudiantes) === 0)
                                                        <div class="p-2 text-center text-muted">
                                                            No se encontraron estudiantes
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Estudiante seleccionado --}}
                                            @if ($createForm['estudiant_id'])
                                                <div class="selected-estudiante mt-2 p-2 bg-light rounded border">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>Seleccionado:</strong>
                                                            <span class="ml-2">{{ $selectedEstudianteName }}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            wire:click="clearSelectedEstudiante">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Campo oculto para el ID --}}
                                            <input type="hidden" name="createForm.estudiant_id"
                                                wire:model="createForm.estudiant_id" id="create_estudiant_id">
                                        </div>

                                        @error('createForm.estudiant_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="create_date_expiration"
                                            class="font-weight-bold text-secondary">Fecha Vencimiento *</label>
                                        <input type="date" class="form-control" id="create_date_expiration"
                                            wire:model="createForm.date_expiration" required>
                                        @error('createForm.date_expiration')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="create_date_calendar_start"
                                            class="font-weight-bold text-secondary">Fecha Inicio Calendario *</label>
                                        <input type="date" class="form-control" id="create_date_calendar_start"
                                            wire:model="createForm.date_calendar_start" required>
                                        @error('createForm.date_calendar_start')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="create_date_calendar_end"
                                            class="font-weight-bold text-secondary">Fecha Fin Calendario *</label>
                                        <input type="date" class="form-control" id="create_date_calendar_end"
                                            wire:model="createForm.date_calendar_end" required>
                                        @error('createForm.date_calendar_end')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="create_status_bad"
                                            class="font-weight-bold text-secondary">Incobrable *</label>
                                        <select class="form-control" id="create_status_bad"
                                            wire:model="createForm.status_bad" required>
                                            <option value="false">No</option>
                                            <option value="true">Sí</option>
                                        </select>
                                        @error('createForm.status_bad')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="create_description"
                                            class="font-weight-bold text-secondary">Descripción *</label>
                                        <textarea class="form-control" id="create_description" wire:model="createForm.description" rows="3" required></textarea>
                                        @error('createForm.description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="create_observations"
                                            class="font-weight-bold text-secondary">Observaciones</label>
                                        <textarea class="form-control" id="create_observations" wire:model="createForm.observations" rows="2"></textarea>
                                        @error('createForm.observations')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancelar</button>
                        <button type="button" class="btn btn-success" wire:click="createCuentaxpagar">
                            <i class="fas fa-save"></i> Crear Cuenta por Pagar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Actualizar el script JavaScript para manejar ambos modales --}}
    @section('livewireCustomeScripts')
        @parent
        <script>
            document.addEventListener('livewire:load', function() {
                // Mostrar/ocultar campo estudiante según el tipo en ambos modales
                function setupTypeToggle(prefix) {
                    const typeSelect = document.getElementById(prefix + '_type');
                    const estudiantContainer = document.getElementById(prefix + '_estudiant_ctr');

                    if (typeSelect && estudiantContainer) {
                        typeSelect.addEventListener('change', function() {
                            if (this.value === 'INDIVIDUAL') {
                                estudiantContainer.style.display = 'block';
                            } else {
                                estudiantContainer.style.display = 'none';
                            }
                        });
                    }
                }

                // Configurar ambos modales
                Livewire.on('showEditModal', function() {
                    setupTypeToggle('edit');
                });

                Livewire.on('showCreateModal', function() {
                    setupTypeToggle('create');
                });

                Livewire.on('showNotification', function(data) {
                    Swal.fire({
                        icon: data.type,
                        title: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            });
        </script>
    @endsection

    @section('stylesheet')
        @parent
        <style>
            .searchable-select {
                position: relative;
            }

            .search-results {
                position: absolute;
                z-index: 1000;
                background: white;
                width: 100%;
                max-height: 200px;
                overflow-y: auto;
            }

            .search-result-item {
                transition: background-color 0.2s;
            }

            .search-result-item:hover {
                background-color: #f8f9fa;
            }

            .selected-estudiante {
                background-color: #e9f7ef !important;
                border-color: #28a745 !important;
            }

            .cursor-pointer {
                cursor: pointer;
            }
        </style>
    @endsection
</div>
