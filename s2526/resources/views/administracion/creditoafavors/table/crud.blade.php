@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_fecha="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="nosort text-center";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_N }}">Ident.</th>
            <th class="{{ $class_estudiant }}">Representante</th>
            <th class="{{ $class_estudiant }}">CI</th>
            <th class="{{ $class_fecha }}">Fecha</th>
            <th class="{{ $class_monto }}">Monto (Bs.)</th>
            <th class="{{ $class_monto }}" title="Monto Cambiario">M.Cambiario ($)</th>
            <th class="{{ $class_grado }}">Estado</th>
            <th class="{{ $class_grado }} text-center">Origen</th>
            <th class="{{ $class_grado }} text-center">Destino</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

    @foreach($creditoafavors as $creditoafavor)

        @php $estudiant = $creditoafavor->estudiant; @endphp
        @php $registropago = $creditoafavor->registropago; @endphp
        @php $registro_pago_combinado = ($registropago) ? $registropago->registro_pago_combinado : null; @endphp
        @php $registro_pago_combinado_id = ($registro_pago_combinado) ? $registro_pago_combinado->id : null; @endphp
        @php $representant = $creditoafavor->representant; @endphp
        @php $exchange_rate = $creditoafavor->exchange_rate @endphp
        @php $exchange_ammount = $creditoafavor->exchange_ammount @endphp
        @php $credito_aplicado = $creditoafavor->credito_aplicado @endphp
        @php $omitted = $creditoafavor->status_omitted @endphp

        <tr data-id="{{$creditoafavor->id ?? ''}}" class="table-{{(!empty($creditoafavor->deleted_at))? 'danger':'' }}">

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td id="td-count" class="{{ $class_N }}">
                <span class="text-dark">{{ ($creditoafavor) ? $creditoafavor->id : null}}</span>
            </td>

            <td class="{{ $class_estudiant  ?? ''}}">
                <a class="btn-link text-dark" href="{{ route('administracion.representants.index',['search'=>$representant->ci_representant]) }}">
                    <b>{{$representant->name}}</b>
                </a>
                @if($creditoafavor->observations_user)
                    <div class="text-muted">
                        {{$creditoafavor->observations_user}}
                    </div>
                @endif
            </td>

            <td class="{{ $class_fecha ?? '' }}">
                {{ $representant->ci_representant ?? ''}}
            </td>

            <td class="{{ $class_fecha ?? '' }}">
                {{ ($creditoafavor->created_at) ? $creditoafavor->created_at->format('d-m-Y'): null }}
            </td>

            <td class="{{ $class_planpago ?? '' }}">
                {{-- {{ f_float($creditoafavor->credito_ammount) ?? ''}} --}}
                {{ round($creditoafavor->credito_ammount,2) ?? ''}}
            </td>
            <td class="{{ $class_monto ?? '' }}">
                @php $class_exchange_ammount = ($exchange_ammount) ? 'font-weight-bold  text-primary':'text-dark'; @endphp
                <span class="{{ $class_exchange_ammount ?? null }}">
                    {{-- {{ ($exchange_ammount) ? f_float($exchange_ammount) : null }} --}}
                    {{f_number($exchange_ammount)}}
                </span>
            </td>
            <td class="{{ $class_planpago ?? '' }} {{ ($omitted=='true') ? 'text-danger':null }}">
                {{(empty($creditoafavor->deleted_at))? 'NO APLICADO':'APLICADO' }}
                <span class="text-muted small float-right font-weight-bold">{{($omitted=='true')? 'Omitido':'No Omitido' }}</span>
            </td>

            <td class="{{ $class_planpago ?? '' }} text-center">
                @if ($registropago)
                    <div class=" font-weight-bold text-nowrap">
                        <span class="border rounded btn-primary p-1">P{{ ($registropago) ? $registropago->id : null}}</span>
                        <span class="border rounded btn-warning p-1">C{{ ($registro_pago_combinado) ? $registro_pago_combinado->id : null}}</span>
                    </div>
                @endif
            </td>

            <td class="{{ $class_planpago ?? '' }} text-center">
                @if ($credito_aplicado)
                    <div class=" font-weight-bold text-nowrap">
                        {{-- {{$credito_aplicado ?? ''}} --}}
                        <span class="btn-success border rounded p-1">P{{ $credito_aplicado->registropago->id ?? null }}</span>
                    </div>
                @endif
            </td>

            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">

                    <a title="Mostrar detalles del credito a favor" class="btn-modal btn btn-info btn-xs" href="#">
                        <i class="fas fa-info"></i>
                    </a>

                    @admon
                        {{-- @php $disabled = (empty($creditoafavor->exchange_ammount) && empty($creditoafavor->deleted_at)) ? false:true @endphp --}}
                        @php $disabled = (empty($creditoafavor->deleted_at)) ? false:true @endphp
                        <a title="Editar Registro" class="btn btn-warning btn-sm {{ ($disabled) ? 'disabled' : null }}" href="{{ route('administracion.creditoafavors.edit',['id'=>$creditoafavor->id]) }}" role="button">
                            <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                        </a>


                        {{-- <button type="button" class="btn-omit btn btn-secondary btn-sm {{ ($disabled) ? 'disabled' : null }}" value="update" data-id="{{$creditoafavor->id ?? null}}">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button> --}}

                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$creditoafavor->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                        <a title="Registro de Vueltos/Devoluciones" class="btn btn-warning btn-sm {{($registro_pago_combinado_id) ? null:'disabled'}}" href="{{ route('administracion.refunds.index',['registro_pago_combinado_id'=>$registro_pago_combinado_id]) }}" role="button">
                            <i class="{{ $icon_menus['administracion'] ?? null }} fa-1x"></i>
                        </a>
                    @endadmon

                </div>
            </td>
        </tr>

        @endforeach

    </tbody>
</table>

{!! Form::open(['route' => ['administracion.creditoafavors.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

<div id="container_modal"></div>

@section('scripts')
    @parent
    <script>
        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_creditoafavors_'+id;  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.creditoafavor", "_id_")}}';
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

        $('.btn-omit').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id'); console.log(id);
            var checkId = '#checkId'+id; console.log(checkId);
            var ajaxurl = '{{route("administracion.creditoafavors.set.ajax.omit", "_id_")}}';
            ajaxurl = ajaxurl.replace('_id_', id); console.log(ajaxurl);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    location.reload(true);
                    // $("#modelId .close").click();
                    // $(checkId).fadeOut();
                    // $( "div" ).remove( checkId );
                }
            });
        });
    </script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}

@include('administracion.datatables.exportBootstrap')
