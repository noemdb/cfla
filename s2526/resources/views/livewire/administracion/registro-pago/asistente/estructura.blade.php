<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-magic"></i> Asistente para Generar Estructura de Cobranza
            </h5>
        </div>

        <div class="card-body">
            {{-- Indicador de Pasos --}}
            {{-- Barra de Progreso Simple --}}
            <div class="progress mb-4" style="height: 20px;">
                @php
                    $porcentaje = (($currentStep - 1) / ($totalSteps - 1)) * 100;
                @endphp

                <div class="progress-bar progress-bar-striped progress-bar-animated
                {{ $modo === 'edit' ? 'bg-warning' : 'bg-primary' }}"
                    role="progressbar" style="width: {{ $porcentaje }}%;" aria-valuenow="{{ $porcentaje }}"
                    aria-valuemin="0" aria-valuemax="100">
                    <strong>Paso {{ $currentStep }} de {{ $totalSteps }}</strong>
                </div>
            </div>

            {{-- Información de progreso --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        <strong>Progreso: {{ number_format($porcentaje, 0) }}% completado</strong>
                    </small>
                </div>
                <div class="col-md-6 text-right">
                    <small class="text-muted">
                        <i class="fas fa-clock"></i>
                        {{ now()->format('d/m/Y H:i:s') }}
                    </small>
                </div>
            </div>

            {{-- PASO 1: Seleccionar Modo --}}
            @if ($currentStep == 1)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-cogs text-primary"></i>
                        Paso 1: Seleccionar Modo de Operación
                    </h4>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Seleccione si desea crear una nueva estructura de cobranza o editar una existente.
                    </div>

                    <div class="row">
                        {{-- Card Crear Nueva --}}
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-primary" style="cursor: pointer;"
                                wire:click="selectModo('create')">
                                <div class="card-body text-center p-5">
                                    <div class="mb-4">
                                        <i class="fas fa-plus-circle text-primary" style="font-size: 64px;"></i>
                                    </div>
                                    <h4 class="text-primary mb-3">Crear Nueva Estructura</h4>
                                    <p class="text-muted mb-4">
                                        Iniciar el proceso de creación de una estructura de cobranza completamente nueva
                                        desde cero.
                                    </p>
                                    <div class="badge badge-primary badge-pill p-2">
                                        <i class="fas fa-rocket"></i> Comenzar Nuevo
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Editar Existente --}}
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-warning">
                                <div class="card-body text-center p-5">
                                    <div class="mb-4">
                                        <i class="fas fa-edit text-warning" style="font-size: 64px;"></i>
                                    </div>
                                    <h4 class="text-warning mb-3">Editar Estructura Existente</h4>
                                    <p class="text-muted mb-4">
                                        Modificar una estructura de cobranza existente. Seleccione el plan de pago a
                                        editar.
                                    </p>

                                    <div class="form-group text-left">
                                        <label for="planpago_seleccionado_id" class="font-weight-bold">
                                            <i class="fas fa-list"></i> Seleccionar Plan de Pago:
                                        </label>
                                        <select readonly
                                            class="form-control @error('planpago_seleccionado_id') is-invalid @enderror "
                                            id="planpago_seleccionado_id" wire:model="planpago_seleccionado_id">
                                            <option value="">-- Seleccione un plan --</option>
                                            @foreach ($planpagos_existentes as $plan)
                                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('planpago_seleccionado_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button disabled type="button" class="btn btn-warning btn-lg mt-3"
                                        wire:click="selectPlanpagoExistente" wire:loading.attr="disabled"
                                        {{ !$planpago_seleccionado_id ? 'disabled' : '' }}>
                                        <span wire:loading.remove wire:target="selectPlanpagoExistente">
                                            <i class="fas fa-edit"></i> Editar Estructura
                                        </span>
                                        <span wire:loading wire:target="selectPlanpagoExistente">
                                            <i class="fas fa-spinner fa-spin"></i> Cargando...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Tip:</strong> En modo "Crear Nueva", se generarán automáticamente 13 cuentas
                            predeterminadas.
                        </small>
                    </div>
                </div>
            @endif

            {{-- PASO 2: Plan de Pago --}}
            @if ($currentStep == 2)
                <div class="step-content">
                    <h4 class="mb-4 d-flex">
                        <div class="flex-grow-1">
                            <i class="fas fa-clipboard-list text-primary"></i>
                            Paso 2: Información del Plan de Pago
                        </div>
                        <div>
                            @if ($modo === 'edit')
                                <span class="badge badge-warning ml-2">
                                    <i class="fas fa-edit"></i> Modo Edición
                                </span>
                            @else
                                <span class="badge badge-primary ml-2">
                                    <i class="fas fa-plus"></i> Modo Creación
                                </span>
                            @endif
                        </div>
                    </h4>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Complete la información básica del plan de pago y configure sus parámetros principales.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="planpago_name">
                                    <i class="fas fa-tag"></i> Nombre del Plan de Pago <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('planpago_name') is-invalid @enderror"
                                    id="planpago_name" wire:model.defer="planpago_name" placeholder="Ej: ÚNICO OCTUBRE">
                                @error('planpago_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="planpago_description">
                                    <i class="fas fa-align-left"></i> Descripción <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('planpago_description') is-invalid @enderror"
                                    id="planpago_description" wire:model.defer="planpago_description"
                                    placeholder="Descripción breve del plan">
                                @error('planpago_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="planpago_observations">
                                    <i class="fas fa-sticky-note"></i> Observaciones
                                </label>
                                <textarea class="form-control" id="planpago_observations" wire:model.defer="planpago_observations" rows="3"
                                    placeholder="Observaciones adicionales (opcional)"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Configuraciones del Plan --}}
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-cog text-primary"></i>
                                Configuraciones del Plan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="status_active"
                                            wire:model="status_active" true-value="true" false-value="false">
                                        <label class="form-check-label font-weight-bold" for="status_active">
                                            <i class="fas fa-power-off text-success"></i> Plan Activo
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            El plan estará disponible para asignar a estudiantes
                                        </small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input"
                                            id="enabled_for_administrative" wire:model="enabled_for_administrative"
                                            true-value="true" false-value="false">
                                        <label class="form-check-label font-weight-bold"
                                            for="enabled_for_administrative">
                                            <i class="fas fa-user-shield text-info"></i> Para Inscripción
                                            Administrativa
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Visible en procesos de inscripción administrativa
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="status_cancel"
                                            wire:model="status_cancel" true-value="true" false-value="false">
                                        <label class="form-check-label font-weight-bold" for="status_cancel">
                                            <i class="fas fa-ban text-warning"></i> Permite Anulación
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Permite la anulación de pagos en fechas posteriores
                                        </small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input"
                                            id="status_inscription_affects" wire:model="status_inscription_affects"
                                            true-value="true" false-value="false">
                                        <label class="form-check-label font-weight-bold"
                                            for="status_inscription_affects">
                                            <i class="fas fa-user-graduate text-primary"></i> Contabiliza Inscripción
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Considera este plan para efectos de inscripción
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="status_inscriptions"
                                            wire:model="status_inscriptions" true-value="true" false-value="false">
                                        <label class="form-check-label font-weight-bold" for="status_inscriptions">
                                            <i class="fas fa-clipboard-check text-info"></i> Para Inscripción
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Considerado para procesos de inscripción
                                        </small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="status_foreign_currency"
                                            wire:model="status_foreign_currency" true-value="true"
                                            false-value="false">
                                        <label class="form-check-label font-weight-bold"
                                            for="status_foreign_currency">
                                            <i class="fas fa-dollar-sign text-success"></i> Moneda de Referencia
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Montos vinculados a moneda foránea
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" wire:click="nextStepTwo">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- PASO 3: Cuentas por Pagar --}}
            @if ($currentStep == 3)
                <div class="step-content">
                    <h4 class="mb-4 d-flex">
                        <div class="flex-grow-1">
                            <i class="fas fa-file-invoice-dollar text-primary"></i>
                            Paso 3: Definir Cuentas por Pagar
                        </div>
                        <div>

                            @if ($modo === 'create')
                                <span class="text-success ml-2">
                                    13 cuentas generadas automáticamente
                                </span>
                            @endif
                        </div>
                    </h4>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Las cuentas por pagar representan los períodos o conceptos que el estudiante debe cancelar.
                        @if ($modo === 'create')
                            <strong>Se han generado automáticamente 13 cuentas predeterminadas. Agregue o elimine según su necesidad.</strong>
                        @else
                            <strong>Modo edición: puede modificar las cuentas existentes.</strong>
                        @endif
                    </div>

                    @if (count($cuentas) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="20%">Nombre</th>
                                        <th width="12%">Vencimiento</th>
                                        <th width="12%">Recargo</th>
                                        <th width="12%">Inicio Calendario</th>
                                        <th width="12%">Fin Calendario</th>
                                        <th width="10%" class="text-center">Inscripción</th>
                                        <th width="10%" class="text-center">Morosidad</th>
                                        <th width="12%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cuentas as $index => $cuenta)
                                        <tr>
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm @error('cuentas.' . $index . '.name') is-invalid @enderror"
                                                    wire:model.defer="cuentas.{{ $index }}.name"
                                                    placeholder="Nombre de cuenta">
                                                @error('cuentas.' . $index . '.name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="date"
                                                    class="form-control form-control-sm @error('cuentas.' . $index . '.date_expiration') is-invalid @enderror"
                                                    wire:model.defer="cuentas.{{ $index }}.date_expiration">
                                                @error('cuentas.' . $index . '.date_expiration')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm"
                                                    wire:model.defer="cuentas.{{ $index }}.date_late_payment">
                                            </td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm"
                                                    wire:model.defer="cuentas.{{ $index }}.date_calendar_start">
                                            </td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm"
                                                    wire:model.defer="cuentas.{{ $index }}.date_calendar_end">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input type="checkbox" class="form-check-input"
                                                        wire:model="cuentas.{{ $index }}.status_inscription"
                                                        true-value="true" false-value="false">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input type="checkbox" class="form-check-input"
                                                        wire:model="cuentas.{{ $index }}.enable_late_payment">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="removeCuenta({{ $index }})"
                                                    title="Eliminar cuenta">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning text-center py-5">
                            <i class="fas fa-folder-open" style="font-size: 48px;"></i>
                            <h5 class="mt-3">No hay cuentas por pagar definidas</h5>
                            <p>Comience agregando una cuenta por pagar para continuar.</p>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="button" class="btn btn-success" wire:click="addCuenta">
                            <i class="fas fa-plus-circle"></i> Agregar Cuenta
                        </button>

                        <div class="text-muted">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Total de cuentas: <strong>{{ count($cuentas) }}</strong>
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" wire:click="nextStepThree">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- PASO 4: Gestión de Conceptos --}}
            @if ($currentStep == 4)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-list-alt text-primary"></i>
                        Paso 4: Gestión de Conceptos de Pago
                    </h4>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Asigne los conceptos de pago para cada cuenta. Cada cuenta debe tener al menos un concepto.
                    </div>

                    @foreach ($cuentas as $index => $cuenta)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-folder-open text-warning"></i>
                                    {{ $cuenta['name'] }}
                                    <span class="badge badge-primary ml-2">
                                        Vence: {{ \Carbon\Carbon::parse($cuenta['date_expiration'])->format('d/m/Y') }}
                                    </span>
                                </h6>
                                <button type="button" class="btn btn-sm btn-success"
                                    wire:click="addConcepto({{ $index }})">
                                    <i class="fas fa-plus"></i> Agregar Concepto
                                </button>
                            </div>
                            <div class="card-body">
                                @if (isset($cuenta['conceptos']) && count($cuenta['conceptos']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="25%">Tipo de Concepto</th>
                                                    <th width="25%">Descripción</th>
                                                    <th width="15%">Monto</th>
                                                    <th width="10%" class="text-center">Descuento</th>
                                                    <th width="10%" class="text-center">Anualidad</th>
                                                    <th width="15%" class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $subtotalCuenta = 0; @endphp
                                                @foreach ($cuenta['conceptos'] as $cIndex => $concepto)
                                                    @php $subtotalCuenta += $concepto['exchange_ammount']; @endphp
                                                    <tr>
                                                        <td>
                                                            <select
                                                                class="form-control form-control-sm @error('cuentas.' . $index . '.conceptos.' . $cIndex . '.nom_concepto_pago_id') is-invalid @enderror"
                                                                wire:model.defer="cuentas.{{ $index }}.conceptos.{{ $cIndex }}.nom_concepto_pago_id">
                                                                <option value="">Seleccione...</option>
                                                                @foreach ($conceptos_disponibles as $concepto_disp)
                                                                    <option value="{{ $concepto_disp->id }}">
                                                                        {{ $concepto_disp->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('cuentas.' . $index . '.conceptos.' . $cIndex .
                                                                '.nom_concepto_pago_id')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model.defer="cuentas.{{ $index }}.conceptos.{{ $cIndex }}.concepto_description"
                                                                placeholder="Descripción opcional">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0"
                                                                class="form-control form-control-sm @error('cuentas.' . $index . '.conceptos.' . $cIndex . '.exchange_ammount') is-invalid @enderror"
                                                                wire:model.defer="cuentas.{{ $index }}.conceptos.{{ $cIndex }}.exchange_ammount"
                                                                wire:change="updateConceptoAmmount({{ $index }}, {{ $cIndex }})"
                                                                placeholder="0.00">
                                                            @error('cuentas.' . $index . '.conceptos.' . $cIndex .
                                                                '.exchange_ammount')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-inline-block py-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    wire:model="cuentas.{{ $index }}.conceptos.{{ $cIndex }}.status_discount"
                                                                    true-value="true" false-value="false">
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check d-inline-block py-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    wire:model="cuentas.{{ $index }}.conceptos.{{ $cIndex }}.status_annuity"
                                                                    true-value="true" false-value="false">
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                wire:click="removeConcepto({{ $index }}, {{ $cIndex }})"
                                                                title="Eliminar concepto">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light font-weight-bold">
                                                    <td colspan="2" class="text-right">Subtotal de la cuenta:</td>
                                                    <td class="text-primary">${{ number_format($subtotalCuenta, 2) }}
                                                    </td>
                                                    <td colspan="3"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning text-center">
                                        <i class="fas fa-exclamation-circle"></i>
                                        No hay conceptos agregados para esta cuenta. Agregue al menos uno.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" wire:click="nextStepFour">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- PASO 5: Vista Previa --}}
            @if ($currentStep == 5)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-clipboard-check text-primary"></i>
                        Paso 5: Vista Previa
                    </h4>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Revise cuidadosamente toda la información antes de guardar la estructura.
                    </div>

                    {{-- Resumen del Plan de Pago --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-contract"></i>
                                Información del Plan de Pago
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong><i class="fas fa-tag"></i> Nombre:</strong>
                                        <span class="text-primary">{{ $resumen['planpago']['name'] }}</span>
                                    </p>
                                    <p>
                                        <strong><i class="fas fa-align-left"></i> Descripción:</strong>
                                        {{ $resumen['planpago']['description'] }}
                                    </p>
                                    <p>
                                        <strong><i class="fas fa-sticky-note"></i> Observaciones:</strong>
                                        {{ $resumen['planpago']['observations'] ?: 'Sin observaciones' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong><i class="fas fa-cog"></i> Configuraciones:</strong>
                                    </p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span
                                                class="badge badge-{{ $resumen['planpago']['configuraciones']['status_active'] == 'true' ? 'success' : 'secondary' }} mb-1">
                                                <i class="fas fa-power-off"></i>
                                                {{ $resumen['planpago']['configuraciones']['status_active'] == 'true' ? 'Activo' : 'Inactivo' }}
                                            </span><br>
                                            <span
                                                class="badge badge-{{ $resumen['planpago']['configuraciones']['enabled_for_administrative'] == 'true' ? 'info' : 'secondary' }} mb-1">
                                                <i class="fas fa-user-shield"></i>
                                                {{ $resumen['planpago']['configuraciones']['enabled_for_administrative'] == 'true' ? 'Para Inscripción' : 'No Inscripción' }}
                                            </span><br>
                                            <span
                                                class="badge badge-{{ $resumen['planpago']['configuraciones']['status_cancel'] == 'true' ? 'warning' : 'secondary' }} mb-1">
                                                <i class="fas fa-ban"></i>
                                                {{ $resumen['planpago']['configuraciones']['status_cancel'] == 'true' ? 'Permite Anulación' : 'No Anulación' }}
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <span
                                                class="badge badge-{{ $resumen['planpago']['configuraciones']['status_inscription_affects'] == 'true' ? 'primary' : 'secondary' }} mb-1">
                                                <i class="fas fa-user-graduate"></i>
                                                {{ $resumen['planpago']['configuraciones']['status_inscription_affects'] == 'true' ? 'Contabiliza' : 'No Contabiliza' }}
                                            </span><br>
                                            <span
                                                class="badge badge-{{ $resumen['planpago']['configuraciones']['status_inscriptions'] == 'true' ? 'info' : 'secondary' }} mb-1">
                                                <i class="fas fa-clipboard-check"></i>
                                                {{ $resumen['planpago']['configuraciones']['status_inscriptions'] == 'true' ? 'Para Inscripción' : 'No Inscripción' }}
                                            </span><br>
                                            <span
                                                class="badge badge-{{ $resumen['planpago']['configuraciones']['status_foreign_currency'] == 'true' ? 'success' : 'secondary' }} mb-1">
                                                <i class="fas fa-dollar-sign"></i>
                                                {{ $resumen['planpago']['configuraciones']['status_foreign_currency'] == 'true' ? 'Moneda Extranjera' : 'Moneda Local' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Resumen de Cuentas y Conceptos --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie"></i>
                                Estructura de Cobranza - Resumen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-info text-white text-center">
                                        <div class="card-body">
                                            <i class="fas fa-folder-open" style="font-size: 32px;"></i>
                                            <h6 class="mt-2">Total Cuentas</h6>
                                            <h2 class="mb-0">{{ $resumen['total_cuentas'] }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-warning text-white text-center">
                                        <div class="card-body">
                                            <i class="fas fa-list-alt" style="font-size: 32px;"></i>
                                            <h6 class="mt-2">Total Conceptos</h6>
                                            <h2 class="mb-0">{{ $resumen['total_conceptos'] }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white text-center">
                                        <div class="card-body">
                                            <i class="fas fa-dollar-sign" style="font-size: 32px;"></i>
                                            <h6 class="mt-2">Monto Total</h6>
                                            <h2 class="mb-0">${{ number_format($resumen['total_general'], 2) }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Detalle de cada cuenta --}}
                            <h6 class="mb-3">
                                <i class="fas fa-list"></i>
                                Detalle de Cuentas y Conceptos
                            </h6>
                            @foreach ($resumen['cuentas'] as $index => $cuenta)
                                <div class="card mb-3 border-left-primary">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong>
                                                    <i class="fas fa-folder text-warning"></i>
                                                    Cuenta {{ $index + 1 }}: {{ $cuenta['name'] }}
                                                </strong>
                                                @if ($cuenta['status_inscription'] == 'true')
                                                    <span class="badge badge-primary ml-2">
                                                        <i class="fas fa-user-graduate"></i> Inscripción
                                                    </span>
                                                @endif
                                                @if ($cuenta['enable_late_payment'])
                                                    <span class="badge badge-warning ml-2">
                                                        <i class="fas fa-exclamation-triangle"></i> Morosidad
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fas fa-calendar-alt"></i>
                                                Vence:
                                                {{ \Carbon\Carbon::parse($cuenta['date_expiration'])->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="50%">Concepto</th>
                                                        <th width="20%" class="text-right">Monto</th>
                                                        <th width="15%" class="text-center">Descuento</th>
                                                        <th width="15%" class="text-center">Anualidad</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cuenta['conceptos'] as $concepto)
                                                        <tr>
                                                            <td>
                                                                <i class="fas fa-check-circle text-success"></i>
                                                                {{ $concepto['nombre'] }}
                                                            </td>
                                                            <td class="text-right font-weight-bold">
                                                                ${{ number_format($concepto['monto'], 2) }}
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($concepto['permite_descuento'])
                                                                    <span class="badge badge-success">Sí</span>
                                                                @else
                                                                    <span class="badge badge-secondary">No</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($concepto['anualidad'])
                                                                    <span class="badge badge-info">Sí</span>
                                                                @else
                                                                    <span class="badge badge-secondary">No</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="bg-light">
                                                        <td class="text-right font-weight-bold">
                                                            <i class="fas fa-calculator"></i> Subtotal Cuenta:
                                                        </td>
                                                        <td class="text-right font-weight-bold text-primary">
                                                            ${{ number_format($cuenta['total'], 2) }}
                                                        </td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Total General --}}
                            <div class="card bg-success text-white mt-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-money-bill-wave"></i>
                                            MONTO TOTAL GENERAL:
                                        </h5>
                                        <h3 class="mb-0">
                                            ${{ number_format($resumen['total_general'], 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" wire:click="previousStep">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-success btn-lg" wire:click="nextStepFive"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="nextStepFive">
                                <i class="fas fa-save"></i>
                                {{ $modo === 'create' ? 'Crear Estructura' : 'Actualizar Estructura' }}
                            </span>
                            <span wire:loading wire:target="nextStepFive">
                                <i class="fas fa-spinner fa-spin"></i> Procesando...
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- PASO 6: Confirmación --}}
            @if ($currentStep == 6)
                <div class="step-content">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 100px;"></i>
                        </div>
                        <h2 class="text-success mb-3">
                            <i class="fas fa-trophy"></i>
                            ¡Estructura {{ $operacion_realizada === 'creada' ? 'Creada' : 'Actualizada' }}
                            Exitosamente!
                        </h2>
                        <p class="lead mb-4">
                            La estructura de cobranza ha sido {{ $operacion_realizada }} correctamente en el sistema.
                        </p>

                        @if ($planpago_creado_id)
                            <div class="alert alert-info d-inline-block mb-4">
                                <i class="fas fa-info-circle"></i>
                                <strong>ID del Plan de Pago:</strong>
                                <span class="badge badge-primary badge-pill" style="font-size: 16px;">
                                    {{ $planpago_creado_id }}
                                </span>
                            </div>
                        @endif

                        {{-- Resumen Rápido --}}
                        <div class="row mb-4 justify-content-center">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>
                                            <i class="fas fa-folder text-warning"></i>
                                            Cuentas
                                        </h5>
                                        <h3 class="text-primary">{{ $resumen['total_cuentas'] ?? 0 }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>
                                            <i class="fas fa-list-alt text-info"></i>
                                            Conceptos
                                        </h5>
                                        <h3 class="text-primary">{{ $resumen['total_conceptos'] ?? 0 }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>
                                            <i class="fas fa-dollar-sign text-success"></i>
                                            Total
                                        </h5>
                                        <h3 class="text-primary">
                                            ${{ number_format($resumen['total_general'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <button type="button" class="btn btn-primary btn-lg mr-2" wire:click="resetWizard">
                                <i class="fas fa-plus-circle"></i>
                                {{ $modo === 'create' ? 'Crear Otra Estructura' : 'Editar Otra Estructura' }}
                            </button>
                            <a href="{{ route('administracion.registropagos.asistent.estructura.create') }}"
                                class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-home"></i> Ir al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Footer de la Card con Información Adicional --}}
        <div class="card-footer bg-light text-muted">
            <div class="row">
                <div class="col-md-6">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        <strong>Paso {{ $currentStep }} de {{ $totalSteps }}</strong>
                        @if ($modo)
                            | Modo: <span class="badge badge-{{ $modo === 'create' ? 'primary' : 'warning' }}">
                                {{ $modo === 'create' ? 'Creación' : 'Edición' }}
                            </span>
                        @endif
                    </small>
                </div>
                <div class="col-md-6 text-right">
                    <small>
                        <i class="fas fa-clock"></i>
                        {{ now()->format('d/m/Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div wire:loading.flex class="position-fixed top-0 right-0 m-4" style="z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>

    {{-- Loading Overlay --}}
    {{-- <div wire:loading.flex class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center"
        style="top: 0; left: 0; background: rgba(0,0,0,0.7); z-index: 9999;">
        <div class="text-center">
            <div class="spinner-border text-light mb-3" role="status"
                style="width: 4rem; height: 4rem; border-width: 0.4rem;">
                <span class="sr-only">Cargando...</span>
            </div>
            <h5 class="text-white">
                <i class="fas fa-cog fa-spin"></i> Procesando...
            </h5>
        </div>
    </div> --}}
</div>

@section('stylesheet')
    @parent
    <style>
        .step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background-color: #dee2e6;
            z-index: 1;
        }

        .step.active:not(:last-child)::after {
            background-color: #007bff;
        }

        .step.completed:not(:last-child)::after {
            background-color: #28a745;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }

        .step.active .step-circle {
            background-color: #007bff;
            color: white;
        }

        .step.completed .step-circle {
            background-color: #28a745;
            color: white;
        }

        .step.completed .step-circle::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
        }

        .step-label {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            text-align: center;
            color: #6c757d;
        }

        .step.active .step-label {
            color: #007bff;
            font-weight: bold;
        }

        .step.completed .step-label {
            color: #28a745;
        }

        .step-content {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .step-label {
                font-size: 0.75rem;
            }

            .step-circle {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }

            .btn-lg {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>
@endsection
