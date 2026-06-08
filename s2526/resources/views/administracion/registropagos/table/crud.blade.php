@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="d-none text-nowrap";
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
            <th class="{{ $class_N }} text-center">Idents.</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            {{-- <th class="{{ $class_ci }}">Cédula</th> --}}

            {{-- <th class="{{ $class_planpago }}">Referencia</th> --}}
            {{-- <th class="{{ $class_planpago }}">Plan de Pago</th> --}}
            <th class="{{ $class_deuda }}" title="Concepto de Cobro">Concepto Cobro</th>
            {{-- <th class="{{ $class_grado }}">Pagado Bs</th> --}}
            <th class="{{ $class_grado }}">Pagado [Bs||USD]</th>
            <th class="{{ $class_grado }}" title="Crédito Generado">C.Generado [Bs||USD]</th>
            <th class="{{ $class_grado }}" title="Pago Combinado">P.C.</th>
            <th class="{{ $class_fecha }}">F.Registro</th>
            <th class="{{ $class_fecha }}">user</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($registropagos as $registropago)

            @if ($registropago->estudiant)

                @php
                    $estudiant = $registropago->estudiant;
                    $pago = $registropago->pago;
                    $registro_pago_combinado = ($registropago) ? $registropago->registro_pago_combinado : null;
                    $administrativa = $estudiant->administrativa;
                    $exchange_ammount = (!empty($pago->exchange_ammount)) ? round($pago->exchange_ammount,2):null;
                @endphp

        @if($exchange_ammount > 0)

                <tr class="{{ ($registropago->status_unexpired) ? 'table-info':null }}" data-id="{{$registropago->id}}" data-representant_id="{{$registropago->representant->id ?? ''}}" title="{{ ($registropago->status_unexpired) ? 'Registro Pago Adelantado':null }}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td id="td-count" class="{{ $class_N }}">
                        @if ($registropago)
                            <div class=" font-weight-bold text-nowrap">
                                <span class="border rounded btn-primary p-1">P{{ ($registropago) ? $registropago->id : null}}</span>
                                <span class="border rounded btn-warning p-1">C{{ ($registro_pago_combinado) ? $registro_pago_combinado->id : null}}</span>
                                <span class="border rounded btn-info p-1">F{{ ($registro_pago_combinado) ? $registro_pago_combinado->correlative : null}}</span>
                            </div>
                        @endif
                    </td>

                    <td  class="{{ $class_estudiant  ?? ''}}">
                        <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                                <b>{{$estudiant->fullname}}</b>
                            </span>
                        </a>
                        <br>
                        ({{ $estudiant->ci_estudiant ?? ''}})
                        ({{ $estudiant->representant->ci_representant ?? ''}})

                        @admin
                            <span class=" small font-weight-bold text-muted">
                                [H{{ (!empty($estudiant->representant->estudiants)) ? count($estudiant->representant->estudiants):''}}H]
                                [RP_ID: {{ $registropago->id ?? ''}}]
                                [RPC_ID: {{ $registropago->registro_pago_combinado->id ?? 'fallo'}}]
                            </span>
                        @endadmin
                        <div>-{{ ($administrativa) ? $administrativa->planpago->name : 'S.I.A'}}-</div>
                    </td>

                    <td class="{{ $class_planpago ?? '' }} ">
                        {{$registropago->cuentaxpagar->name ?? ''}}
                    </td>
                    <td class="{{ $class_grado ?? '' }}">
                        @php $exchange_ammount = (!empty($pago->exchange_ammount)) ? $pago->exchange_ammount:null; @endphp
                        @php $ammount = (!empty($pago->pagos_ammount)) ? $pago->pagos_ammount:null; @endphp
                        <span title="{{$ammount ?? ''}}"> {{ ($ammount) ? f_float($ammount,2) : BsBsBs }} </span> ||
                        <span title="{{$exchange_ammount ?? ''}}"> {{ ($exchange_ammount) ? f_float($exchange_ammount,2) : '$$$' }} </span>
                        
                    </td>

                    <td class="{{ $class_grado ?? '' }}">
                        @php $ammount_creditos = (!empty($registropago->creditoafavor)) ? $registropago->creditoafavor->credito_ammount:null; @endphp
                        @php $exchange_ammount = (!empty($registropago->creditoafavor)) ? $registropago->creditoafavor->exchange_ammount:null; @endphp
                        @php $ammount = (!empty($registropago->creditoafavor)) ? $registropago->creditoafavor->credito_ammount:null; @endphp

                        <span title="{{$ammount ?? ''}}"> {{ ($exchange_ammount) ? f_float($ammount,2) : null }} </span> ||
                        <span title="{{$ammount_creditos ?? ''}}"> {{ ($exchange_ammount) ? f_float($exchange_ammount,2) : null }} </span>

                    </td>

                    <td class="{{ $class_grado ?? '' }}">
                        {{ ($registropago->pagos_combinados->count() > 1) ? 'SI':'NO'}}
                    </td>

                    <td  class="{{ $class_ci ?? '' }}">
                        {{ ($registropago->created_at) ? $registropago->created_at->format('d-m-Y') : null }}
                    </td>
                    <td  class="{{ $class_ci ?? '' }}">
                        {{ $registropago->user->username ?? '' }}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                        <div class="btn-group btn-group-sm">

                            <a title="Mostrar detalles del registro de pago" class="btn-modal btn btn-info btn-sm" href="#">
                                <i class="fas fa-info"></i>
                            </a>

                            <a title="Editar Registro de Pago" class="btn btn-warning btn-sm" href="{{ route('administracion.registropagos.edit',['id'=>$registropago->id]) }}" role="button">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                            </a>

                            @php $disabled = ($registropago->status_delete) ? null:'disabled'; @endphp
                            @php $disabled = 'disabled'; @endphp
                            @php $disabled = ($registropago->cancellable) ? null:'disabled'; @endphp
                            <a title="Haz clic para la anulación" class="btn-anular btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-anular_registropago_id_{{$registropago->id}}">
                                <i class="fa fa-window-close" aria-hidden="true"></i>
                            </a>

                            <a title="Histórico de pagos" class="btn-nodal-historico btn btn-sm btn-light btn-sm "
                                href="{{ route('administracion.representants.historico',['representant_id'=>$estudiant->representant->id]) }}"
                                role="button">
                                <i class="{{ $icon_menus['historico'] ?? ''}} fa-1x text-primary"></i>...
                            </a>

                            <a title="Editar Registro de Pago" class="btn btn-warning btn-sm" href="{{ route('administracion.refunds.index',['registro_pago_combinado_id'=>$registro_pago_combinado->id]) }}" role="button">
                                <i class="{{ $icon_menus['administracion'] ?? null }} fa-1x"></i>
                            </a>

                        </div>
                    </td>
                </tr>

        @endif

            @endif

        @endforeach

    </tbody>
</table>

<div id="container_modal"></div>

{!! Form::open(['route' => ['administracion.registropagos.anular',':REGISTROPAGO_ID'], 'method' => 'POST', 'id'=>'form-registro-pago-anular', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/registropagos/anular.js") }}"></script> @endsection

@section('scripts')
    @parent
    <script>

        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_registropago';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.registro_pago", "_id_")}}';
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

        //****************************************************************************************************************************

        $('.btn-nodal-historico').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('representant_id');  //console.log(id);
            var modal = '#modal_historico';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.representant_historico_pago", "_id_")}}';
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

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')
