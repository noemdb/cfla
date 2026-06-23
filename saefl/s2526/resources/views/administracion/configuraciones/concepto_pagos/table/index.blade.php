@php
    $class_N="d-none d-sm-table-cell";
    $class_name="d-none d-sm-table-cell";
    $class_planpago="d-none d-lg-table-cell";
    $class_concepto="d-none d-lg-table-cell";
    $class_tipo="d-none d-md-table-cell";
    $class_date="d-none d-md-table-cell";
    $class_ammount="d-none d-sm-table-cell";
    $class_description="d-none d-sm-table-cell";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_name ?? ''}}">Nombre</th>
                <th class="{{ $class_planpago ?? ''}}">Plan de Pago</th>
                <th class="{{ $class_concepto ?? ''}}">Concepto</th>
                <th class="{{ $class_tipo ?? ''}}">Tipo</th>
                <th class="{{ $class_date ?? ''}}">F.Vencimiento</th>
                <th class="{{ $class_ammount ?? ''}}">Monto</th>
                <th class="{{ $class_ammount }}">M.Cambairio</th>
                <th class="{{ $class_ammount ?? ''}}">Decuento</th>
                <th class="{{ $class_description }}">Descripción</th>
                <th class="{{ $class_action ?? ''}}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($concepto_pagos as $concepto_pago)

            @php
                $cuentaxpagar = $concepto_pago->cuentaxpagar;
                $cuentaxpagar_type = ($cuentaxpagar) ? $cuentaxpagar->type : null ;
                $estudiant = ($cuentaxpagar_type == 'INDIVIDUAL') ? $cuentaxpagar->estudiant : null;
            @endphp

            <tr data-id="{{$concepto_pago->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_name ?? '' }}">
                    {{$concepto_pago->nomconceptopago->name ?? ''}}
                </td>
                <td class="{{ $class_planpago  ?? ''}}">
                    {{$cuentaxpagar->planpago->name ?? ''}}
                </td>
                <td class="{{ $class_concepto  ?? ''}}">
                    {{$cuentaxpagar->name ?? ''}}
                </td>

                <td class="{{ $class_tipo ?? '' }}">
                    {{$concepto_pago->cuentaxpagar->type ?? ''}}
                    {{ ($estudiant) ? $estudiant->ci_estudiant : null }}
                </td>
                <td class="{{ $class_date ?? '' }}">
                    {{ f_date($cuentaxpagar->date_expiration) ?? ''}}
                </td>
                <td class="{{ $class_ammount ?? '' }}">
                    Bs. {{ f_float($concepto_pago->concepto_ammount) ?? '' }}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    $ {{ f_float($concepto_pago->exchange_ammount) }}
                </td>
                <td class="{{ $class_ammount ?? '' }}">
                    {{ ($concepto_pago->status_discount=='true') ? 'SI':'NO'}}
                </td>
                <td class="{{ $class_description ?? '' }}">
                    {{$concepto_pago->concepto_description ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $concepto_pago->id }}">
                    <div class="btn-group btn-group-sm">

                        <a title="Mostrar detalles" class="btn-modal btn btn-info btn-sm" href="#" data-url="{{route("administracion.ajax.fill.modal.concepto_pagos.show", "_id_")}}" role="button">
                            <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
                        </a>

                        {{-- @php $disabled = ($concepto_pago->status_edit) ? null:'disabled'; @endphp
                        <a title="Editar" class="btn-modal btn btn-warning btn-sm {{ $disabled ?? '' }}"  href="{{route('administracion.configuraciones.concepto_pagos.edit',$concepto_pago->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a> --}}

                        {{-- @php $disabled = ( $concepto_pago->status_edit) ? null : ' disabled '; @endphp --}}
                        <a title="Editar" class="btn-modal btn btn-warning btn-sm {{$disabled ?? ''}}" href="#"
                            data-url="{{route("administracion.ajax.fill.modal.concepto_pagos.edit", "_id_")}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        {{-- @php $disabled = ($concepto_pago->status_delete) ? null:'disabled'; @endphp --}}
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$concepto_pago->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>
                </td>
                {{-- modal --}}
                @php $modal_id = 'modal_concepto_pago_'.$concepto_pago->id; @endphp
                @php $id_container = 'container_modal_'.$concepto_pago->id; @endphp
                <div id="{{$id_container ?? ''}}"></div>

            </tr>
            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.configuraciones.concepto_pagos.destroy',':CONCEPTO_COBRO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':CONCEPTO_COBRO_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/destroy/default.js") }}"></script> @endsection

@section('scripts')
    @parent
    <script>
        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_concepto_pago_'+id;  //console.log(modal);
            var container = '#container_modal_'+id;  //console.log(container);
            var ajaxurl = $(this).data('url');
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
@include('administracion.datatables.default')
