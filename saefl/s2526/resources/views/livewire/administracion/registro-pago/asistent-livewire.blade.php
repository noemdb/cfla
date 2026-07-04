<div>
    <fieldset {{ $disabled ? 'disabled' : '' }}>
    {{-- <fieldset> --}}
    {{-- Información del representante si se pasa como parámetro --}}
    @if ($representant)
        <div class="alert alert-info mb-4 shadow-sm rounded-lg">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-1 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i>
                        Registrando pago para: <strong>{{ $representant->name }}</strong>
                    </h6>
                    <small class="text-muted">
                        CI: {{ $representant->ci_representant }} |
                        Estudiantes: {{ $estudiantes->count() }}
                        @if ($availableAbonos->count() > 0 || $availableCreditosAFavor->count() > 0)
                            | Recursos disponibles:
                            <span class="font-weight-bold text-danger  border rounded p-1 bg-white">
                                Bs. {{ number_format($availableAbonos->sum('abono_ammount') + $availableCreditosAFavor->sum('credito_ammount'), 2) }}
                            </span>

                            <span class="font-weight-bold text-dark border rounded p-1 bg-white">
                                USD {{ number_format($availableAbonos->sum('exchange_ammount') + $availableCreditosAFavor->sum('exchange_ammount'), 2) }}
                            </span>
                        @endif
                    </small>
                </div>
                <div class="col-md-4 text-right">

                    @php
                        $exchange_ammount_expire_bill = $representant
                            ? $representant->exchange_ammount_expire_bill
                            : null;

                        $ammount_expire_bill = $exchange_rate_current
                            ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill
                            : null;
                        $ammount_expire_bill = round($ammount_expire_bill, 2);

                        $status_debtor = round($exchange_ammount_expire_bill, 2) > 0 ? true : false;

                    @endphp
                    @if ($status_debtor)
                        <strong class="text-danger">Saldo Pendiente:</strong>
                        <span class="badge badge-danger py-2 px-3 rounded-sm">Bs.
                            {{ f_float($ammount_expire_bill, 2) }} </span>
                        <span class="badge badge-dark py-2 px-3 rounded-sm">USD
                            {{ f_float($exchange_ammount_expire_bill, 2) }} </span>
                    @else
                        <span class="badge badge-success py-2 px-3 rounded-sm">SOLVENTE</span>
                    @endif

                </div>
            </div>
        </div>
    @endif

    {{-- Mensajes de estado --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-4 shadow-sm rounded">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-4 shadow-sm rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show mt-4 shadow-sm rounded">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <fieldset>

        Indicador de pasos
        <div class="step-indicator mb-5">
            <div class="step {{ $currentStep >= 1 ? 'active' : '' }} {{ $currentStep > 1 ? 'completed' : '' }}">
                <div class="step-circle">1</div>
                <div class="step-label">Datos del Pago</div>
            </div>
            <div class="step {{ $currentStep >= 2 ? 'active' : '' }} {{ $currentStep > 2 ? 'completed' : '' }}">
                <div class="step-circle">2</div>
                <div class="step-label">Recursos Disponibles</div>
            </div>
            <div class="step {{ $currentStep >= 3 ? 'active' : '' }} {{ $currentStep > 3 ? 'completed' : '' }}">
                <div class="step-circle">3</div>
                <div class="step-label">Cuotas Vencidas</div>
            </div>
            <div class="step {{ $currentStep >= 4 ? 'active' : '' }} {{ $currentStep > 4 ? 'completed' : '' }}">
                <div class="step-circle">4</div>
                <div class="step-label">Cuotas No Vencidas</div>
            </div>
            <div class="step {{ $currentStep >= 5 ? 'active' : '' }} {{ $currentStep > 5 ? 'completed' : '' }}">
                <div class="step-circle">5</div>
                <div class="step-label">Vista Previa</div>
            </div>
            <div class="step {{ $currentStep >= 6 ? 'active' : '' }} {{ $currentStep > 6 ? 'completed' : '' }}">
                <div class="step-circle">6</div>
                <div class="step-label">Confirmación</div>
            </div>
            <div class="step {{ $currentStep >= 7 ? 'active' : '' }}">
                <div class="step-circle">7</div>
                <div class="step-label">Recibo</div>
            </div>
        </div>

        {{-- Contenido de los pasos --}}
        @if ($currentStep == 1)
            {{-- Paso 1: Datos del Pago Reportado --}}
            <div class="card shadow-sm rounded-lg">
                <div class="card-header alert-light text-primary font-weight-bold py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-money-bill mr-2"></i>
                        Paso 1: Datos del Pago Reportado
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="representant_id" class="form-label font-weight-bold">Representante <span
                                                class="text-danger">*</span></label>
                                        <select wire:model="representant_id"
                                            class="form-control @error('representant_id') is-invalid @enderror"
                                            {{ $representant ? 'disabled' : '' }}>
                                            <option value="">Seleccione un representante</option>
                                            @foreach ($representants as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('representant_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if ($representant)
                                            <small class="form-text text-info">
                                                <i class="fas fa-lock mr-1"></i>
                                                Representante preseleccionado
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="banco_id" class="form-label font-weight-bold">Banco</label>
                                        <select wire:model="banco_id" class="form-control">
                                            <option value="">Seleccione banco</option>
                                            @foreach ($bancos as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @unless ($hideIngresoFields)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="method_pay_id" class="form-label font-weight-bold">Método de Pago <span
                                                    class="text-danger">*</span></label>
                                            <select wire:model="method_pay_id"
                                                class="form-control @error('method_pay_id') is-invalid @enderror"
                                                @if ($hideIngresoFields) disabled @endif>
                                                <option value="">Seleccione método de pago</option>
                                                @foreach ($metodosPago as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('method_pay_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="number_i_pay" class="form-label font-weight-bold">Número de Transacción
                                                <span class="text-danger">*</span></label>
                                            <input type="text" wire:model="number_i_pay"
                                                class="form-control @error('number_i_pay') is-invalid @enderror"
                                                placeholder="Número de referencia">
                                            @error('number_i_pay')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endunless

                            @unless ($hideIngresoFields)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="date_transaction" class="form-label font-weight-bold">Fecha de Transacción
                                                <span class="text-danger">*</span></label>
                                            <input type="date" wire:model="date_transaction"
                                                class="form-control @error('date_transaction') is-invalid @enderror">
                                            @error('date_transaction')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="date_payment" class="form-label font-weight-bold">Fecha de Pago <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" wire:model="date_payment"
                                                class="form-control @error('date_payment') is-invalid @enderror">
                                            @error('date_payment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="exchange_ammount" class="form-label font-weight-bold">Monto (Moneda
                                                Referencial)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text font-weight-bold">USD</span>
                                                </div>
                                                <input type="number" step="0.01" wire:model="exchange_ammount"
                                                    class="form-control" placeholder="0.00">
                                            </div>
                                            @if ($this->getCurrentExchangeRate())
                                                <small class="form-text text-info mt-1">
                                                    <i class="fas fa-exchange-alt mr-1"></i>
                                                    Tasa: 1 USD = {{ number_format($this->getCurrentExchangeRate(), 2) }} Bs
                                                </small>
                                            @endif
                                            @if ($exchange_ammount && $this->getCurrentExchangeRate())
                                                <small class="form-text text-success mt-1">
                                                    <i class="fas fa-calculator mr-1"></i>
                                                    Calculado: ${{ (isset($exchange_ammount)) ? number_format($exchange_ammount, 2) : null}} ×
                                                    {{ number_format($this->getCurrentExchangeRate(), 2) }} = Bs
                                                    {{ (isset($ingreso_ammount)) ? number_format($ingreso_ammount, 2) : null }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ingreso_ammount" class="form-label font-weight-bold">Monto Bs. <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text font-weight-bold">Bs</span>
                                                </div>
                                                <input type="number" step="0.01" wire:model="ingreso_ammount"
                                                    class="form-control @error('ingreso_ammount') is-invalid @enderror"
                                                    placeholder="0.00">
                                            </div>
                                            @error('ingreso_ammount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if ($ingreso_ammount && $this->getCurrentExchangeRate())
                                                <small class="form-text text-success mt-1">
                                                    <i class="fas fa-calculator mr-1"></i>
                                                    Equivale a: Bs {{ number_format($ingreso_ammount, 2) }} ÷
                                                    @php $getCurrentExchangeRate = $this->getCurrentExchangeRate(); @endphp
                                                    {{ (!empty($getCurrentExchangeRate)) ? number_format($getCurrentExchangeRate, 2) : null}} =
                                                    ${{ (!empty($exchange_ammount)) ? number_format($exchange_ammount, 2) : null }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="person_bill_ci" class="form-label font-weight-bold">Cédula del
                                                Titular</label>
                                            <input type="text" wire:model="person_bill_ci" class="form-control"
                                                placeholder="V-12345678">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="person_bill_name" class="form-label font-weight-bold">Nombre del
                                                Titular</label>
                                            <input type="text" wire:model="person_bill_name" class="form-control"
                                                placeholder="Nombre completo">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label for="ingreso_observations" class="form-label font-weight-bold">Observaciones</label>
                                    <textarea wire:model="ingreso_observations" class="form-control" rows="3"
                                        placeholder="Observaciones adicionales"></textarea>
                                </div>
                            @endunless
                            
                        </div>
                        <div class="col-md-4">
                            <div class="text-right border rounded shadow-sm p-2"> 
                                @include('administracion.registropagos.form.asistent.partials.representant.resumen') 
                            </div>
                        </div>
                    </div>
                    

                </div>
            </div>
        @elseif($currentStep == 2)
            {{-- Paso 2: Recursos Disponibles --}}
            <div class="card shadow-sm rounded-lg">
                <div class="card-header alert-light text-primary font-weight-bold py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-piggy-bank mr-2"></i>
                        Paso 2: Recursos Disponibles
                    </h5>
                </div>
                @admin
                <div class="card-body p-4">
                    Botones de prueba temporales - SOLO PARA DESARROLLO
                    <div class="alert alert-info mb-3" style="border-left: 4px solid #17a2b8;">
                        <h6 class="font-weight-bold text-info mb-2">
                            <i class="fas fa-flask mr-2"></i>Herramientas de Prueba
                        </h6>
                        <div class="btn-group" role="group">
                            <button type="button" wire:click="createTestCredits"
                                class="btn btn-sm btn-outline-info">
                                <i class="fas fa-plus mr-1"></i>Crear Créditos de Prueba
                            </button>
                            <button type="button" wire:click="cleanTestCredits"
                                class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-trash mr-1"></i>Limpiar Créditos de Prueba
                            </button>
                            <button type="button" wire:click="loadAvailableResources"
                                class="btn btn-sm btn-outline-success">
                                <i class="fas fa-sync mr-1"></i>Recargar Recursos
                            </button>
                        </div>
                        <small class="d-block mt-2 text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Estos botones son solo para pruebas. Los créditos creados tendrán montos de USD 10 y USD 5.
                        </small>
                    </div>
                    @if ($availableAbonos->count() > 0 || $availableCreditosAFavor->count() > 0)
                        Resumen de recursos disponibles
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card bg-light h-100 shadow-sm rounded">
                                    <div class="card-body text-center">
                                        <h6 class="text-primary font-weight-bold">Abonos Disponibles</h6>
                                        <h4 class="text-success font-weight-bold">
                                            Bs. {{ number_format($availableAbonos->sum('abono_ammount'), 2) }}</h4>
                                        @if ($availableAbonos->sum('exchange_ammount') > 0)
                                            <small class="text-muted">USD
                                                {{ number_format($availableAbonos->sum('exchange_ammount'), 2) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card bg-light h-100 shadow-sm rounded">
                                    <div class="card-body text-center">
                                        <h6 class="text-info font-weight-bold">Créditos a Favor</h6>
                                        <h4 class="text-success font-weight-bold">
                                            Bs.
                                            {{ number_format($availableCreditosAFavor->sum('credito_ammount'), 2) }}
                                        </h4>
                                        @if ($availableCreditosAFavor->sum('exchange_ammount') > 0)
                                            <small class="text-muted">USD
                                                {{ number_format($availableCreditosAFavor->sum('exchange_ammount'), 2) }}
                                                (Ref.)</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-primary text-white h-100 shadow-sm rounded">
                                    <div class="card-body text-center">
                                        <h6 class="font-weight-bold">Total Recursos</h6>
                                        <h4 class="font-weight-bold">Bs.
                                            {{ number_format($availableAbonos->sum('abono_ammount') + $availableCreditosAFavor->sum('credito_ammount'), 2) }}
                                        </h4>
                                        @if ($availableAbonos->sum('exchange_ammount') + $availableCreditosAFavor->sum('exchange_ammount') > 0)
                                            <small>USD
                                                {{ number_format($availableAbonos->sum('exchange_ammount') + $availableCreditosAFavor->sum('exchange_ammount'), 2) }}
                                                (Ref.)</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mb-4">Seleccione los recursos adicionales que desea utilizar para este
                            pago:
                        </p>

                        @if ($availableAbonos->count() > 0)
                            <h6 class="text-primary font-weight-bold mb-3">Abonos Disponibles</h6>
                            @foreach ($availableAbonos as $abono)
                                <div
                                    class="resource-item {{ in_array('abono_' . $abono->id, $selectedResources) ? 'selected' : '' }} mb-3 p-3 rounded shadow-sm">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="selectedResources"
                                            value="abono_{{ $abono->id }}" class="form-check-input"
                                            id="abono_{{ $abono->id }}">
                                        <label class="form-check-label w-100" for="abono_{{ $abono->id }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-dark">{{ $abono->metodo_pago }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $abono->abono_description }}</small>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-success py-1 px-2 rounded-sm">Bs.
                                                        {{ number_format($abono->abono_ammount, 2) }}</span>
                                                    @if ($abono->exchange_ammount)
                                                        <br>
                                                        <small class="text-muted">USD
                                                            {{ number_format($abono->exchange_ammount, 2) }} (Ref.)
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if ($availableCreditosAFavor->count() > 0)
                            <h6 class="text-info font-weight-bold mt-4 mb-3">Créditos a Favor Disponibles</h6>
                            @foreach ($availableCreditosAFavor as $credito)
                                <div
                                    class="resource-item {{ in_array('credito_' . $credito->id, $selectedResources) ? 'selected' : '' }} mb-3 p-3 rounded shadow-sm">
                                    <div class="form-check w-100">
                                        <input type="checkbox" wire:model="selectedResources"
                                            value="credito_{{ $credito->id }}" class="form-check-input"
                                            id="credito_{{ $credito->id }}">
                                        <label class="form-check-label w-100" for="credito_{{ $credito->id }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-dark">Crédito a Favor</strong>
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $credito->credito_description }}</small>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-info py-1 px-2 rounded-sm">Bs.
                                                        {{ number_format($credito->credito_ammount, 2) }}</span>
                                                    @if ($credito->exchange_ammount)
                                                        <br><small class="text-muted">USD
                                                            {{ number_format($credito->exchange_ammount, 2) }}
                                                            (Ref.)
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @else
                        <div class="alert alert-info shadow-sm rounded">
                            <i class="fas fa-info-circle mr-2"></i>
                            No hay recursos adicionales disponibles para este representante.
                        </div>
                    @endif
                </div>
                @endadmin
            </div>
            @elseif($currentStep == 3)
                {{-- Paso 3: Cuotas Vencidas --}}
                <div class="card shadow-sm rounded-lg">
                    <div class="card-header alert-light text-primary font-weight-bold py-3">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Paso 3: Cuotas Vencidas
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($overdueQuotasByStudent->count() > 0)
                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Seleccione las cuotas vencidas que desea cancelar</strong>
                            </div>

                            {{-- Grid Responsive --}}
                            <div class="row">
                                @foreach ($overdueQuotasByStudent as $studentId => $studentData)
                                    <div class="col-12 mb-4">
                                        {{-- Header del Estudiante --}}
                                        <div class="student-header bg-light p-3 rounded mb-3">
                                            <h6 class="mb-0 font-weight-bold text-primary">
                                                <i class="fas fa-user-graduate mr-2"></i>
                                                {{ $studentData['student']['name'] }} {{ $studentData['student']['lastname'] }}
                                            </h6>
                                            <small class="text-muted">
                                                CI: {{ $studentData['student']['ci_estudiant'] ?? 'N/A' }}
                                            </small>
                                        </div>

                                        {{-- Grid de Cuotas --}}
                                        <div class="row">
                                            @foreach ($studentData['quotas'] as $quota)
                                                <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                                    <div class="card quota-card h-100 
                                                        {{ in_array($quota['id'] . '_' . $studentData['student']['id'], $selectedOverdueQuotas) ? 'border-success selected-quota' : 'border-warning' }}
                                                        {{ $quota['is_paid'] ? 'border-secondary bg-light' : '' }}"
                                                        style="transition: all 0.3s ease;">
                                                        
                                                        <div class="card-body p-3">
                                                            {{-- Header de la Cuota --}}
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="card-title mb-0 font-weight-bold text-dark">
                                                                    {{ $quota['name'] }}
                                                                </h6>
                                                                @if ($quota['is_paid'])
                                                                    <span class="badge badge-success">PAGADA</span>
                                                                @else
                                                                    <span class="badge badge-danger">VENCIDA</span>
                                                                @endif
                                                            </div>

                                                            {{-- Información de Vencimiento --}}
                                                            <div class="mb-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar-times mr-1"></i>
                                                                    Vencida: {{ \Carbon\Carbon::parse($quota['date_expiration'])->format('d/m/Y') }}
                                                                </small>
                                                            </div>

                                                            {{-- Montos --}}
                                                            <div class="mb-3">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <span class="font-weight-bold text-danger">
                                                                        Bs {{ number_format($quota['pending_amount'], 2) }}
                                                                    </span>
                                                                    <small class="text-muted">
                                                                        USD {{ number_format($quota['pending_exchange_amount'], 2) }}
                                                                    </small>
                                                                </div>
                                                                @if (!$quota['is_paid'] && $quota['pending_exchange_amount'] != $quota['total_exchange_amount'])
                                                                    <small class="text-info d-block mt-1">
                                                                        <i class="fas fa-info-circle mr-1"></i>
                                                                        Pago parcial realizado
                                                                    </small>
                                                                @endif
                                                            </div>

                                                            {{-- Checkbox de Selección --}}
                                                            @if (!$quota['is_paid'])
                                                                <div class="form-check">
                                                                    <input 
                                                                        type="checkbox" 
                                                                        wire:model="selectedOverdueQuotas"
                                                                        value="{{ $quota['id'] }}_{{ $studentData['student']['id'] }}" 
                                                                        class="form-check-input"
                                                                        id="overdue_{{ $quota['id'] }}_{{ $studentData['student']['id'] }}"
                                                                        style="transform: scale(1.2);">
                                                                    <label 
                                                                        class="form-check-label font-weight-bold text-success" 
                                                                        for="overdue_{{ $quota['id'] }}_{{ $studentData['student']['id'] }}">
                                                                        Seleccionar para pago
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="text-center">
                                                                    <span class="badge badge-secondary">CUOTA PAGADA</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Resumen de Selección --}}
                            @if (count($selectedOverdueQuotas) > 0)
                                <div class="alert alert-success mt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-check-circle mr-2"></i>
                                            <strong>{{ count($selectedOverdueQuotas) }} cuota(s) vencida(s) seleccionada(s)</strong>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-success py-2 px-3">
                                                Total seleccionado: Bs {{ number_format($this->calculateSelectedOverdueTotal(), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @else
                            <div class="alert alert-success text-center py-4">
                                <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                <h5 class="font-weight-bold">¡Excelente!</h5>
                                <p class="mb-0">No hay cuotas vencidas para este representante.</p>
                            </div>
                        @endif
                    </div>
                </div>
        @elseif($currentStep == 4)
        {{-- Paso 4: Cuotas No Vencidas --}}
        <div class="card shadow-sm rounded-lg">
            <div class="card-header alert-light text-primary font-weight-bold py-3">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Paso 4: Cuotas No Vencidas
                </h5>
            </div>
            <div class="card-body p-4">
                @if ($notDueQuotasByStudent->count() > 0)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Seleccione las cuotas no vencidas que desea cancelar por adelantado</strong>
                    </div>

                    {{-- Grid Responsive --}}
                    <div class="row">
                        @foreach ($notDueQuotasByStudent as $studentId => $studentData)
                            <div class="col-12 mb-4">
                                {{-- Header del Estudiante --}}
                                <div class="student-header bg-light p-3 rounded mb-3">
                                    <h6 class="mb-0 font-weight-bold text-primary">
                                        <i class="fas fa-user-graduate mr-2"></i>
                                        {{ $studentData['student']['name'] }} {{ $studentData['student']['lastname'] }}
                                    </h6>
                                    <small class="text-muted">
                                        CI: {{ $studentData['student']['ci_estudiant'] ?? 'N/A' }}
                                    </small>
                                </div>

                                {{-- Grid de Cuotas --}}
                                <div class="row">
                                    @foreach ($studentData['quotas'] as $quota)
                                        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                            <div class="card quota-card h-100 
                                                {{ in_array($quota['id'] . '_' . $studentData['student']['id'], $selectedNotDueQuotas) ? 'border-success selected-quota' : 'border-info' }}
                                                {{ $quota['is_paid'] ? 'border-secondary bg-light' : '' }}"
                                                style="transition: all 0.3s ease;">
                                                
                                                <div class="card-body p-3">
                                                    {{-- Header de la Cuota --}}
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0 font-weight-bold text-dark">
                                                            {{ $quota['name'] }}
                                                        </h6>
                                                        @if ($quota['is_paid'])
                                                            <span class="badge badge-success">PAGADA</span>
                                                        @else
                                                            <span class="badge badge-success">NO VENCIDA</span>
                                                        @endif
                                                    </div>

                                                    {{-- Información de Vencimiento --}}
                                                    <div class="mb-2">
                                                        <small class="text-success">
                                                            <i class="fas fa-calendar-check mr-1"></i>
                                                            Vence: {{ \Carbon\Carbon::parse($quota['date_expiration'])->format('d/m/Y') }}
                                                        </small>
                                                    </div>

                                                    {{-- Montos --}}
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="font-weight-bold text-success">
                                                                Bs {{ number_format($quota['pending_amount'], 2) }}
                                                            </span>
                                                            <small class="text-muted">
                                                                USD {{ number_format($quota['pending_exchange_amount'], 2) }}
                                                            </small>
                                                        </div>
                                                        @if (!$quota['is_paid'] && $quota['pending_exchange_amount'] != $quota['total_exchange_amount'])
                                                            <small class="text-info d-block mt-1">
                                                                <i class="fas fa-info-circle mr-1"></i>
                                                                Pago parcial realizado
                                                            </small>
                                                        @endif
                                                    </div>

                                                    {{-- Checkbox de Selección --}}
                                                    @if (!$quota['is_paid'])
                                                        <div class="form-check">
                                                            <input 
                                                                type="checkbox" 
                                                                wire:model="selectedNotDueQuotas"
                                                                value="{{ $quota['id'] }}_{{ $studentData['student']['id'] }}" 
                                                                class="form-check-input"
                                                                id="notdue_{{ $quota['id'] }}_{{ $studentData['student']['id'] }}"
                                                                style="transform: scale(1.2);">
                                                            <label 
                                                                class="form-check-label font-weight-bold text-success" 
                                                                for="notdue_{{ $quota['id'] }}_{{ $studentData['student']['id'] }}">
                                                                Seleccionar para pago
                                                            </label>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <span class="badge badge-secondary">CUOTA PAGADA</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Resumen de Selección --}}
                    @if (count($selectedNotDueQuotas) > 0)
                        <div class="alert alert-success mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <strong>{{ count($selectedNotDueQuotas) }} cuota(s) no vencida(s) seleccionada(s)</strong>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-success py-2 px-3">
                                        Total seleccionado: Bs {{ number_format($this->calculateSelectedNotDueTotal(), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                        <h5 class="font-weight-bold">Sin cuotas no vencidas</h5>
                        <p class="mb-0">No hay cuotas no vencidas disponibles para este representante.</p>
                    </div>
                @endif
            </div>
        </div>
        @elseif($currentStep == 5)
            {{-- Paso 5: Vista Previa --}}
            <div class="card shadow-sm rounded-lg">
                <div class="card-header alert-light text-primary font-weight-bold py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-eye mr-2"></i>
                        Paso 5: Vista Previa del Pago
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8 mb-4 mb-md-0">
                            Resumen del pago
                            <div class="payment-summary p-4 shadow-sm rounded-lg">
                                <h6 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Resumen del Pago</h6>

                                Datos del ingreso
                                <div class="mb-4">
                                    <strong class="d-block mb-2 text-dark">Ingreso Reportado:</strong>
                                    <ul class="list-unstyled ml-3 mb-0">
                                        <li><strong>Método:</strong>
                                            <span
                                                class="text-muted">{{ $paymentSummary['ingreso']['method_pay'] ?? '' }}</span>
                                        </li>
                                        @if ($paymentSummary['ingreso']['banco'])
                                            <li><strong>Banco:</strong> <span
                                                    class="text-muted">{{ $paymentSummary['ingreso']['banco'] }}</span>
                                            </li>
                                        @endif
                                        <li><strong>Referencia:</strong>
                                            <span
                                                class="text-muted">{{ $paymentSummary['ingreso']['number_i_pay'] ?? '' }}</span>
                                        </li>
                                        <li><strong>Fecha:</strong>
                                            <span
                                                class="text-muted">{{ $paymentSummary['ingreso']['date_transaction'] ?? '' }}</span>
                                        </li>
                                        <li><strong>Monto:</strong>
                                            <span class="text-success font-weight-bold">Bs.
                                                {{ number_format($paymentSummary['ingreso']['ingreso_ammount'] ?? 0, 2) }}</span>
                                            <small class="text-muted">[USD
                                                {{ number_format($paymentSummary['ingreso']['exchange_ammount'] ?? 0, 2) }}]</small>
                                        </li>
                                    </ul>
                                </div>

                                Recursos utilizados
                                @if (count($paymentSummary['resources'] ?? []) > 0)
                                    <div class="mb-4">
                                        <strong class="d-block mb-2 text-dark">Recursos Utilizados:</strong>
                                        <ul class="list-unstyled ml-3 mb-0">
                                            @foreach ($paymentSummary['resources'] as $resource)
                                                <li><span class="text-info">{{ $resource['type'] }}</span>:
                                                    <span class="text-success font-weight-bold">Bs.
                                                        {{ number_format($resource['amount'], 2) }}</span>
                                                    <small class="text-muted">[USD
                                                        {{ number_format($resource['exchange_amount'], 2) }}]</small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                Cuotas a cancelar
                                @if (count($paymentSummary['quotas'] ?? []) > 0)
                                    <div class="mb-4">
                                        <strong class="d-block mb-2 text-dark">Cuotas a Cancelar:</strong>
                                        <ul class="list-unstyled ml-3 mb-0">
                                            @foreach ($paymentSummary['quotas'] as $quota)
                                                <li class="mb-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span
                                                                class="font-weight-bold">{{ $quota['name'] }}</span>
                                                            <small
                                                                class="text-muted">({{ $quota['student'] }})</small>
                                                        </div>
                                                        <div>
                                                            <span
                                                                class="badge badge-{{ $quota['status'] == 'Vencida' ? 'danger' : 'success' }} py-1 px-2 rounded-sm">
                                                                Bs. {{ number_format($quota['amount'], 2) }}
                                                            </span>
                                                            <span
                                                                class="badge badge-{{ $quota['status'] == 'Vencida' ? 'dark' : 'success' }} py-1 px-2 rounded-sm">
                                                                USD {{ number_format($quota['exchange_amount'], 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                Totales
                                <div class="border-top pt-3">
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <strong class="d-block text-dark">Total Ingresos:</strong>
                                            <span class="text-success font-weight-bold border rounded p-1">Bs.
                                                {{ number_format($paymentSummary['totals']['total_income'] ?? 0, 2) }}</span>
                                            <span class="text-dark font-weight-bold border rounded p-1">USD
                                                {{ number_format($paymentSummary['totals']['total_income_exchange'] ?? 0, 2) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong class="d-block text-dark">Total Cuotas:</strong>
                                            <span class="badge badge-warning py-1 px-2 rounded-sm">Bs.
                                                {{ number_format($paymentSummary['totals']['total_quotas'] ?? 0, 2) }}</span>
                                            <span class="badge badge-dark py-1 px-2 rounded-sm">USD
                                                {{ number_format($paymentSummary['totals']['total_exchange_quotas'] ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong class="d-block text-dark">Recursos Disponibles:</strong>
                                            <small class="text-muted d-block">
                                                Abonos:
                                                Bs.{{ number_format($availableAbonos->sum('abono_ammount'), 2) }}
                                                [USD
                                                {{ number_format($availableAbonos->sum('exchange_ammount'), 2) }}]
                                            </small>
                                            <small class="text-muted d-block">
                                                Créditos:
                                                Bs.
                                                {{ number_format($availableCreditosAFavor->sum('credito_ammount'), 2) }}
                                                [USD
                                                {{ number_format($availableCreditosAFavor->sum('exchange_ammount'), 2) }}]
                                            </small>
                                        </div>
                                        <div class="col-6">
                                            <strong class="d-block text-dark">Saldo:</strong>
                                            <span
                                                class="badge badge-{{ ($paymentSummary['totals']['balance'] ?? 0) >= 0 ? 'success' : 'danger' }} ml-2 py-1 px-2 rounded-sm">
                                                Bs. {{ number_format($paymentSummary['totals']['balance'] ?? 0, 2) }}
                                            </span>
                                            <span
                                                class="badge badge-{{ ($paymentSummary['totals']['balance'] ?? 0) >= 0 ? 'success' : 'dark' }} ml-2 py-1 px-2 rounded-sm">
                                                USD
                                                {{ number_format($paymentSummary['totals']['exchange_balance'] ?? 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <span class="font-weight-bold">Ticket de caja</span>
                             <small>Vista preliminar</small>
                            <div class="ticket-preview p-4 shadow-sm rounded-lg">
                                <div class="text-center mb-3">
                                    <div class="header">
                                        <img class="logo img-fluid mb-2"
                                            src="{{ asset('images/avatar/uecfla.jpg') }}" alt="Logo"
                                            style="max-width: 72px; max-height: 72px;">
                                        <div class="institution-name font-weight-bold text-uppercase mb-1">
                                            {{ $institucion->name ?? 'INSTITUCIÓN EDUCATIVA' }}</div>
                                        <div class="ministry small text-muted font-weight-bold">DIRECCIÓN DE
                                            ADMINISTRACIÓN</div>
                                    </div>
                                    <small class="d-block mt-2 font-weight-bold">RECIBO DE PAGO</small>
                                </div>

                                <div class="text-center mb-3">
                                    <small class="text-muted">{{ now()->format('d/m/Y H:i:s') }}</small>
                                </div>

                                <div class="mb-2">
                                    <strong class="d-block">REPRESENTANTE:</strong>
                                    <span class="text-muted">{{ $representants[$representant_id] ?? '' }}</span>
                                </div>

                                <div class="mb-2">
                                    <strong class="d-block">MÉTODO DE PAGO:</strong>
                                    <span
                                        class="text-muted">{{ $paymentSummary['ingreso']['method_pay'] ?? '' }}</span>
                                </div>

                                <div class="mb-2">
                                    <strong class="d-block">REFERENCIA:</strong>
                                    <span
                                        class="text-muted">{{ $paymentSummary['ingreso']['number_i_pay'] ?? '' }}</span>
                                </div>

                                <hr class="my-3" style="border-top: 1px dashed #000;">

                                <div class="mb-2">
                                    <strong class="d-block">RECURSOS UTILIZADOS:</strong>
                                </div>

                                Ingreso principal
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Ingreso Reportado</span>
                                    <span class="font-weight-bold">Bs
                                        {{ number_format($paymentSummary['ingreso']['ingreso_ammount'] ?? 0, 2) }}</span>
                                </div>

                                Recursos adicionales
                                @foreach ($paymentSummary['resources'] ?? [] as $resource)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">{{ $resource['type'] }}</span>
                                        <span class="font-weight-bold">Bs
                                            {{ number_format($resource['amount'], 2) }}</span>
                                    </div>
                                @endforeach

                                <hr class="my-3" style="border-top: 1px dashed #000;">

                                <div class="mb-2">
                                    <strong class="d-block">CONCEPTOS A PAGAR:</strong>
                                </div>

                                @foreach ($paymentSummary['quotas'] ?? [] as $quota)
                                    <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                        <div class="flex-grow-1">
                                            <span class="text-dark font-weight-bold d-block">{{ $quota['name'] }}</span>
                                            <small class="text-muted d-block">
                                                {{-- {{ $quota['student'] }}  --}}
                                                @if($quota['ci_estudiant'] ?? false)
                                                    <span class="badge badge-light ml-1">{{ $quota['ci_estudiant'] }}</span>
                                                @endif
                                            </small>
                                            <small class="d-block">
                                                @if($quota['status'] == 'Vencida')
                                                    <span class="badge badge-danger">Vencida</span>
                                                @else
                                                    <span class="badge badge-success">No Vencida</span>
                                                @endif
                                                @if(($quota['pending_amount'] ?? 0) > $quota['amount'])
                                                    <span class="badge badge-warning ml-1">Pago parcial</span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-weight-bold d-block text-primary">Bs {{ number_format($quota['amount'], 2) }}</span>
                                            <small class="text-muted">USD {{ number_format($quota['exchange_amount'], 2) }}</small>
                                            @if(($quota['pending_amount'] ?? 0) > $quota['amount'])
                                                <small class="d-block text-warning">
                                                    <i class="fas fa-info-circle"></i> Pendiente: Bs {{ number_format($quota['pending_amount'], 2) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <hr class="my-3" style="border-top: 1px dashed #000;">

                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="text-dark">TOTAL RECURSOS:</strong>
                                    <strong class="text-success">Bs
                                        {{ number_format($paymentSummary['totals']['total_income'] ?? 0, 2) }}</strong>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="text-dark">TOTAL PAGADO:</strong>
                                    <strong class="text-success">Bs
                                        {{ number_format($paymentSummary['totals']['total_income'] ?? 0, 2) }}</strong>
                                </div>

                                @if (($paymentSummary['totals']['balance'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between mt-2">
                                        <strong class="text-dark">SALDO A FAVOR:</strong>
                                        <strong class="text-info">Bs
                                            {{ number_format($paymentSummary['totals']['balance'], 2) }}</strong>
                                    </div>
                                @endif

                                <div class="text-center mt-4">
                                    <small class="font-weight-bold text-uppercase">¡GRACIAS POR SU PAGO!</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($currentStep == 6)
            {{-- Paso 6: Confirmación --}}
            <div class="card shadow-sm rounded-lg">
                <div class="card-header alert-light text-primary font-weight-bold py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-check-circle mr-2"></i>
                        Paso 6: Confirmación Final
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    <div class="alert alert-warning shadow-sm rounded mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>¡Atención!</strong> Una vez confirmado, el pago será registrado en el sistema y no podrá
                        ser modificado.
                    </div>

                    <p class="lead font-weight-bold mb-4">¿Está seguro de que desea registrar este pago?</p>

                    <div class="mt-4 d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
                        <button type="button" wire:click="confirmPayment"
                            class="btn btn-success btn-lg px-5 py-3 font-weight-bold shadow-lg mb-3 mb-md-0 rounded-sm"
                            style="font-size: 1.5rem; border-width: 3px; box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);">
                            <i class="fas fa-check fa-lg mr-2"></i>
                            Confirmar y Registrar Pago
                        </button>
                    </div>

                    <button type="button" wire:click="previousStep"
                        class="btn btn-outline-secondary btn-md ml-md-4 mt-3 rounded-sm" style="opacity: 0.8;">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Revisar Nuevamente
                    </button>
                </div>
            </div>
        @elseif($currentStep == 7)
            {{-- Paso 7: Presentación de Recibo de Pago --}}
            <div class="card shadow-sm rounded-lg">
                <div class="card-header alert-light text-primary font-weight-bold py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-receipt mr-2"></i>
                        Paso 7: Recibo de Pago
                    </h5>
                </div>
                <div class="card-body p-4 alert-secondary">
                    @if ($registroPagoCombinado)
                        <div id="printable-ticket-area p-2">
                            @include('livewire.administracion.registro-pago.tickets', [
                                'registro_pago_combinado' => $registroPagoCombinado,
                                'institucion' => $institucion,
                                'pescolar_name' => $pescolar_name,
                            ])
                        </div>
                    @else
                        <div class="alert alert-warning">No se pudo cargar el recibo de pago.</div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <button type="button" wire:click="startNewPayment"
                        class="btn btn-primary btn-lg rounded-sm rounded-sm">
                        <i class="fas fa-plus mr-2"></i> Iniciar Nuevo Pago
                    </button>
                </div>
            </div>
        @endif

        {{-- Botones de navegación --}}
        <div class="d-flex justify-content-between mt-5">
            <div>
                @if ($currentStep > 1 && $currentStep < 7)
                    {{-- Show "Anterior" on steps 2, 3, 4, 5, 6 --}}
                    <button type="button" wire:click="previousStep" class="btn btn-secondary px-4 py-2 rounded-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Anterior
                    </button>
                @endif
            </div>

            <div>
                @if ($currentStep < 6)
                    <button type="button" wire:click="nextStep" class="btn btn-primary px-4 py-2 rounded-sm">
                        Siguiente
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                @endif
            </div>
        </div>

    </fieldset>


    @section('stylesheet')
        @parent
        <style>
            /* Estilos para las cards de cuotas */
            .quota-card {
                transition: all 0.3s ease;
                border-width: 2px;
            }

            .quota-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }

            .selected-quota {
                background-color: #f8fff8;
                border-color: #28a745 !important;
            }

            .student-header {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-left: 4px solid #007bff;
            }

            /* Grid responsive */
            @media (max-width: 768px) {
                .quota-card .card-body {
                    padding: 1rem;
                }
            }

            /* Mejora visual para los checkboxes */
            .form-check-input:checked {
                background-color: #28a745;
                border-color: #28a745;
            }
        </style>
    @endsection

    @section('stylesheet')
        @parent
        <style>
        /* Estilos para pantalla */
        .receipts-container {
            display: none; /* Ocultar en vista normal */
        }

        /* Estilos para impresión */
        @media print {
            .no-print { 
                display: none !important; 
            }
            
            body * { 
                visibility: hidden; 
            }
            
            #printable-ticket-area, #printable-ticket-area * { 
                visibility: visible; 
            }
            
            #printable-ticket-area { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%;
                height: 100%;
                display: block !important;
            }
            
            .receipts-container {
                display: flex !important;
                justify-content: center;
                align-items: flex-start;
                gap: 20px;
                padding: 20px;
                height: 100vh;
                page-break-inside: avoid;
            }
            
            .receipt-copy {
                width: 48% !important;
                max-width: 80mm !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .ticket { 
                box-shadow: none !important; 
                border: 1px solid #000 !important;
                margin: 0 !important;
                font-family: 'Courier New', monospace;
                font-size: 12px;
            }
        }

        /* Estilos para vista previa en pantalla */
        @media screen {
            .receipts-container {
                display: flex;
                justify-content: center;
                gap: 20px;
                flex-wrap: wrap;
                margin: 20px 0;
            }
            
            .receipt-copy {
                width: 45%;
                max-width: 80mm;
                border: 1px solid #ddd;
                padding: 10px;
                background: white;
            }
        }
        </style>
    @endsection

    @section('scripts')
        @parent
        <script>
        function printReceipt() {
            console.log('Imprimiendo dos copias del recibo...');
            
            // Forzar vista de impresión
            const originalStyles = document.querySelector('style').innerHTML;
            const printStyles = `
                @media print {
                    body { margin: 0; padding: 0; }
                    .receipts-container { 
                        display: flex !important; 
                        justify-content: center;
                        align-items: flex-start;
                        gap: 20px;
                        height: 100vh;
                    }
                    .receipt-copy { 
                        width: 48% !important; 
                        max-width: 80mm !important;
                    }
                }
            `;
            
            // Agregar estilos temporalmente
            const styleSheet = document.createElement("style");
            styleSheet.type = "text/css";
            styleSheet.innerText = printStyles;
            document.head.appendChild(styleSheet);
            
            window.print();
            
            // Limpiar estilos después de imprimir
            document.head.removeChild(styleSheet);
        }

        window.printReceipt = printReceipt;
        </script>
    @endsection

</fieldset>
</div>
