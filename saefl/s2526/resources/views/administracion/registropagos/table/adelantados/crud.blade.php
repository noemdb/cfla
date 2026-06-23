@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="d-none";
    $class_ammount="d-none d-lg-table-cell text-right";
    $class_conceptos="d-none d-md-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp


<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_representant }}">Ident.</th>
            <th class="{{ $class_representant }}">REPRESENTANTE</th>
            <th class="{{ $class_ammount ?? null }}" title="Monto Total de los ingresos">Monto ingresado hasta <span class=" text-capitalize">{{ ($current_month) ? $current_month->format('F') : null}} [$]</span></th>
            <th class="{{ $class_ammount ?? null }}" title="Monto Total Pagado">Monto pagado hasta <span class=" text-capitalize">{{ ($current_month) ? $current_month->format('F') : null}} [$]</span></th>
            <th class="{{ $class_ammount ?? null }}" title="Monto Pagado siguiente cuota/mensualidad">Monto adelantado para <span class=" text-capitalize">{{ ($next_month) ? $next_month->format('F') : null}} [$]</span></th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @php
            $sum_ingresos = null;
            $sum_conceptos = null;
            $sum_advanced = null;
        @endphp

        @forelse($adelantados as $adelantado)

            @php
                $representant = $adelantado['representant'];
                $estudiants = $adelantado['estudiants'];
                $ingresos = $adelantado['ingresos'];
                $total_exchange_ammount_concepto_pagos = $adelantado['total_exchange_ammount_concepto_pagos'];
                $total_exchange_ammoun_ingreso = $adelantado['total_exchange_ammoun_ingreso'];
                $texchange_ammoun_advanced = $adelantado['texchange_ammoun_advanced'];
                $sum_ingresos += $total_exchange_ammoun_ingreso;
                $sum_conceptos += $total_exchange_ammount_concepto_pagos;
                $sum_advanced += $texchange_ammoun_advanced;
            @endphp

            <tr data-id="{{$representant->id}}">

                <td id="td-count" class="{{ $class_N ?? null }}">
                    {{$loop->iteration}}
                </td>

                <td  class="{{ $class_representant  ?? null }}">
                    {{ $representant->ci_representant ?? null }}
                </td>
                <td  class="{{ $class_representant  ?? null }}">
                    @include('administracion.elements.href.representant')
                </td>

                <td class="{{ $class_ammount ?? null }} table-success pr-3">
                    <span title="{{$total_exchange_ammoun_ingreso ?? null}}">
                        @php $round = ($total_exchange_ammount_concepto_pagos > 0.009) ? 2 : 8 ; @endphp
                        {{round($total_exchange_ammoun_ingreso,$round)}}
                    </span>
                </td>

                <td class="{{ $class_ammount ?? null }} table-primary pr-3">
                    <span title="{{$total_exchange_ammount_concepto_pagos ?? null}}">
                        @php $round = ($total_exchange_ammount_concepto_pagos > 0.009) ? 2 : 8 ; @endphp
                        {{round($total_exchange_ammount_concepto_pagos,$round)}}
                    </span>
                </td>

                <td class="{{ $class_ammount ?? null }} table-info pr-3">
                    <span title="{{$texchange_ammoun_advanced ?? null}}">
                        @php $round = ($texchange_ammoun_advanced > 0.009) ? 2 : 8 ; @endphp
                        {{round($texchange_ammoun_advanced,$round)}}
                    </span>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" class=" text-center text-muted font-weight-bold">NO HAY DATOS</td>
            </tr>

        @endforelse

    </tbody>
</table>

{{-- <div> {{$sum_ingresos ?? null}} || {{$sum_conceptos ?? null}} || {{$sum_advanced ?? null}}</div> --}}

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.simple') --}}
@include('administracion.datatables.exportBootstrap')
