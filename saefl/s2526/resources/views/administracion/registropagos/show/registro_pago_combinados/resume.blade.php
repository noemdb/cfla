@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_representant }}">FEC. REGISTRO</th>
                <th class="{{ $class_action }}">CONCEPTOS</th>
                {{-- <th class="{{ $class_action }}">OBSERVACIONES</th> --}}
                <th class="{{ $class_grado }} text-right">RECURSO <span class="small">(Bs.)</span></th>
                <th class="{{ $class_grado }} text-right">PAGADO <span class="small">(Bs.)</span></th>
                <th class="{{ $class_grado }} text-right">CAF <span class="small">(Bs.)</span></th>
            </tr>
        </thead>

        <tbody id="tdatos">

            <tr data-id="{{$registro_pago_combinado->id}}" class="rounded-bottom {{ ($registro_pago_combinado->status_irregular) ? 'table-danger': null}}">

                @php  $registropagos = $registro_pago_combinado->registropagos; @endphp
                @php  $representant = $registro_pago_combinado->representant; @endphp

                <td>
                    {{f_date($registro_pago_combinado->created_at)}}
                    @admin [{{$registro_pago_combinado->id ?? ''}}] @endadmin
                </td>

                <td>
                    <dl class="mb-0 small">
                        @foreach ($registropagos as $registropago)
                            <dd>
                                <b>{{$registropago->cuentaxpagar->name ?? ''}}</b>
                                @admin [{{$registropago->id ?? ''}}] @endadmin
                                <span class="small">
                                    {{$registropago->estudiant->fullname ?? ''}}
                                </span>
                            </dd>
                        @endforeach
                    </dl>
                </td>

                <td class="align-bottom text-right">
                    <dl class="mb-0 small text-muted">
                        @php $recursos = 0 @endphp
                        @if (!empty($registro_pago_combinado->ammount_ingresos))
                            @php $sum_ammont_ingresos = $registro_pago_combinado->ammount_ingresos; @endphp
                            @php $recursos = $recursos + $sum_ammont_ingresos; @endphp

                            @foreach ($registro_pago_combinado->ingresos as $ingreso)
                                <dd class="mb-0 pb-0">{{$loop->iteration}}. ING: {{ f_float($ingreso->ingreso_ammount) ?? '' }}</dd>
                            @endforeach

                        @endif
                        @if (!empty($registro_pago_combinado->ammount_abonos_aplicados))
                            @php $ammount_abonos_aplicados = $registro_pago_combinado->ammount_abonos_aplicados; @endphp
                            @php $recursos = $recursos + $ammount_abonos_aplicados; @endphp
                            <dd class="mb-0 pb-0">ABN: {{ f_float($ammount_abonos_aplicados) ?? '' }}</dd>
                        @endif
                        @if (!empty($registro_pago_combinado->ammount_creditos_aplicados))
                            @php $ammount_creditos_aplicados = $registro_pago_combinado->ammount_creditos_aplicados; @endphp
                            @php $recursos = $recursos + $ammount_creditos_aplicados; @endphp
                            <dd class="mb-0 pb-0">CAF: {{ f_float($ammount_creditos_aplicados) ?? '' }}</dd>
                        @endif
                    </dl>
                    <hr class="m-0 p-0">
                    <span class="font-weight-bold text-dark small">
                        + {{ f_float($recursos) }}
                    </span>
                </td>

                <td class="align-bottom text-right">
                    <dl class="mb-0 small text-muted">
                        @foreach ($registropagos as $registropago)
                            <dd class="mb-0 pb-0">
                                {{f_float($registropago->pagos->sum('pagos_ammount'))}}
                            </dd>
                        @endforeach
                    </dl>
                    <hr class="m-0 p-0">
                    <span class="font-weight-bold text-dark small">
                        - {{ f_float($registro_pago_combinado->ammount_pagado) }}
                    </span>
                </td>

                <td class="align-bottom text-right">
                    <hr class="m-0 p-0">
                    <span class="font-weight-bold text-dark small">
                        = {{ f_float($registro_pago_combinado->ammount_creditos_generados) }}
                    </span>
                </td>

            </tr>

        </tbody>

    </table>
