<div>
    <div class="mt-3">

        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: .35rem;">

            {{-- 🔍 Buscar por CI o nombre --}}
            <div class="flex-fill" style="min-width: 200px;">
                <div class="input-group input-group-sm">
                    <input wire:model.debounce.500ms="filter_ci"
                           type="text"
                           class="form-control"
                           placeholder="CI o nombre estudiante/representante...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>

            {{-- 🗓 Fecha Inicial (basado en cuentaxpagar.date_expiration) --}}
            <div style="min-width: 150px;">
                <input type="date"
                       class="form-control form-control-sm"
                       wire:model="finicial">
            </div>

            {{-- 🗓 Fecha Final --}}
            <div style="min-width: 150px;">
                <input type="date"
                       class="form-control form-control-sm"
                       wire:model="ffinal">
            </div>

            {{-- 🧾 Plan de Pago --}}
            <div style="min-width: 170px;">
                <select wire:model="filter_planpago_id" class="form-control form-control-sm">
                    <option value="">Plan de Pago</option>
                    @foreach ($list_planpagos as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 📌 Tipo --}}
            <div style="min-width: 140px;">
                <select wire:model="filter_type" class="form-control form-control-sm">
                    <option value="">Tipo</option>
                    <option value="GENERAL">GENERAL</option>
                    <option value="INDIVIDUAL">INDIVIDUAL</option>
                </select>
            </div>

            {{-- 🔗 Asociación --}}
            <div style="min-width: 150px;">
                <select wire:model="filter_asociado" class="form-control form-control-sm">
                    <option value="">Asociación</option>
                    <option value="asociado">Asociado</option>
                    <option value="no_asociado">No asociado</option>
                </select>
            </div>

            {{-- ⚠ Incobrables --}}
            <div style="min-width: 150px;">
                <select wire:model="filter_status_bad" class="form-control form-control-sm">
                    <option value="">Estado Cuenta</option>
                    <option value="incobrable">Incobrable</option>
                    <option value="normal">Normal</option>
                </select>
            </div>

            {{-- ➕ Botón Crear --}}
            <div style="min-width: 80px;" class="ml-auto">
                <button class="btn btn-success btn-sm w-100"
                        wire:click="showCreateModal">
                    <i class="fas fa-plus-circle"></i>
                </button>
            </div>

        </div>



        <div class="card-body p-1">

            {{-- 🔹 Información general de resultados --}}
            <div class="alert alert-info py-2 px-3 mb-3 small">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Total registros:</strong> {{ $stats['total'] }}
                    </div>
                    <div class="col-md-3">
                        <strong>Tipo General:</strong> {{ $stats['general'] }}
                    </div>
                    <div class="col-md-3">
                        <strong>Tipo Individual:</strong> {{ $stats['individual'] }}
                    </div>
                    <div class="col-md-3">
                        <strong>Total Monto (USD):</strong> {{ number_format($stats['total_monto'], 2, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <table class="table table-striped table-hover table-sm small p-1">
                <thead class="table-secondary">
                    <tr>
                        <th>ID</th>
                        <th>Concepto</th>
                        <th>Cuenta</th>
                        <th>Plan de Pago</th>
                        <th>Incobrable</th>
                        <th>Estudiante (CI)</th>
                        <th>Monto USD.</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($concepto_pagos as $item)
                        @php 
                            $cuentaxpagar = $item->cuentaxpagar;
                            $canEdit = $item->status_edit;
                            $canDelete = !$item->conceptocancelados->count();
                        @endphp
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                {{ $item->nomconceptopago->name ?? '' }}
                                <div>{{ $cuentaxpagar->type ?? '' }}</div>
                            </td>
                            <td>{{ $cuentaxpagar->name ?? '' }}
                                <small class="d-block">{{ f_date($cuentaxpagar->date_expiration) ?? '' }}</small>
                            </td>
                            <td>{{ $item->cuentaxpagar->planpago->name ?? '' }}</td>
                            <td>{{ $cuentaxpagar->status_bad == 'true' ? 'SI' : 'NO' }}</td>
                            <td>{{ $item->cuentaxpagar->estudiant->ci_estudiant ?? '-' }}</td>
                            <td>
                                USD {{ number_format($item->exchange_ammount, 2, ',', '.') }}
                            </td>
                            <td>
                                @if($item->conceptocancelados->count())
                                    <span class="badge badge-success">-ASOCIADO-</span>
                                @else
                                    <span class="badge badge-secondary">-NO ASOCIADO-</span>
                                @endif
                                <br>
                                <small class="text-muted">
                                    {{ $item->status_active == 'true' ? 'Activo' : 'Inactivo' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    {{-- Botón Editar --}}
                                    <button wire:click="showEditModal({{ $item->id }})" 
                                            class="btn btn-warning btn-sm"
                                            title="{{ $canEdit ? 'Editar concepto' : 'Concepto no editable' }}"
                                            @if(!$canEdit) disabled @endif>
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Botón Eliminar --}}
                                    <button wire:click="alertQuestion({{ $item->id }},'concepto_delete')" 
                                            class="btn btn-danger btn-sm"
                                            title="{{ $canDelete ? 'Eliminar concepto' : 'No se puede eliminar' }}"
                                            @if(!$canDelete) disabled @endif>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                @if(!$canEdit)
                                    <small class="text-muted d-block mt-1">No editable</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $concepto_pagos->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL DE CREACIÓN --}}
    @if($showCreateModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle"></i> Crear Nueva Cuenta de Cobro
                    </h5>
                    <button type="button" class="close text-white" wire:click="closeCreateModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        {{-- Selección de Tipo CON SELECT --}}
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="create_type" class="font-weight-bold text-secondary">
                                        Tipo de Cuenta <span class="text-danger">*</span>
                                    </label>
                                    
                                    {{-- SELECT EN LUGAR DE RADIO BUTTONS --}}
                                    <select class="form-control form-control-sm" id="create_type" 
                                            wire:model="create_type" required>
                                        <option value="">-- Seleccione el tipo de cuenta --</option>
                                        <option value="GENERAL">GENERAL - Para todos los estudiantes</option>
                                        <option value="INDIVIDUAL">INDIVIDUAL - Para estudiante específico</option>
                                    </select>
                                    
                                    @error('create_type') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                    
                                    {{-- Información del tipo seleccionado --}}
                                    @if($create_type)
                                        <div class="mt-2 p-2 border rounded bg-light">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle text-primary mr-1"></i>
                                                @if($create_type == 'GENERAL')
                                                    <strong>Cuenta GENERAL:</strong> Aplicable a todos los estudiantes del plan de pago seleccionado.
                                                @else
                                                    <strong>Cuenta INDIVIDUAL:</strong> Específica para un estudiante en particular.
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Campos según el tipo seleccionado --}}
                        <div class="row">
                            <div class="col-md-6">
                                {{-- Cuenta por Cobrar --}}
                                <div class="form-group">
                                    <label for="create_cuentaxpagar_id" class="font-weight-bold text-secondary">
                                        Concepto de Cobro <span class="text-danger">*</span>
                                    </label>
                                    
                                    {{-- Lista dinámica según el tipo --}}
                                    @if($create_type == 'GENERAL')
                                        <select class="form-control form-control-sm" id="create_cuentaxpagar_id" 
                                                wire:model="create_cuentaxpagar_id" 
                                                {{ empty($create_type) ? 'disabled' : '' }} required>
                                            <option value="">-- Seleccione una cuenta GENERAL --</option>
                                            @foreach ($list_cuentaxpagar_general as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($create_type == 'INDIVIDUAL')
                                        <select class="form-control form-control-sm" id="create_cuentaxpagar_id" 
                                                wire:model="create_cuentaxpagar_id" 
                                                {{ empty($create_type) ? 'disabled' : '' }} required>
                                            <option value="">-- Seleccione una cuenta INDIVIDUAL --</option>
                                            @foreach ($list_cuentaxpagar_individual as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select class="form-control form-control-sm" disabled>
                                            <option value="">-- Primero seleccione el tipo de cuenta --</option>
                                        </select>
                                    @endif
                                    
                                    @error('create_cuentaxpagar_id') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="create_nom_concepto_pago_id" class="font-weight-bold text-secondary">
                                        Concepto de Pago <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="create_nom_concepto_pago_id" 
                                            wire:model="create_nom_concepto_pago_id" required>
                                        <option value="">-- Seleccione un concepto --</option>
                                        @foreach ($list_nom_concepto_pago as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('create_nom_concepto_pago_id') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="create_exchange_ammount" class="font-weight-bold text-secondary">
                                        Monto USD <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control form-control-sm" id="create_exchange_ammount" 
                                           wire:model="create_exchange_ammount" required
                                           placeholder="0.00" min="0.01">
                                    @error('create_exchange_ammount') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="create_status_active" class="font-weight-bold text-secondary">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="create_status_active" 
                                            wire:model="create_status_active" required>
                                        <option value="true">Activo</option>
                                        <option value="false">Inactivo</option>
                                    </select>
                                    @error('create_status_active') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>

                                {{-- Indicador visual del tipo seleccionado --}}
                                @if($create_type)
                                    <div class="card border-{{ $create_type == 'GENERAL' ? 'primary' : 'info' }} mb-3">
                                        <div class="card-header bg-{{ $create_type == 'GENERAL' ? 'primary' : 'info' }} text-white py-2">
                                            <h6 class="mb-0 small">
                                                <i class="fas fa-{{ $create_type == 'GENERAL' ? 'users' : 'user' }} mr-1"></i>
                                                {{ $create_type == 'GENERAL' ? 'CUENTA GENERAL' : 'CUENTA INDIVIDUAL' }}
                                            </h6>
                                        </div>
                                        <div class="card-body py-2">
                                            <p class="small mb-0">
                                                @if($create_type == 'GENERAL')
                                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                                    Aplicable a <strong>todos los estudiantes</strong> del plan de pago.
                                                @else
                                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                                    Específica para un <strong>estudiante individual</strong>.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="create_status_discount"
                                               wire:model="create_status_discount" value="true">
                                        <label class="custom-control-label font-weight-bold text-secondary" 
                                               for="create_status_discount">
                                            Permite Descuento
                                        </label>
                                    </div>
                                    @error('create_status_discount') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="create_status_annual"
                                               wire:model="create_status_annual" value="true">
                                        <label class="custom-control-label font-weight-bold text-secondary" 
                                               for="create_status_annual">
                                            Anualidad
                                        </label>
                                    </div>
                                    @error('create_status_annual') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="create_concepto_description" class="font-weight-bold text-secondary">
                                        Descripción <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-sm" id="create_concepto_description" 
                                              wire:model="create_concepto_description" rows="3" required
                                              placeholder="Descripción detallada del concepto..."></textarea>
                                    @error('create_concepto_description') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="create_concepto_observations" class="font-weight-bold text-secondary">
                                        Observaciones
                                    </label>
                                    <textarea class="form-control form-control-sm" id="create_concepto_observations" 
                                              wire:model="create_concepto_observations" rows="2"
                                              placeholder="Observaciones adicionales..."></textarea>
                                    @error('create_concepto_observations') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeCreateModal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success btn-sm" wire:click="store" 
                            wire:loading.attr="disabled"
                            {{ empty($create_type) ? 'disabled' : '' }}>
                        <i class="fas fa-save"></i> 
                        <span wire:loading.remove>Crear Concepto</span>
                        <span wire:loading>Creando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DE EDICIÓN --}}
    @if($showEditModal && $selectedConcepto)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Editar Concepto de Pago
                        <small class="d-block">ID: {{ $selectedConcepto->id }}</small>
                    </h5>
                    <button type="button" class="close text-white" wire:click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        
                        {{-- Información de la Cuenta (Solo Lectura) --}}
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    Información de la Cuenta Asociada
                                </h6>
                            </div>
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="font-weight-bold text-secondary small">Nombre de la Cuenta</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded small">
                                                <i class="fas fa-tag text-primary mr-1"></i>
                                                {{ $selectedCuentaInfo['name'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="font-weight-bold text-secondary small">Plan de Pago</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded small">
                                                <i class="fas fa-clipboard-list text-primary mr-1"></i>
                                                {{ $selectedCuentaInfo['planpago_name'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="font-weight-bold text-secondary small">Fecha de Vencimiento</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded small">
                                                <i class="fas fa-calendar-day text-primary mr-1"></i>
                                                {{ $selectedCuentaInfo['date_expiration'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="font-weight-bold text-secondary small">Tipo de Cuenta</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded small">
                                                @if($selectedCuentaInfo['type'] == 'INDIVIDUAL')
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-user mr-1"></i> INDIVIDUAL
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-users mr-1"></i> GENERAL
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Campos Editables --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_nom_concepto_pago_id" class="font-weight-bold text-secondary">
                                        Concepto de Pago <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="edit_nom_concepto_pago_id" 
                                            wire:model="editForm.nom_concepto_pago_id" required>
                                        <option value="">Seleccione un concepto</option>
                                        @foreach ($list_nom_concepto_pago as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editForm.nom_concepto_pago_id') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="edit_exchange_ammount" class="font-weight-bold text-secondary">
                                        Monto USD <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control form-control-sm" id="edit_exchange_ammount" 
                                           wire:model="editForm.exchange_ammount" required
                                           placeholder="0.00">
                                    @error('editForm.exchange_ammount') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_status_active" class="font-weight-bold text-secondary">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="edit_status_active" 
                                            wire:model="editForm.status_active" required>
                                        <option value="true">Activo</option>
                                        <option value="false">Inactivo</option>
                                    </select>
                                    @error('editForm.status_active') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_concepto_description" class="font-weight-bold text-secondary">
                                        Descripción <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-sm" id="edit_concepto_description" 
                                              wire:model="editForm.concepto_description" rows="3" required
                                              placeholder="Descripción detallada del concepto..."></textarea>
                                    @error('editForm.concepto_description') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_concepto_observations" class="font-weight-bold text-secondary">
                                        Observaciones
                                    </label>
                                    <textarea class="form-control form-control-sm" id="edit_concepto_observations" 
                                              wire:model="editForm.concepto_observations" rows="2"
                                              placeholder="Observaciones adicionales..."></textarea>
                                    @error('editForm.concepto_observations') 
                                        <span class="text-danger small">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Información de Auditoría --}}
                        <div class="alert alert-secondary py-2 small mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-calendar-plus"></i> Creado:</strong> 
                                    {{ $selectedConcepto->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-calendar-check"></i> Actualizado:</strong> 
                                    {{ $selectedConcepto->updated_at->format('d/m/Y H:i') }}
                                </div>
                                @if($selectedConcepto->conceptocancelados->count())
                                <div class="col-md-12 mt-1">
                                    <strong><i class="fas fa-link"></i> Pagos asociados:</strong> 
                                    {{ $selectedConcepto->conceptocancelados->count() }} registro(s)
                                </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" wire:click="update" wire:loading.attr="disabled">
                        <i class="fas fa-save"></i> 
                        <span wire:loading.remove>Actualizar</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @section('livewireCustomeScripts')
        @parent
        <script>
            // Cerrar modales con tecla ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (@this.showCreateModal) {
                        @this.closeCreateModal();
                    }
                    if (@this.showEditModal) {
                        @this.closeModal();
                    }
                }
            });

            // Prevenir envío del formulario con Enter fuera de los campos
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'SELECT') {
                    e.preventDefault();
                }
            });

            // Escuchar cuando cambia el tipo de cuenta
            document.addEventListener('DOMContentLoaded', function() {
                Livewire.on('concepto-type-changed', (data) => {
                    // Mostrar notificación toast (opcional)
                    if (data.type === 'GENERAL') {
                        console.log('Cambiado a cuenta GENERAL');
                        // Aquí puedes agregar animaciones o efectos visuales
                    } else if (data.type === 'INDIVIDUAL') {
                        console.log('Cambiado a cuenta INDIVIDUAL');
                        // Aquí puedes agregar animaciones o efectos visuales
                    }
                });
            });

            // También puedes escuchar directamente los cambios de Livewire
            document.addEventListener('livewire:load', function() {
                // El método updatedCreateFormType ya maneja la lógica principal
                // Este script es solo para efectos visuales adicionales
            });
        </script>
    @endsection
</div>