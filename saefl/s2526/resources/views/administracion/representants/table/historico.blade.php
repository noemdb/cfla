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
                <th class="{{ $class_grado }} text-right">RECURSO</th>
                <th class="{{ $class_grado }} text-right">PAGADO</th>
                <th class="{{ $class_grado }} text-right">CAF</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($pago_combinados as $pago_combinado)

                @php
                    $registropagos = $pago_combinado->registropagos;
                @endphp

                <tr data-id="{{$pago_combinado->id}}" class="rounded-bottom {{ ($pago_combinado->status_irregular) ? 'table-danger': null}}">

                    <td class=" text-nowrap">
                        <div class="d-flex justify-content-center align-items-center font-weight-bold small">
                            {{$pago_combinado->created_at->format('d-m-Y h:i')}} @admin [{{$pago_combinado->id ?? ''}}] @endadmin
                        </div>
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
                            @php
                                $recursos = 0;
                                $recursos_exchange = 0;
                            @endphp
                            @if (!empty($pago_combinado->ammount_ingresos))
                                @php
                                    $sum_ammont_ingresos = $pago_combinado->ammount_ingresos;
                                    $sum_ammont_ingresos_exchange = $pago_combinado->ammount_ingresos_exchange;
                                    $recursos = $recursos + $sum_ammont_ingresos;
                                    $recursos_exchange = $recursos_exchange + $sum_ammont_ingresos_exchange;
                                @endphp

                                @foreach ($pago_combinado->ingresos as $ingreso)
                                    <dd class="mb-0 pb-0">
                                        @php $ingreso_ammount = $ingreso->ingreso_ammount; $ammount_exchange = $ingreso->ammount_exchange;@endphp
                                        {{$loop->iteration}}. ING: {{ f_float($ingreso_ammount) ?? '' }} | $ {{ f_float($ammount_exchange)}}
                                    </dd>
                                @endforeach

                            @endif
                            @if (!empty($pago_combinado->ammount_abonos_aplicados))
                                @php
                                    $ammount_abonos_aplicados = $pago_combinado->ammount_abonos_aplicados;
                                    $ammount_abonos_aplicados_exchange = $pago_combinado->ammount_abonos_aplicados_exchange;
                                    $recursos = $recursos + $ammount_abonos_aplicados;
                                    $recursos_exchange = $recursos_exchange + $ammount_abonos_aplicados_exchange;

                                @endphp
                                <dd class="mb-0 pb-0">
                                    ABN: {{ f_float($ammount_abonos_aplicados) ?? '' }} | $ {{ f_float($ammount_abonos_aplicados_exchange)}}
                                </dd>
                            @endif
                            @if (!empty($pago_combinado->ammount_creditos_aplicados))
                                @php
                                    $ammount_creditos_aplicados = $pago_combinado->ammount_creditos_aplicados;
                                    $ammount_creditos_aplicados_exchange = $pago_combinado->ammount_creditos_aplicados_exchange;
                                    $recursos = $recursos + $ammount_creditos_aplicados;
                                    $recursos_exchange = $recursos_exchange + $ammount_creditos_aplicados_exchange;
                                @endphp
                                <dd class="mb-0 pb-0">CAF: {{ f_float($ammount_creditos_aplicados) ?? '' }} | $ {{ f_float($ammount_creditos_aplicados_exchange)}}</dd>
                            @endif
                        </dl>
                        <hr class="m-0 p-0">
                        <span class="font-weight-bold text-dark small">
                            + {{ f_float($recursos) }} | $ {{ f_float($recursos_exchange)}}
                        </span>
                    </td>

                    <td class="align-bottom text-right">
                        <dl class="mb-0 small text-muted">
                            @foreach ($registropagos as $registropago)
                                <dd class="mb-0 pb-0">
                                    {{f_float($registropago->pagos->sum('pagos_ammount'))}} | $ {{ f_float($registropago->pagos->sum('exchange_ammount'))}}
                                </dd>
                            @endforeach
                        </dl>
                        <hr class="m-0 p-0">
                        <span class="font-weight-bold text-dark small">
                            - {{ f_float($pago_combinado->ammount_pagado) }} | $ {{ f_float($pago_combinado->ammount_pagado_exchange)}}
                        </span>
                    </td>

                    <td class="align-bottom text-right">
                        <hr class="m-0 p-0">
                        <span class="font-weight-bold text-dark small">
                            = {{ f_float($pago_combinado->ammount_creditos_generados) }}  | $ {{ f_float($pago_combinado->ammount_creditos_generados_exchange)}}
                        </span>
                    </td>

                    <td class="{{ $class_action ?? '' }} text-center align-middle" id="btn-action-{{ $representant->id }}">

                        <div class="btn-group" role="group" aria-label="Basic example">

                            <a title="Mostrar detalles del registro de pago combinado" class="btn-modal btn btn-info btn-sm" href="#">
                                <i class="{{ $icon_menus['show'] ?? '' }} fa-1x"></i>
                            </a>

                            <a target="_blank" title="Recibo de Pago" class="btn btn-dark btn-sm {{ ($pago_combinado->status_irregular) ? 'disabled': null}}" href="{{ route('administracion.representants.recibo.pdf',$pago_combinado->id) }}">
                                <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>

                    </td>

                </tr>

            @endforeach

        </tbody>
    </table>

    <div id="container_modal"></div>

@section('scripts')
    @parent
    <script>
        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.registro_pago_combinado", "_id_")}}';
            var modal = '#modal_registropago';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            ajaxurl = ajaxurl.replace('_id_', id);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });
    </script>
@endsection
