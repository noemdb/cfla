<div class="btn-group-vertical btn-group-sm" role="group" aria-label="Basic example" style="display:inline !important">
    <a title="Editar datos del representante" class="btn btn-warning btn-sm"
        href="{{ route('administracion.representants.edit', ['id' => $representant->id]) }}" role="button">
        {{-- Representante --}}
        <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>

    <a title="Ver representados" class="btn btn-danger btn-sm"
        href="{{ route('administracion.estudiants.index', ['search' => $representant->ci_representant]) }}" role="button">
        {{-- Representante --}}
        <i class="{{ $icon_menus['estudiante'] }} fa-1x"></i>
    </a>

    @if (Auth::user()->IsAdmon())

        {{-- @admin --}}

        <a title="Iniciar Asistente de Registro de Pagos.." class="btn btn-secondary btn-sm"
            href="{{ route('administracion.registropagos.livewire.asistent', ['ci_representant' => $representant->ci_representant]) }}"
            role="button">
            <i class="{{ $icon_menus['registro_pagos'] }} fa-1x"></i>
        </a>

        {{-- @endadmin --}}

        <a title="Iniciar Asistente de Registro de Pagos.." class="btn btn-success btn-sm" {{-- href="{{ route('administracion.registropagos.asistent.representant.create',$representant->id) }}" --}}
            href="{{ route('administracion.registropagos.asistent.representant.create', ['id' => $representant->id]) }}"
            role="button">
            <i class="{{ $icon_menus['registro_pagos'] }} fa-1x"></i>
        </a>

        <div class="btn-group">
            <button class="btn btn-info btn-small p-0 dropdown-toggle" type="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a title="Histórico de pagos" class="dropdown-item"
                    href="{{ route('administracion.registropagos.crud', ['ci' => $representant->ci_representant]) }}"
                    role="button">
                    <i class="{{ $icon_menus['registropagos'] }} fa-1x text-dark"></i>
                    Listado de Reg. de Pagos
                </a>
                <a title="Histórico de pagos" class="dropdown-item"
                    href="{{ route('administracion.representants.historico', ['representant_id' => $representant->id]) }}"
                    role="button">
                    <i class="{{ $icon_menus['historico'] }} fa-1x text-info"></i>
                    Histórico de Pagos
                </a>
                <a title="Registrar Abonos" class="dropdown-item"
                    href="{{ route('administracion.abonos.index', ['search' => $representant->ci_representant]) }}"
                    role="button">
                    <i class="{{ $icon_menus['abonos'] }} fa-1x text-success"></i>
                    Registrar Abonos
                </a>
                <a title="Listado de Ingresos" class="dropdown-item"
                    href="{{ route('administracion.ingresos.crud', ['ci' => $representant->ci_representant]) }}"
                    role="button">
                    <i class="{{ $icon_menus['crud'] }} fa-1x text-dark"></i>
                    Listado de Ingresos
                </a>
                <a title="Listado de Abonos" class="dropdown-item"
                    href="{{ route('administracion.abonos.crud', ['ci_representant' => $representant->ci_representant]) }}"
                    role="button">
                    <i class="{{ $icon_menus['crud'] }} fa-1x text-dark"></i>
                    Listado de Abonos
                </a>
                <a title="Listado de Créditos a Favor" class="dropdown-item"
                    href="{{ route('administracion.creditoafavors.crud', ['ci' => $representant->ci_representant]) }}"
                    role="button">
                    <i class="{{ $icon_menus['crud'] }} fa-1x text-dark"></i>
                    Listado de CAF
                </a>
                <a title="Info. de Cobranza" class="dropdown-item" href="#" role="button" data-toggle="modal"
                    data-target="#billsModal{{ $representant->id ?? null }}">
                    <i class="{{ $icon_menus['coll_politicals'] }} fa-1x text-dark"></i>
                    Info. de Cobranza
                </a>

            </div>
        </div>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="billsModal{{ $representant->id ?? null }}" tabindex="-1"
        aria-labelledby="billsModalLabel{{ $representant->id ?? null }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="billsModalLabel{{ $representant->id ?? null }}">Información de cuotas
                        pendientes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('email.collections.partials.bills')
                </div>
            </div>
        </div>
    </div>

</div>
