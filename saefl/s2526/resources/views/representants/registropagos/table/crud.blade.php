@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="d-none";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp


<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_fecha }}" title="Fecha del Registro de Pago">Fecha</th>
            <th class="{{ $class_deuda }}" title="Concepto de Cobro">Cuota</th>
            <th class="{{ $class_grado }} alert-info">Pagado</th>
            {{-- <th class="{{ $class_action }} text-center">Acción</th> --}}
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($registropagos as $registropago)

        @php
            $estudiant = $registropago->estudiant;
            $representant = $registropago->representant;
        @endphp

        <tr data-id="{{$registropago->id}}" data-representant_id="{{$representant->id ?? ''}}">

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td class="{{ $class_ci ?? '' }}">
                {{ $registropago->created_at->format('d-m-Y') ?? ''}}
            </td>

            <td class="{{ $class_planpago ?? '' }}">
                {{$registropago->cuentaxpagar->name ?? ''}}
            </td>

            <td class="{{ $class_grado ?? '' }} alert-info">
                {{ ($registropago->total_exchange_pagos_ammount) ? '$ '.f_float($registropago->total_exchange_pagos_ammount) : 'Bs '.f_float($registropago->total_pagos_ammount)}}
            </td>

            {{-- <td class="{{ $class_action ?? '' }} text-center align-middle" id="btn-action-{{ $representant->id }}">

                <div class="btn-group" role="group" aria-label="Basic example">

                    <a target="_blank" title="Recibo de Pago" class="btn btn-outline-danger btn-sm" href="{{ route('representants.registropagos.recibos.pagos.representant.pdf',$registropago->cuentaxpagar_id) }}">
                        <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                    </a>

                </div>

            </td> --}}

        </tr>

        @endforeach

    </tbody>
</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
