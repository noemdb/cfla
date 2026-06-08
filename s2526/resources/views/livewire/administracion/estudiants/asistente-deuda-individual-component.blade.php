<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-plus"></i> Asistente para Crear Deuda Individual
            </h5>
        </div>
        <div class="card-body">
            {{-- Barra de Progreso --}}
            <div class="progress mb-4" style="height: 20px;">
                @php
                    $porcentaje = (($currentStep - 1) / ($totalSteps - 1)) * 100;
                @endphp
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                    style="width: {{ $porcentaje }}%;" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0"
                    aria-valuemax="100">
                    <strong>Paso {{ $currentStep }} de {{ $totalSteps }}</strong>
                </div>
            </div>
            {{-- PASO 1: Selección de Estudiante --}}
            @if ($currentStep == 1)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-search text-primary"></i>
                        Paso 1: Seleccionar Estudiante
                    </h4>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Busque y seleccione el estudiante al que desea asignar una deuda individual.
                    </div>
                    {{-- Búsqueda de Estudiantes --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="estudianteSearch" class="font-weight-bold">
                                    <i class="fas fa-user-graduate"></i> Buscar Estudiante
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="estudianteSearch"
                                        wire:model.debounce.500ms="estudianteSearch"
                                        placeholder="Buscar por cédula o nombre...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            wire:click="limpiarBusquedaEstudiante">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Loading --}}
                    <div wire:loading wire:target="estudianteSearch" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Buscando...</span>
                        </div>
                        <small class="text-muted ml-2">Buscando estudiantes...</small>
                    </div>
                    {{-- Resultados de Búsqueda --}}
                    @if ($estudianteSearch && count($searchEstudiantes) > 0)
                        <div class="search-results mt-3 border rounded shadow-sm">
                            @foreach ($searchEstudiantes as $estudiante)
                                <div class="search-result-item p-3 border-bottom cursor-pointer"
                                    wire:click="seleccionarEstudiante({{ $estudiante['id'] }})"
                                    style="cursor: pointer;">
                                    <div class="font-weight-bold text-primary">
                                        {{ $estudiante['ci_estudiant'] }} - {{ $estudiante['name'] }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-user-tie"></i> {{ $estudiante['representante'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($estudianteSearch && empty($searchEstudiantes))
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            No se encontraron estudiantes con los criterios de búsqueda.
                        </div>
                    @endif
                    {{-- Estudiante Seleccionado --}}
                    @if ($estudianteSeleccionado && !empty($estudianteSeleccionado['ci_estudiant']))
                        <div class="card border-success mt-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle"></i> Estudiante Seleccionado
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Cédula:</strong>
                                            {{ $estudianteSeleccionado['ci_estudiant'] ?? 'N/A' }}</p>
                                        <p><strong>Nombre:</strong> {{ $estudianteSeleccionado['name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Email:</strong> {{ $estudianteSeleccionado['email'] ?? 'N/A' }}</p>
                                        <p><strong>Teléfono:</strong> {{ $estudianteSeleccionado['phone'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-warning btn-sm"
                                    wire:click="removerEstudianteSeleccionado">
                                    <i class="fas fa-times"></i> Cambiar Estudiante
                                </button>
                            </div>
                        </div>
                    @endif
                    {{-- Navegación --}}
                    <div class="d-flex justify-content-between mt-4">
                        <div></div>
                        {{-- Espacio para alinear --}}
                        <button type="button" class="btn btn-primary btn-lg" wire:click="siguientePaso"
                            {{ !$estudianteSeleccionado ? 'disabled' : '' }}>
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            @endif
            {{-- PASO 2: Selección de Plan de Pago --}}
            @if ($currentStep == 2)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-clipboard-list text-primary"></i>
                        Paso 2: Seleccionar Plan de Pago
                    </h4>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Seleccione el plan de pago al que pertenecerá la deuda individual.
                    </div>
                    {{-- Información del Estudiante --}}
                    @if ($estudianteSeleccionado)
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-graduate"></i>
                                    Estudiante: {{ $estudianteSeleccionado['ci_estudiant'] }} -
                                    {{ $estudianteSeleccionado['name'] }}
                                </h6>
                            </div>
                        </div>
                    @endif
                    {{-- Lista de Planes de Pago --}}
                    <div class="row">
                        @foreach ($planpagosDisponibles as $id => $nombre)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-primary 
                     {{ $planpagoId == $id ? 'border-3 bg-light' : '' }}"
                                    style="cursor: pointer;" wire:click="seleccionarPlanPago({{ $id }})">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-primary">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                            {{ $nombre }}
                                        </h5>
                                        @if ($planpagoId == $id)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Seleccionado
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Navegación --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" wire:click="pasoAnterior">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" wire:click="siguientePaso"
                            {{ !$planpagoId ? 'disabled' : '' }}>
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            @endif
            {{-- PASO 3: Información del Estudiante --}}
            @if ($currentStep == 3)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-user-circle text-primary"></i>
                        Paso 3: Información del Estudiante
                    </h4>
                    {{-- Validación de datos requeridos --}}
                    @if (empty($estudianteSeleccionado) || !isset($estudianteSeleccionado['ci_estudiant']))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Error:</strong> No hay estudiante seleccionado.
                            <button type="button" class="btn btn-warning btn-sm ml-2" wire:click="irAPaso(1)">
                                <i class="fas fa-arrow-left"></i> Volver a seleccionar estudiante
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Revise la información del estudiante, sus deudas existentes y conceptos pendientes antes de
                            continuar.
                        </div>
                        {{-- Resumen de Selecciones Previas --}}
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-clipboard-check"></i> Resumen de Selecciones
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong><i class="fas fa-user-graduate"></i> Estudiante:</strong><br>
                                            {{ $estudianteSeleccionado['ci_estudiant'] }} -
                                            {{ $estudianteSeleccionado['name'] }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong><i class="fas fa-file-invoice-dollar"></i> Plan de
                                                Pago:</strong><br>
                                            {{ $planpagosDisponibles[$planpagoId] ?? 'No seleccionado' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Información General del Estudiante --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-id-card text-primary"></i> Datos Generales del Estudiante
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Cédula:</strong></td>
                                                <td>{{ $estudianteSeleccionado['ci_estudiant'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nombre Completo:</strong></td>
                                                <td>{{ $estudianteSeleccionado['name'] ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td><strong>Representante:</strong></td>
                                                <td>
                                                    @if (isset($estudianteSeleccionado['representante']))
                                                        {{ $estudianteSeleccionado['representante']['name'] ?? 'N/A' }}
                                                        ({{ $estudianteSeleccionado['representante']['ci_representant'] ?? 'N/A' }})
                                                    @else
                                                        No asignado
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40%"><strong>Estado en Sistema:</strong></td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ ($estudianteSeleccionado['status_active'] ?? '') == 'Activo' ? 'success' : 'secondary' }}">
                                                        {{ $estudianteSeleccionado['status_active'] ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Información Detallada del Estudiante --}}
                        @if (!empty($estudianteInfo) && isset($estudianteInfo['representante']))
                            {{-- Información del Representante --}}
                            @if ($estudianteInfo['representante'])
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-user-tie text-info"></i> Información del Representante
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td width="40%"><strong>Nombre:</strong></td>
                                                        <td>{{ $estudianteInfo['representante']['name'] ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Cédula:</strong></td>
                                                        <td>{{ $estudianteInfo['representante']['ci_representant'] ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Parentesco:</strong></td>
                                                        <td>{{ $estudianteInfo['representante']['relationship'] ?? 'No especificado' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td width="40%"><strong>Email:</strong></td>
                                                        <td>{{ $estudianteInfo['representante']['email'] ?? 'No registrado' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Teléfono:</strong></td>
                                                        <td>{{ $estudianteInfo['representante']['phone'] ?? 'No registrado' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Dirección:</strong></td>
                                                        <td>{{ $estudianteInfo['representante']['address'] ?? 'No registrado' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Este estudiante no tiene representante asignado en el sistema.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle"></i>
                                No se pudo cargar la información detallada del estudiante.
                            </div>
                        @endif
                        @php $deuda = number_format($deudaActualTotal ?? 0, 2) ; @endphp
                        @if ($deuda > 0)
                            {{-- Resumen de Deudas Existentes --}}
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-invoice-dollar text-warning"></i> Resumen de Deudas
                                        Existentes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if (!empty($deudasExistentes) && count($deudasExistentes) > 0)
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="card bg-primary text-white text-center">
                                                    <div class="card-body py-3">
                                                        <h6>Total Cuentas</h6>
                                                        <h3 class="mb-0">
                                                            {{ $conceptosPendientes['total_cuentas'] ?? 0 }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card bg-info text-white text-center">
                                                    <div class="card-body py-3">
                                                        <h6>Total Conceptos</h6>
                                                        <h3 class="mb-0">
                                                            {{ $conceptosPendientes['total_conceptos'] ?? 0 }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card bg-danger text-white text-center">
                                                    <div class="card-body py-3">
                                                        <h6>Monto Vencido</h6>
                                                        <h4 class="mb-0">
                                                            ${{ number_format($conceptosPendientes['monto_vencido'] ?? 0, 2) }}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card bg-success text-white text-center">
                                                    <div class="card-body py-3">
                                                        <h6>Deuda Total</h6>
                                                        <h4 class="mb-0">
                                                            ${{ number_format($deudaActualTotal ?? 0, 2) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Detalle de Cuentas Existentes --}}
                                        <h6 class="mb-3">
                                            <i class="fas fa-list"></i> Detalle de Cuentas por Pagar
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Cuenta</th>
                                                        <th>Plan de Pago</th>
                                                        <th>Vencimiento</th>
                                                        <th>Estado</th>
                                                        <th class="text-center">Conceptos</th>
                                                        <th class="text-right">Monto Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($deudasExistentes as $deuda)
                                                        <tr
                                                            class="{{ $deuda['vencida'] ?? false ? 'table-danger' : 'table-default' }}">
                                                            <td>
                                                                <strong>{{ $deuda['name'] ?? 'N/A' }}</strong>
                                                                @if (($deuda['status_bad'] ?? 'false') == 'true')
                                                                    <span
                                                                        class="badge badge-danger ml-1">Incobrable</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $deuda['planpago'] ?? 'N/A' }}</td>
                                                            <td>
                                                                {{ $deuda['date_expiration'] ?? 'N/A' }}
                                                                @if ($deuda['vencida'] ?? false)
                                                                    <br><small class="text-danger">Vencida</small>
                                                                @elseif (isset($deuda['dias_restantes']))
                                                                    <br><small
                                                                        class="text-success">{{ $deuda['dias_restantes'] }}
                                                                        días</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($deuda['vencida'] ?? false)
                                                                    <span class="badge badge-danger">Vencida</span>
                                                                @else
                                                                    <span class="badge badge-success">Vigente</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <span
                                                                    class="badge badge-secondary">{{ $deuda['total_conceptos'] ?? 0 }}</span>
                                                            </td>
                                                            <td class="text-right font-weight-bold">
                                                                ${{ number_format($deuda['monto_total'] ?? 0, 2) }}
                                                            </td>
                                                        </tr>
                                                        {{-- Detalle de Conceptos --}}
                                                        @if (isset($deuda['conceptos']) && count($deuda['conceptos']) > 0)
                                                            <tr class="bg-light">
                                                                <td colspan="6" class="p-2">
                                                                    <div class="pl-4">
                                                                        <strong class="small">Conceptos:</strong>
                                                                        <div class="row mt-1">
                                                                            @foreach ($deuda['conceptos'] as $concepto)
                                                                                <div class="col-md-6 mb-1">
                                                                                    <span
                                                                                        class="badge badge-light border">
                                                                                        {{ $concepto['nombre'] ?? 'N/A' }}:
                                                                                        ${{ number_format($concepto['monto'] ?? 0, 2) }}
                                                                                        @if ($concepto['permite_descuento'] ?? false)
                                                                                            <i class="fas fa-percentage text-success ml-1"
                                                                                                title="Permite descuento"></i>
                                                                                        @endif
                                                                                        @if ($concepto['anualidad'] ?? false)
                                                                                            <i class="fas fa-calendar-alt text-info ml-1"
                                                                                                title="Anualidad"></i>
                                                                                        @endif
                                                                                    </span>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-dark text-white">
                                                    <tr>
                                                        <td colspan="5" class="text-right"><strong>DEUDA TOTAL
                                                                ACTUAL:</strong></td>
                                                        <td class="text-right font-weight-bold">
                                                            ${{ number_format($deudaActualTotal ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-success text-center py-4">
                                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                                            <h5>¡Excelente!</h5>
                                            <p class="mb-0">El estudiante no tiene deudas individuales registradas en
                                                el sistema.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success font-weight-bold text-center">SOLVENTE</div>
                        @endif
                        {{-- Navegación --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" wire:click="pasoAnterior">
                                <i class="fas fa-arrow-left"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" wire:click="siguientePaso">
                                Continuar con Creación de Deuda <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
            {{-- PASO 4: Creación de Conceptos --}}
            @if ($currentStep == 4)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-plus-circle text-primary"></i>
                        Paso 4: Crear Conceptos de Deuda
                    </h4>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Defina los conceptos de pago para la nueva deuda individual del estudiante.
                    </div>
                    {{-- Resumen de Selecciones --}}
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-clipboard-check"></i> Resumen de Selecciones
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1">
                                        <strong><i class="fas fa-user-graduate"></i> Estudiante:</strong><br>
                                        {{ $estudianteSeleccionado['ci_estudiant'] }} -
                                        {{ $estudianteSeleccionado['name'] }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1">
                                        <strong><i class="fas fa-file-invoice-dollar"></i> Plan de Pago:</strong><br>
                                        {{ $planpagosDisponibles[$planpagoId] ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1">
                                        <strong><i class="fas fa-money-bill-wave"></i> Deuda Actual:</strong><br>
                                        ${{ number_format($deudaActualTotal ?? 0, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Información de la Nueva Cuenta --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-file-invoice text-warning"></i> Información del Nuevo Concepto de
                                Cobro
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nuevaCuenta_name" class="font-weight-bold">
                                            <i class="fas fa-tag"></i> Nombre del Concepto *
                                        </label>
                                        <input type="text"
                                            class="form-control @error('nuevaCuenta.name') is-invalid @enderror"
                                            id="nuevaCuenta_name" wire:model="nuevaCuenta.name"
                                            placeholder="Ej: Multa por material, Mora mensual...">
                                        @error('nuevaCuenta.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="nuevaCuenta_date_expiration" class="font-weight-bold">
                                            <i class="fas fa-calendar-day"></i> Fecha de Vencimiento *
                                        </label>
                                        <input type="date"
                                            class="form-control @error('nuevaCuenta.date_expiration') is-invalid @enderror"
                                            id="nuevaCuenta_date_expiration" wire:model="nuevaCuenta.date_expiration">
                                        @error('nuevaCuenta.date_expiration')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nuevaCuenta_description" class="font-weight-bold">
                                            <i class="fas fa-align-left"></i> Descripción
                                        </label>
                                        <textarea class="form-control" id="nuevaCuenta_description" wire:model="nuevaCuenta.description" rows="1"
                                            placeholder="Descripción detallada de la deuda..."></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="nuevaCuenta_status_bad" class="font-weight-bold">
                                            <i class="fas fa-exclamation-triangle"></i> Cuenta Incobrable
                                        </label>
                                        <select
                                            class="form-control form-control-sm @error('nuevaCuenta.status_bad') is-invalid @enderror"
                                            wire:model="nuevaCuenta.status_bad">
                                            <option value="">-- Seleccione Tipo --</option>
                                            <option value="true">Cuenta incobrable</option>
                                            <option value="false">Cuenta normal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Conceptos de Cobro --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list-alt text-info"></i> Cuenta(s) de Cobro
                                <span class="badge badge-primary ml-2">{{ count($nuevosConceptos) }}</span>
                            </h5>
                            <div>
                                <button type="button" class="btn btn-success btn-sm mr-2"
                                    wire:click="agregarConceptoVacio">
                                    <i class="fas fa-plus"></i> Agregar Cuenta
                                </button>
                                @if (count($nuevosConceptos) > 1)
                                    <button type="button" class="btn btn-warning btn-sm"
                                        wire:click="limpiarConceptos">
                                        <i class="fas fa-broom"></i> Limpiar Todo
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if (count($nuevosConceptos) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="25%">Tipo *</th>
                                                <th width="25%">Descripción</th>
                                                <th width="15%">Monto USD *</th>
                                                {{-- 
                              <th width="10%" class="text-center">Descuento</th>
                              --}}
                                                {{-- 
                              <th width="10%" class="text-center">Anualidad</th>
                              --}}
                                                <th width="15%" class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($nuevosConceptos as $index => $concepto)
                                                <tr>
                                                    <td>
                                                        <select
                                                            class="form-control form-control-sm @error('nuevosConceptos.' . $index . '.nom_concepto_pago_id') is-invalid @enderror"
                                                            wire:model="nuevosConceptos.{{ $index }}.nom_concepto_pago_id">
                                                            <option value="">-- Seleccione Tipo --</option>
                                                            @foreach ($conceptosDisponibles as $id => $nombre)
                                                                <option value="{{ $id }}">
                                                                    {{ $nombre }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('nuevosConceptos.' . $index . '.nom_concepto_pago_id')
                                                            <div class="invalid-feedback d-block">{{ $message }}
                                                            </div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            wire:model="nuevosConceptos.{{ $index }}.concepto_description"
                                                            placeholder="Descripción automática o personalizada">
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="number" step="0.01" min="0"
                                                                class="form-control form-control-sm @error('nuevosConceptos.' . $index . '.exchange_ammount') is-invalid @enderror"
                                                                wire:model="nuevosConceptos.{{ $index }}.exchange_ammount"
                                                                wire:keydown.debounce.500ms="calcularNuevaDeuda"
                                                                placeholder="0.00">
                                                            @error('nuevosConceptos.' . $index . '.exchange_ammount')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    {{-- 
                              <td class="text-center">
                                 <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                       id="discount_{{ $index }}"
                                       wire:model="nuevosConceptos.{{ $index }}.status_discount"
                                       value="true">
                                    <label class="custom-control-label" for="discount_{{ $index }}"></label>
                                 </div>
                              </td>
                              <td class="text-center">
                                 <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                       id="annuity_{{ $index }}"
                                       wire:model="nuevosConceptos.{{ $index }}.status_annuity"
                                       value="true">
                                    <label class="custom-control-label" for="annuity_{{ $index }}"></label>
                                 </div>
                              </td>
                              --}}
                                                    <td class="text-center">
                                                        @if (count($nuevosConceptos) > 1)
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                wire:click="removerConcepto({{ $index }})"
                                                                title="Eliminar concepto">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @else
                                                            <span class="text-muted small">Obligatorio</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Tip:</strong> La descripción se auto-completa al seleccionar el tipo de
                                    concepto.
                                    Los montos se actualizan automáticamente en el resumen.
                                </div>
                            @else
                                <div class="alert alert-warning text-center py-4">
                                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                    <h5>No hay conceptos agregados</h5>
                                    <p class="mb-3">Agregue al menos un concepto de pago para continuar.</p>
                                    <button type="button" class="btn btn-success" wire:click="agregarConceptoVacio">
                                        <i class="fas fa-plus"></i> Agregar Primer Concepto
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Resumen de Impacto --}}
                    <div class="card border-warning mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-calculator"></i> Resumen del Impacto en Deuda
                                <span class="badge badge-primary ml-2">Actualizado en tiempo real</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h5 class="text-muted mb-2">Deuda Actual</h5>
                                            <h3 class="text-primary mb-1" id="deuda-actual">
                                                ${{ number_format($deudaActualTotal ?? 0, 2) }}
                                            </h3>
                                            <small class="text-muted">Antes de esta operación</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h5 class="text-muted mb-2">Nueva Deuda</h5>
                                            <h3 class="text-success mb-1" id="nueva-deuda">
                                                ${{ number_format($nuevaDeudaTotal, 2) }}
                                            </h3>
                                            <small class="text-muted">Conceptos a agregar</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-danger">
                                        <div class="card-body">
                                            <h5 class="text-muted mb-2">Deuda Total Final</h5>
                                            <h3 class="text-danger mb-1" id="deuda-total">
                                                ${{ number_format(($deudaActualTotal ?? 0) + $nuevaDeudaTotal, 2) }}
                                            </h3>
                                            <small class="text-muted">Después de esta operación</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($nuevaDeudaTotal > 0)
                                <div class="mt-3 p-3 bg-light rounded">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <strong>Conceptos a crear:</strong>
                                            @foreach ($nuevosConceptos as $concepto)
                                                @if (!empty($concepto['nom_concepto_pago_id']) && $concepto['exchange_ammount'] > 0)
                                                    <span class="badge badge-light border mr-1 mb-1">
                                                        {{ $conceptosDisponibles[$concepto['nom_concepto_pago_id']] ?? 'N/A' }}
                                                        (${{ number_format($concepto['exchange_ammount'], 2) }})
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Debug temporal --}}
                    {{-- 
            <div class="card border-danger mb-4">
               <div class="card-header bg-danger text-white">
                  <h6 class="mb-0">Debug Info</h6>
               </div>
               <div class="card-body">
                  <p><strong>Nueva Deuda Total:</strong> {{ $nuevaDeudaTotal }}</p>
                  <p><strong>Conceptos Count:</strong> {{ count($nuevosConceptos) }}</p>
                  <button wire:click="calcularNuevaDeuda" class="btn btn-sm btn-warning">Recalcular Manual</button>
               </div>
            </div>
            --}}
                    {{-- Navegación --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" wire:click="pasoAnterior">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" wire:click="prepararVistaPrevia"
                            wire:loading.attr="disabled"
                            {{ empty($nuevaCuenta['name']) || empty($nuevaCuenta['date_expiration']) || $nuevaDeudaTotal <= 0 ? 'disabled' : '' }}>
                            <span wire:loading.remove>
                                <i class="fas fa-eye"></i> Vista Previa
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin"></i> Validando...
                            </span>
                        </button>
                    </div>
                </div>
            @endif
            {{-- PASO 5: Vista Previa --}}
            @if ($currentStep == 5)
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-eye text-primary"></i>
                        Paso 5: Vista Previa
                    </h4>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Revise cuidadosamente</strong> toda la información antes de confirmar la creación de la
                        deuda.
                        Esta acción registrará la deuda individual en el sistema.
                    </div>
                    {{-- Resumen General --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-contract"></i> Resumen General
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-user-graduate"></i> Información del Estudiante
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Cédula:</strong></td>
                                            <td>{{ $resumen['estudiante']['ci_estudiant'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>{{ $resumen['estudiante']['name'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $resumen['estudiante']['email'] ?? 'No registrado' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-file-invoice-dollar"></i> Información de la Deuda
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="50%"><strong>Plan de Pago:</strong></td>
                                            <td>{{ $resumen['planpago'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nueva Cuenta:</strong></td>
                                            <td>{{ $resumen['nuevaCuenta']['name'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Vencimiento:</strong></td>
                                            <td>{{ $resumen['fecha_vencimiento_formateada'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                @if (($resumen['nuevaCuenta']['status_bad'] ?? 'false') == 'true')
                                                    <span class="badge badge-danger">INCOBRABLE</span>
                                                @else
                                                    <span class="badge badge-success">COBRABLE</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if (!empty($resumen['nuevaCuenta']['description']))
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <strong>Descripción de la Cuenta:</strong>
                                        <p class="mb-0 text-muted">{{ $resumen['nuevaCuenta']['description'] }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Comparativa de Deudas --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-balance-scale"></i> Comparativa de Deudas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card border-primary shadow-sm">
                                        <div class="card-body py-4">
                                            <h6 class="text-muted mb-2">Deuda Actual</h6>
                                            <h3 class="text-primary mb-2">
                                                ${{ number_format($resumen['deudaActualTotal'], 2) }}</h3>
                                            <small class="text-muted">Antes de esta operación</small>
                                            @if ($resumen['deudaActualTotal'] > 0)
                                                <div class="mt-2">
                                                    <span class="badge badge-warning">
                                                        {{ $conceptosPendientes['total_cuentas'] ?? 0 }} cuenta(s)
                                                        pendiente(s)
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-success shadow-sm">
                                        <div class="card-body py-4">
                                            <h6 class="text-muted mb-2">Nueva Deuda</h6>
                                            <h3 class="text-success mb-2">+
                                                ${{ number_format($resumen['totalNuevaDeuda'], 2) }}</h3>
                                            <small class="text-muted">Conceptos a agregar</small>
                                            <div class="mt-2">
                                                <span class="badge badge-info">
                                                    {{ $resumen['total_conceptos'] }} concepto(s)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-danger shadow-sm">
                                        <div class="card-body py-4">
                                            <h6 class="text-muted mb-2">Deuda Total Final</h6>
                                            <h3 class="text-danger mb-2">
                                                ${{ number_format($resumen['deudaTotalFinal'], 2) }}</h3>
                                            <small class="text-muted">Después de esta operación</small>
                                            <div class="mt-2">
                                                @php
                                                    $incremento =
                                                        $resumen['deudaTotalFinal'] - $resumen['deudaActualTotal'];
                                                    $porcentaje =
                                                        $resumen['deudaActualTotal'] > 0
                                                            ? ($incremento / $resumen['deudaActualTotal']) * 100
                                                            : 100;
                                                @endphp
                                                <span
                                                    class="badge badge-{{ $incremento > 0 ? 'danger' : 'success' }}">
                                                    {{ $incremento > 0 ? '+' : '' }}{{ number_format($porcentaje, 1) }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Análisis de Impacto --}}
                            @if ($resumen['deudaActualTotal'] > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div
                                            class="alert alert-{{ $resumen['totalNuevaDeuda'] > 0 ? 'warning' : 'success' }}">
                                            <i class="fas fa-chart-line"></i>
                                            <strong>Análisis de Impacto:</strong>
                                            @if ($resumen['totalNuevaDeuda'] > 0)
                                                La deuda del estudiante aumentará en
                                                <strong>${{ number_format($resumen['totalNuevaDeuda'], 2) }}</strong>
                                                ({{ number_format(($resumen['totalNuevaDeuda'] / $resumen['deudaActualTotal']) * 100, 1) }}%
                                                más).
                                            @else
                                                No se agregará nueva deuda al estudiante.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Detalle de Nuevos Conceptos --}}
                    <div class="card mb-4">
                        <div
                            class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list-ul"></i> Detalle de Nuevos Conceptos
                            </h5>
                            <span class="badge badge-light">
                                {{ $resumen['total_conceptos'] }} concepto(s)
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="30%">Concepto</th>
                                            <th width="35%">Descripción</th>
                                            <th width="15%" class="text-right">Monto USD</th>
                                            <th width="15%" class="text-center">Configuraciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($resumen['nuevosConceptos'] as $index => $concepto)
                                            <tr>
                                                <td class="text-center">
                                                    <strong>{{ $index + 1 }}</strong>
                                                </td>
                                                <td>
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    <strong>{{ $concepto['nombre'] }}</strong>
                                                </td>
                                                <td>
                                                    @if ($concepto['descripcion'] && $concepto['descripcion'] !== 'Sin descripción')
                                                        {{ $concepto['descripcion'] }}
                                                    @else
                                                        <span class="text-muted">Sin descripción adicional</span>
                                                    @endif
                                                </td>
                                                <td class="text-right font-weight-bold">
                                                    ${{ number_format($concepto['monto'], 2) }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-around">
                                                        @if ($concepto['permite_descuento'])
                                                            <span class="badge badge-success"
                                                                title="Permite descuento">
                                                                <i class="fas fa-percentage"></i> Dto.
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary"
                                                                title="No permite descuento">
                                                                <i class="fas fa-ban"></i> Dto.
                                                            </span>
                                                        @endif
                                                        @if ($concepto['anualidad'])
                                                            <span class="badge badge-info" title="Anualidad">
                                                                <i class="fas fa-calendar-alt"></i> Anual
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary"
                                                                title="No es anualidad">
                                                                <i class="fas fa-calendar"></i> Anual
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="3" class="text-right">Total Nueva Deuda:</td>
                                            <td class="text-right text-primary">
                                                ${{ number_format($resumen['totalNuevaDeuda'], 2) }}
                                            </td>
                                            <td class="text-center">
                                                <small class="text-muted">
                                                    @if ($resumen['conceptos_con_descuento'] > 0)
                                                        <span class="badge badge-success mr-1">
                                                            {{ $resumen['conceptos_con_descuento'] }} con descuento
                                                        </span>
                                                    @endif
                                                    @if ($resumen['conceptos_anualidad'] > 0)
                                                        <span class="badge badge-info">
                                                            {{ $resumen['conceptos_anualidad'] }} anualidad(es)
                                                        </span>
                                                    @endif
                                                </small>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            {{-- Resumen de Configuraciones --}}
                            @if ($resumen['conceptos_con_descuento'] > 0 || $resumen['conceptos_anualidad'] > 0)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-secondary">
                                            <h6 class="mb-2">
                                                <i class="fas fa-cog"></i> Resumen de Configuraciones
                                            </h6>
                                            <div class="row">
                                                @if ($resumen['conceptos_con_descuento'] > 0)
                                                    <div class="col-md-6">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        <strong>{{ $resumen['conceptos_con_descuento'] }}
                                                            concepto(s)</strong> permiten descuento
                                                    </div>
                                                @endif
                                                @if ($resumen['conceptos_anualidad'] > 0)
                                                    <div class="col-md-6">
                                                        <i class="fas fa-check-circle text-info"></i>
                                                        <strong>{{ $resumen['conceptos_anualidad'] }}
                                                            concepto(s)</strong> son anualidades
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Información Adicional --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-info"></i> Información Adicional
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-info">
                                        <i class="fas fa-shield-alt"></i> Estado de la Cuenta
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="60%"><strong>Tipo de Cuenta:</strong></td>
                                            <td>
                                                <span class="badge badge-info">INDIVIDUAL</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado de Cobranza:</strong></td>
                                            <td>
                                                @if ($resumen['nuevaCuenta']['status_bad'] == 'true')
                                                    <span class="badge badge-danger">INCOBRABLE</span>
                                                @else
                                                    <span class="badge badge-success">COBRABLE</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha de Creación:</strong></td>
                                            <td>{{ now()->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-info">
                                        <i class="fas fa-calculator"></i> Resumen
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="60%"><strong>Total Conceptos:</strong></td>
                                            <td class="text-right">{{ $resumen['total_conceptos'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Promedio por Concepto:</strong></td>
                                            <td class="text-right">
                                                ${{ number_format($resumen['totalNuevaDeuda'] / max($resumen['total_conceptos'], 1), 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Concepto más Alto:</strong></td>
                                            <td class="text-right">
                                                ${{ number_format(collect($resumen['nuevosConceptos'])->max('monto') ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if (!empty($resumen['nuevaCuenta']['observations']))
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-info">
                                            <i class="fas fa-sticky-note"></i> Observaciones
                                        </h6>
                                        <div class="border rounded p-3 bg-light">
                                            {{ $resumen['nuevaCuenta']['observations'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Navegación --}}
                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            <button type="button" class="btn btn-secondary mr-2" wire:click="pasoAnterior">
                                <i class="fas fa-arrow-left"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-warning" wire:click="volverAEditarConceptos">
                                <i class="fas fa-edit"></i> Editar Conceptos
                            </button>
                        </div>
                        <button type="button" class="btn btn-success btn-lg" wire:click="confirmarCreacionDeuda"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-check-circle"></i> Confirmar y Continuar
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin"></i> Procesando...
                            </span>
                        </button>
                    </div>
                    @admin
                        {{-- Información de Debug (solo en desarrollo) --}}
                        @if (config('app.debug'))
                            <div class="card border-danger mt-4">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">Debug Information</h6>
                                </div>
                                <div class="card-body">
                                    <pre>{{ json_encode($resumen, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif
                    @endadmin
                </div>
            @endif
            {{-- PASO 6: Confirmación --}}
            @if ($currentStep == 6)
                @php
                    // Calcular estadísticas directamente en la vista para evitar el error
                    $estadisticas = [
                        'total_conceptos' => count($confirmacionData['nuevosConceptos'] ?? []),
                        'conceptos_con_descuento' => collect($confirmacionData['nuevosConceptos'] ?? [])
                            ->where('permite_descuento', true)
                            ->count(),
                        'conceptos_anualidad' => collect($confirmacionData['nuevosConceptos'] ?? [])
                            ->where('anualidad', true)
                            ->count(),
                        'monto_promedio' => collect($confirmacionData['nuevosConceptos'] ?? [])->avg('monto') ?? 0,
                        'monto_maximo' => collect($confirmacionData['nuevosConceptos'] ?? [])->max('monto') ?? 0,
                        'monto_minimo' => collect($confirmacionData['nuevosConceptos'] ?? [])->min('monto') ?? 0,
                    ];
                    $incremento = $confirmacionData['deudaTotalFinal'] - $confirmacionData['deudaActualTotal'];
                    $porcentajeIncremento =
                        $confirmacionData['deudaActualTotal'] > 0
                            ? ($incremento / $confirmacionData['deudaActualTotal']) * 100
                            : 100;
                @endphp
                <div class="step-content">
                    <h4 class="mb-4">
                        <i class="fas fa-check-double text-primary"></i>
                        Paso 6: Confirmación Final
                    </h4>
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle fa-2x mr-3"></i>
                            <div>
                                <strong>Última verificación:</strong> Esta acción creará la deuda individual en el
                                sistema
                                y afectará el estado financiero del estudiante. ¿Está completamente seguro de continuar?
                            </div>
                        </div>
                    </div>
                    {{-- Resumen Final --}}
                    <div class="card border-warning mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-clipboard-list"></i> Resumen Final a Crear
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-user-graduate"></i> Información del Estudiante
                                    </h6>
                                    <div class="border rounded p-3 bg-light">
                                        <p class="mb-2">
                                            <strong>Cédula:</strong>
                                            {{ $confirmacionData['estudiante']['ci_estudiant'] ?? 'N/A' }}
                                        </p>
                                        <p class="mb-2">
                                            <strong>Nombre:</strong>
                                            {{ $confirmacionData['estudiante']['name'] ?? 'N/A' }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>Email:</strong>
                                            {{ $confirmacionData['estudiante']['email'] ?? 'No registrado' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-info mb-3">
                                        <i class="fas fa-file-invoice"></i> Nueva Cuenta
                                    </h6>
                                    <div class="border rounded p-3 bg-light">
                                        <p class="mb-2">
                                            <strong>Cuenta:</strong>
                                            {{ $confirmacionData['nuevaCuenta']['name'] ?? 'N/A' }}
                                        </p>
                                        <p class="mb-2">
                                            <strong>Vencimiento:</strong>
                                            {{ $confirmacionData['fecha_vencimiento_formateada'] ?? 'No definida' }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>Estado:</strong>
                                            @if (($confirmacionData['nuevaCuenta']['status_bad'] ?? 'false') == 'true')
                                                <span class="badge badge-danger">INCOBRABLE</span>
                                            @else
                                                <span class="badge badge-success">COBRABLE</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            {{-- Resumen Financiero --}}
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card border-primary shadow">
                                        <div class="card-body py-3">
                                            <h5 class="text-muted mb-1">Deuda Actual</h5>
                                            <h3 class="text-primary mb-1">
                                                ${{ number_format($confirmacionData['deudaActualTotal'] ?? 0, 2) }}
                                            </h3>
                                            <small class="text-muted">Estado actual del estudiante</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-success shadow">
                                        <div class="card-body py-3">
                                            <h5 class="text-muted mb-1">Nueva Deuda</h5>
                                            <h3 class="text-success mb-1">
                                                ${{ number_format($confirmacionData['totalNuevaDeuda'] ?? 0, 2) }}
                                            </h3>
                                            <small class="text-muted">A agregar al estudiante</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-danger shadow">
                                        <div class="card-body py-3">
                                            <h5 class="text-muted mb-1">Deuda Total Final</h5>
                                            <h3 class="text-danger mb-1">
                                                ${{ number_format($confirmacionData['deudaTotalFinal'] ?? 0, 2) }}
                                            </h3>
                                            <small class="text-muted">Nuevo estado financiero</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Análisis de Impacto --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div
                                        class="alert alert-{{ $incremento > 0 ? 'warning' : 'success' }} text-center">
                                        <h6 class="mb-2">
                                            <i class="fas fa-chart-bar"></i> Impacto Financiero
                                        </h6>
                                        <p class="mb-0">
                                            @if ($incremento > 0)
                                                <strong>La deuda del estudiante aumentará en
                                                    ${{ number_format($incremento, 2) }}
                                                    ({{ number_format($porcentajeIncremento, 1) }}%).</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Pasará de
                                                    ${{ number_format($confirmacionData['deudaActualTotal'] ?? 0, 2) }}
                                                    a
                                                    ${{ number_format($confirmacionData['deudaTotalFinal'] ?? 0, 2) }}
                                                </small>
                                            @else
                                                <strong>No se generará nueva deuda para el estudiante.</strong>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Conceptos a Crear --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list-ol text-info"></i> Conceptos a Crear
                                <span class="badge badge-primary ml-2">{{ $estadisticas['total_conceptos'] }}</span>
                            </h5>
                            <div class="text-right">
                                <small class="text-muted">
                                    @if ($estadisticas['conceptos_con_descuento'] > 0)
                                        <span class="badge badge-success mr-2">
                                            <i class="fas fa-percentage"></i>
                                            {{ $estadisticas['conceptos_con_descuento'] }} con descuento
                                        </span>
                                    @endif
                                    @if ($estadisticas['conceptos_anualidad'] > 0)
                                        <span class="badge badge-info">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $estadisticas['conceptos_anualidad'] }} anualidad(es)
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="40%">Concepto</th>
                                            <th width="30%">Descripción</th>
                                            <th width="15%" class="text-right">Monto USD</th>
                                            <th width="10%" class="text-center">Configuración</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($confirmacionData['nuevosConceptos'] ?? [] as $index => $concepto)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $concepto['nombre'] ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    @if (($concepto['descripcion'] ?? '') && $concepto['descripcion'] !== 'Sin descripción')
                                                        <small
                                                            class="text-muted">{{ $concepto['descripcion'] }}</small>
                                                    @else
                                                        <small class="text-muted font-italic">Sin descripción
                                                            adicional</small>
                                                    @endif
                                                </td>
                                                <td class="text-right font-weight-bold">
                                                    ${{ number_format($concepto['monto'] ?? 0, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    @if (($concepto['permite_descuento'] ?? false) || ($concepto['anualidad'] ?? false))
                                                        <div class="d-flex flex-column align-items-center">
                                                            @if ($concepto['permite_descuento'] ?? false)
                                                                <span class="badge badge-success mb-1"
                                                                    title="Permite descuento">
                                                                    <i class="fas fa-percentage"></i>
                                                                </span>
                                                            @endif
                                                            @if ($concepto['anualidad'] ?? false)
                                                                <span class="badge badge-info" title="Anualidad">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="badge badge-secondary">Básico</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-dark text-white">
                                        <tr>
                                            <td colspan="3" class="text-right font-weight-bold">TOTAL NUEVA DEUDA:
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                ${{ number_format($confirmacionData['totalNuevaDeuda'] ?? 0, 2) }}
                                            </td>
                                            <td class="text-center">
                                                <small>
                                                    {{ $estadisticas['total_conceptos'] }} concepto(s)
                                                </small>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            {{-- Estadísticas de Conceptos --}}
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="card bg-light border">
                                        <div class="card-body text-center py-2">
                                            <h6 class="mb-1">Monto Promedio</h6>
                                            <h5 class="text-primary mb-0">
                                                ${{ number_format($estadisticas['monto_promedio'], 2) }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border">
                                        <div class="card-body text-center py-2">
                                            <h6 class="mb-1">Monto Máximo</h6>
                                            <h5 class="text-success mb-0">
                                                ${{ number_format($estadisticas['monto_maximo'], 2) }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border">
                                        <div class="card-body text-center py-2">
                                            <h6 class="mb-1">Monto Mínimo</h6>
                                            <h5 class="text-info mb-0">
                                                ${{ number_format($estadisticas['monto_minimo'], 2) }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Información del Plan de Pago --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-file-invoice-dollar text-primary"></i> Información del Plan de Pago
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Plan Seleccionado:</strong>
                                        {{ $confirmacionData['planpago'] ?? 'N/A' }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>ID del Plan:</strong>
                                        <span
                                            class="badge badge-secondary">{{ $confirmacionData['planpago_id'] ?? 'N/A' }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Fecha y Hora:</strong> {{ now()->format('d/m/Y H:i:s') }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>Usuario:</strong> {{ auth()->user()->name ?? 'Sistema' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Advertencia Final --}}
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt fa-2x mr-3"></i>
                            <div>
                                <h6 class="mb-2">
                                    <i class="fas fa-lock"></i> Acción Irreversible
                                </h6>
                                <p class="mb-0">
                                    Una vez confirmada, esta acción <strong>NO PODRÁ SER DESHECHA</strong>
                                    automáticamente.
                                    La deuda individual quedará registrada en el sistema y afectará el estado financiero
                                    del estudiante.
                                    Si necesita modificar o eliminar la deuda, deberá hacerlo manualmente desde el
                                    módulo correspondiente.
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- Navegación --}}
                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            <button type="button" class="btn btn-secondary mr-2" wire:click="pasoAnterior">
                                <i class="fas fa-arrow-left"></i> Revisar
                            </button>
                            <button type="button" class="btn btn-warning" wire:click="cancelarCreacion">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                        <button type="button" class="btn btn-success btn-lg" wire:click="crearDeudaIndividual"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-save"></i> Confirmar y Crear Deuda
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin"></i> Procesando...
                            </span>
                        </button>
                    </div>
                    {{-- Información de Procesamiento --}}
                    <div wire:loading wire:target="crearDeudaIndividual" class="mt-3">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm mr-3" role="status">
                                    <span class="sr-only">Procesando...</span>
                                </div>
                                <div>
                                    <strong>Procesando solicitud...</strong><br>
                                    <small class="text-muted">
                                        Creando cuenta individual y registrando conceptos de pago.
                                        Esto puede tomar unos segundos.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{-- PASO 7: Éxito --}}
            @if ($currentStep == 7)
                <div class="step-content">
                    <div class="text-center py-4">
                        @if ($operacionExitosa)
                            {{-- Éxito --}}
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 100px;"></i>
                            </div>
                            <h2 class="text-success mb-3">
                                <i class="fas fa-trophy"></i>
                                ¡Deuda Individual Creada Exitosamente!
                            </h2>
                            <p class="lead mb-4">
                                La deuda individual ha sido registrada correctamente en el sistema.
                            </p>
                            {{-- ID de la Cuenta Creada --}}
                            @if ($cuentaCreadaId)
                                <div class="alert alert-info d-inline-block mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle fa-2x mr-3"></i>
                                        <div>
                                            <strong>ID de la Cuenta Creada:</strong>
                                            <span class="badge badge-primary badge-pill ml-2"
                                                style="font-size: 18px;">
                                                #{{ $cuentaCreadaId }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                Puede usar este ID para buscar la cuenta en el sistema
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- Resumen Rápido --}}
                            <div class="row mb-4 justify-content-center">
                                <div class="col-md-3 mb-3">
                                    <div class="card border-success shadow-sm">
                                        <div class="card-body">
                                            <div class="text-success mb-2">
                                                <i class="fas fa-user-graduate fa-2x"></i>
                                            </div>
                                            <h5 class="card-title">Estudiante</h5>
                                            <p class="card-text mb-1 small">
                                                {{ $estudianteSeleccionado['ci_estudiant'] ?? 'N/A' }}</p>
                                            <p class="card-text small text-muted">
                                                {{ $estudianteSeleccionado['name'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card border-info shadow-sm">
                                        <div class="card-body">
                                            <div class="text-info mb-2">
                                                <i class="fas fa-list-alt fa-2x"></i>
                                            </div>
                                            <h5 class="card-title">Conceptos</h5>
                                            <h3 class="text-primary mb-0">{{ count($nuevosConceptos) }}</h3>
                                            <small class="text-muted">creados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-body">
                                            <div class="text-warning mb-2">
                                                <i class="fas fa-dollar-sign fa-2x"></i>
                                            </div>
                                            <h5 class="card-title">Monto Total</h5>
                                            <h3 class="text-primary mb-0">${{ number_format($nuevaDeudaTotal, 2) }}
                                            </h3>
                                            <small class="text-muted">nueva deuda</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card border-primary shadow-sm">
                                        <div class="card-body">
                                            <div class="text-primary mb-2">
                                                <i class="fas fa-calendar-check fa-2x"></i>
                                            </div>
                                            <h5 class="card-title">Fecha</h5>
                                            <p class="card-text mb-0 small">{{ now()->format('d/m/Y') }}</p>
                                            <p class="card-text small text-muted">{{ now()->format('H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Detalle de la Operación --}}
                            <div class="card border-success mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-clipboard-check"></i> Resumen de la Operación
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-success">
                                                <i class="fas fa-check-circle"></i> Operación Exitosa
                                            </h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    <strong>Cuenta Individual:</strong> Creada correctamente
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    <strong>Conceptos de Pago:</strong> {{ count($nuevosConceptos) }}
                                                    registrados
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    <strong>Transacción:</strong> Completada sin errores
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    <strong>Plan de Pago:</strong>
                                                    {{ $planpagosDisponibles[$planpagoId] ?? 'N/A' }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    <strong>Estado:</strong>
                                                    @if (($nuevaCuenta['status_bad'] ?? 'false') == 'true')
                                                        <span class="badge badge-danger">INCOBRABLE</span>
                                                    @else
                                                        <span class="badge badge-success">COBRABLE</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-info">
                                                <i class="fas fa-info-circle"></i> Información Técnica
                                            </h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-hashtag text-info mr-2"></i>
                                                    <strong>ID Cuenta:</strong> {{ $cuentaCreadaId ?? 'N/A' }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-user-tie text-info mr-2"></i>
                                                    <strong>Usuario:</strong> {{ auth()->user()->name ?? 'Sistema' }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-clock text-info mr-2"></i>
                                                    <strong>Hora:</strong> {{ now()->format('H:i:s') }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-calendar text-info mr-2"></i>
                                                    <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Conceptos Creados --}}
                            @if (count($nuevosConceptos) > 0)
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-list-ul text-primary"></i> Conceptos Creados
                                            <span
                                                class="badge badge-primary ml-2">{{ count($nuevosConceptos) }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Concepto</th>
                                                        <th class="text-right">Monto USD</th>
                                                        <th class="text-center">Configuración</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($nuevosConceptos as $concepto)
                                                        @if (!empty($concepto['nom_concepto_pago_id']))
                                                            <tr>
                                                                <td>
                                                                    <i class="fas fa-check text-success mr-2"></i>
                                                                    {{ $conceptosDisponibles[$concepto['nom_concepto_pago_id']] ?? 'N/A' }}
                                                                    @if ($concepto['concepto_description'] && $concepto['concepto_description'] !== 'Sin descripción')
                                                                        <br>
                                                                        <small
                                                                            class="text-muted">{{ $concepto['concepto_description'] }}</small>
                                                                    @endif
                                                                </td>
                                                                <td class="text-right font-weight-bold">
                                                                    ${{ number_format($concepto['exchange_ammount'] ?? 0, 2) }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @if (($concepto['status_discount'] ?? 'false') === 'true')
                                                                        <span class="badge badge-success mr-1"
                                                                            title="Permite descuento">
                                                                            <i class="fas fa-percentage"></i>
                                                                        </span>
                                                                    @endif
                                                                    @if (($concepto['status_annuity'] ?? 'false') === 'true')
                                                                        <span class="badge badge-info"
                                                                            title="Anualidad">
                                                                            <i class="fas fa-calendar-alt"></i>
                                                                        </span>
                                                                    @endif
                                                                    @if (($concepto['status_discount'] ?? 'false') === 'false' && ($concepto['status_annuity'] ?? 'false') === 'false')
                                                                        <span
                                                                            class="badge badge-secondary">Básico</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-light font-weight-bold">
                                                    <tr>
                                                        <td class="text-right">Total Creado:</td>
                                                        <td class="text-right text-primary">
                                                            ${{ number_format($nuevaDeudaTotal, 2) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- Estadísticas Adicionales --}}
                            @php
                                $conceptosConDescuento = collect($nuevosConceptos)
                                    ->where('status_discount', 'true')
                                    ->count();
                                $conceptosAnualidad = collect($nuevosConceptos)
                                    ->where('status_annuity', 'true')
                                    ->count();
                                $montoPromedio =
                                    count($nuevosConceptos) > 0 ? $nuevaDeudaTotal / count($nuevosConceptos) : 0;
                            @endphp
                            @if ($conceptosConDescuento > 0 || $conceptosAnualidad > 0)
                                <div class="card border-info mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-chart-pie"></i> Estadísticas de Conceptos
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            @if ($conceptosConDescuento > 0)
                                                <div class="col-md-4">
                                                    <div class="card bg-light border-0">
                                                        <div class="card-body">
                                                            <h6 class="text-success mb-1">Con Descuento</h6>
                                                            <h3 class="text-success mb-0">
                                                                {{ $conceptosConDescuento }}</h3>
                                                            <small class="text-muted">conceptos</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($conceptosAnualidad > 0)
                                                <div class="col-md-4">
                                                    <div class="card bg-light border-0">
                                                        <div class="card-body">
                                                            <h6 class="text-info mb-1">Anualidades</h6>
                                                            <h3 class="text-info mb-0">{{ $conceptosAnualidad }}</h3>
                                                            <small class="text-muted">conceptos</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-4">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <h6 class="text-primary mb-1">Monto Promedio</h6>
                                                        <h4 class="text-primary mb-0">
                                                            ${{ number_format($montoPromedio, 2) }}</h4>
                                                        <small class="text-muted">por concepto</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- Acciones Posteriores --}}
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-forward"></i> ¿Qué desea hacer ahora?
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center mb-3">
                                            <button type="button"
                                                class="btn btn-outline-success btn-lg w-100 h-100 py-3"
                                                wire:click="reiniciarAsistenteCompleto">
                                                <i class="fas fa-redo fa-2x mb-2"></i>
                                                <br>
                                                <strong>Reiniciar Asistente</strong>
                                                <br>
                                                <small class="d-block mt-1">Volver al paso 1</small>
                                            </button>
                                        </div>
                                        <div class="col-md-4 text-center mb-3">
                                            <a href="{{ route('administracion.estudiants.index') }}"
                                                class="btn btn-outline-primary btn-lg w-100 h-100 py-3">
                                                <i class="fas fa-home fa-2x mb-2"></i>
                                                <br>
                                                <strong>Ir a Estudiantes</strong>
                                                <br>
                                                <small class="d-block mt-1">Gestión general</small>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Error --}}
                            <div class="alert alert-danger">
                                <div class="mb-4">
                                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                                </div>
                                <h3 class="text-danger mb-3">Error al Procesar</h3>
                                <p class="lead mb-4">
                                    No se pudo crear la deuda individual. Por favor, intente nuevamente.
                                </p>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-warning btn-lg mr-3"
                                        wire:click="$set('currentStep', 5)">
                                        <i class="fas fa-arrow-left"></i> Volver a Vista Previa
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-lg"
                                        wire:click="$set('currentStep', 1)">
                                        <i class="fas fa-home"></i> Volver al Inicio
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- Información de Auditoría --}}
                    @if ($operacionExitosa)
                        <div class="card border-light mt-4">
                            <div class="card-body text-center text-muted">
                                <small>
                                    <i class="fas fa-clock"></i>
                                    Operación completada el {{ now()->format('d/m/Y') }} a las
                                    {{ now()->format('H:i:s') }}
                                    |
                                    <i class="fas fa-user"></i>
                                    Usuario: {{ auth()->user()->name ?? 'Sistema' }}
                                    |
                                    <i class="fas fa-code-branch"></i>
                                    ID Transacción: {{ uniqid() }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        <div class="card-footer bg-light text-muted">
            <div class="row">
                <div class="col-md-6">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        <strong>Paso {{ $currentStep }} de {{ $totalSteps }}</strong>
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
    @section('stylesheet')
        @parent
        <style>
            .search-results {
                max-height: 300px;
                overflow-y: auto;
            }

            .search-result-item:hover {
                background-color: #f8f9fa;
            }

            .cursor-pointer {
                cursor: pointer;
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
        </style>
        <style>
            @media print {

                .btn,
                .alert,
                .card-header {
                    display: none !important;
                }

                .step-content {
                    margin: 0 !important;
                    padding: 0 !important;
                }

                .card {
                    border: 1px solid #000 !important;
                    box-shadow: none !important;
                }
            }
        </style>
    @endsection
    @section('scripts')
        @parent
        <script>
            document.addEventListener('livewire:load', function() {
                // Forzar actualización cuando cambien los montos
                Livewire.on('conceptoUpdated', () => {
                    console.log('Concepto actualizado, recalculando...');
                });
            });
        </script>
        <script>
            document.addEventListener('livewire:load', function() {
                // Reproducir sonido de éxito (opcional)
                Livewire.on('operacionExitosa', () => {
                    // Puedes agregar un sonido de éxito aquí si lo deseas
                    console.log('Operación completada exitosamente');
                });
            });

            // Listener para SweetAlert
            window.addEventListener('swal', event => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: event.detail.title,
                        text: event.detail.text,
                        icon: event.detail.icon,
                        confirmButtonText: event.detail.confirmButtonText || 'Aceptar',
                        confirmButtonColor: event.detail.confirmButtonColor || '#28a745'
                    });
                } else {
                    console.warn('SweetAlert2 is not loaded');
                }
            });
        </script>
    @endsection
</div>
