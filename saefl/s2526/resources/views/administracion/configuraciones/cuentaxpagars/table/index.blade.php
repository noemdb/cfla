@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Nombre</th>
                <th class="{{ $class_ci }}">Plan de Pago</th>
                <th class="{{ $class_ci }}">Tipo</th>
                <th class="{{ $class_grado }}">F.Vencimiento</th>
                <th class="{{ $class_grado }}">Descripción</th>
                <th class="{{ $class_grado }}" title="Número de cuentas de cobro">N.Cuentas</th>
                <th class="{{ $class_grado }}" title="Estudiente">Representante/Estudiante</th>
                <th class="{{ $class_grado }}" title="Estudiente">Incobrable</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($cuentaxpagars as $cuentaxpagar)

            @php $estudiant = $cuentaxpagar->estudiant; @endphp
            @php $representant = ($estudiant ) ? $estudiant->representant : null; @endphp
            @php
                $conceptopagos = $cuentaxpagar->conceptopagos;
                $conceptopagos_exchange_ammount = $conceptopagos->sum('exchange_ammount');
            @endphp

            <tr data-id="{{$cuentaxpagar->id}}" class="table-{{(empty($cuentaxpagar->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_email ?? '' }}">
                    {{$cuentaxpagar->name ?? ''}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{$cuentaxpagar->planpago->name ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{$cuentaxpagar->type ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{f_date($cuentaxpagar->date_expiration) ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{$cuentaxpagar->description ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $conceptopagos->count() ?? ''}}
                    [{{ round($conceptopagos_exchange_ammount,2) }}]
                </td>
                <td class="{{ $class_state ?? '' }}">
                    @if ($estudiant) {{$representant->ci_representant ?? ''}} / {{$estudiant->ci_estudiant ?? ''}} @endif
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ ($cuentaxpagar->status_bad=="true") ? '-SI-':'-NO-' }}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $cuentaxpagar->id }}">

                    <div class="btn-group btn-group-sm">

                        <a title="Detalles" class="btn-modal btn btn-info btn-sm" href="#"
                            data-url="{{route("administracion.ajax.fill.modal.cuentaxpagar", "_id_")}}"
                            role="button">
                            <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
                        </a>

                        {{-- @php $disabled = ( $cuentaxpagar->status_asignation) ? null : ' disabled '; @endphp --}}
                        <a title="Asignar Cuentas" class="btn-modal btn btn-success btn-sm {{$disabled ?? ''}}" href="#"
                            data-url="{{route("administracion.ajax.fill.modal.cuentaxpagar.asignar_concepto", "_id_")}}" role="button">
                            <i class="{{ $icon_menus['cuentas_cobrar'] ?? '' }} fa-1x"></i>
                        </a>

                        {{-- <a title="Editar" class="btn-modal btn btn-warning btn-sm {{$disabled ?? ''}}" href="#" data-url="{{route("administracion.ajax.fill.modal.cuentaxpagar.edit", "_id_")}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a> --}}

                        <a title="Editar" class="btn btn-warning btn-sm {{$disabled ?? ''}}" role="button" href="{{route('administracion.configuraciones.cuentaxpagars.edit',$cuentaxpagar->id)}}" >
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        {{-- @php $disabled = ( $cuentaxpagar->status_delete) ? null : ' disabled '; @endphp --}}
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_{{$cuentaxpagar->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>

                    {{-- modal --}}
                    @php $modal_id = 'modal_cuentaxpagar_'.$cuentaxpagar->id; @endphp
                    @php $id_container = 'container_modal_'.$cuentaxpagar->id; @endphp
                    <div id="{{$id_container ?? ''}}"></div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.configuraciones.cuentaxpagars.destroy',':CUENTAXPAGAR_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':CUENTAXPAGAR_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/destroy/default.js") }}"></script> @endsection

@section('scripts')
    @parent
    <script>
        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_cuentaxpagar_'+id;  //console.log(modal);
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

