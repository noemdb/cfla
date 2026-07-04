<div class="container-fluid my-2">
    <h4 class="mb-2 font-weight-bold text-primary">
        <i class="fas fa-exclamation-triangle"></i> Generar Recargo por Morosidad
    </h4>
    <hr>

    {{-- Mensajes flash --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if ($errors->has('general'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first('general') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- BUSCADOR --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Búsqueda de Estudiante</strong>
        </div>
        <div class="card-body">
            <input wire:model.debounce.500ms="search" type="text" class="form-control"
                placeholder="Buscar por nombre, apellido o cédula...">
            @if (!empty($students))
                <ul class="list-group mt-2 shadow-sm">
                    @foreach ($students as $s)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="font-weight-bold">{{ $s->name }} {{ $s->lastname }}</div>
                                <small class="text-muted">CI: {{ $s->ci_estudiant }} @admin
                                        — ID: {{ $s->id }}
                                    @endadmin</small>
                            </div>
                            <button wire:click="selectStudent({{ $s->id }})" class="btn btn-sm btn-primary">
                                Seleccionar
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- ESTUDIANTE SELECCIONADO --}}
    @if ($selectedEstudiant)
        <div class="card mb-4 border-info">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-user"></i>
                    {{ $selectedEstudiant->name }} {{ $selectedEstudiant->lastname }}
                    <small class="d-block">CI: {{ $selectedEstudiant->ci_estudiant }}</small>
                </span>
                {{--
                <button
                    wire:click="$set('selectedEstudiant', null); $set('selectedEstudiantId', null); $set('quotas', []);"
                    class="btn btn-sm btn-light">
                    Cambiar
                </button>
                --}}
            </div>
        </div>

        {{-- CUOTAS VENCIDAS --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">CUOTAS VENCIDAS.</h5>
                {{-- <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6> --}}
                <div class="card-text">
                    <div class="row">
                        @forelse ($quotas as $quota)
                            @php
                                $montoAdeudado = method_exists($quota, 'TotalExchangeMontoCuentasXPagarAdeudado')
                                    ? $quota->TotalExchangeMontoCuentasXPagarAdeudado($selectedEstudiantId)
                                    : $quota->conceptopagos()->sum('exchange_ammount') -
                                        $quota->conceptocancelados()->sum('exchange_ammount');

                                $status_late_payment = $quota->status_late_payment;
                            @endphp

                            @if ($montoAdeudado > 0)
                                <div class="col-md-6 mb-3">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title mb-1">
                                                <i class="fas fa-file-invoice"></i> {{ $quota->name }}
                                            </h6>
                                            <p class="card-text text-muted small mb-2">
                                                <i class="far fa-calendar-alt"></i> Vence:
                                                {{ $quota->date_expiration }} <br>
                                                <i class="far fa-clock"></i> Mora desde:
                                                {{ $quota->date_late_payment ?? '—' }}
                                            </p>
                                            <p class="mb-2">
                                                <span class="badge badge-pill badge-danger">
                                                    Adeudado: USD {{ number_format($montoAdeudado ?? 0, 2) }}
                                                </span>
                                            </p>
                                            <div class="mt-auto">
                                                @if ($status_late_payment)
                                                    <div class="alert alert-secondary">
                                                        Recargo por morosidad
                                                    </div>
                                                @else
                                                    <button wire:click="selectQuota({{ $quota->id }})"
                                                        class="btn btn-sm btn-warning btn-block">
                                                        <i class="fas fa-plus-circle"></i> Generar Recargo
                                                    </button>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-12">
                                <div class="alert alert-secondary">
                                    <i class="fas fa-info-circle"></i> No se encontraron cuotas vencidas.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DE CONFIRMACIÓN --}}
    @if ($confirming)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.6);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle"></i> Confirmar Recargo
                        </h5>
                        <button type="button" class="close" aria-label="Cerrar"
                            wire:click="$set('confirming', false)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- <p><strong>Meses de mora:</strong> {{ $mesesMora }}</p> --}}
                        <p><strong>Monto original:</strong>USD {{ number_format($montoOriginal, 2) }}</p>
                        <p><strong>Recargo calculado:</strong>
                            <span class="text-danger font-weight-bold">USD {{ number_format($recargoTotal, 2) }}</span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="generarRecargo" class="btn btn-success">
                            <i class="fas fa-check"></i> Confirmar y Guardar
                        </button>
                        <button wire:click="$set('confirming', false)" class="btn btn-outline-secondary">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
