@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_fecha="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $observations_user="text-nowrap";
    $class_action="nosort text-center";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_N }}">Omitir</th>
            <th class="{{ $class_estudiant }}">Representante</th>
            <th class="{{ $class_fecha }}">Fecha</th>
            <th class="{{ $class_monto }}">Monto (Bs.)</th>
            <th class="{{ $class_monto }}" title="Monto Cambiario">M.Cambiario ($)</th>
            <th class="{{ $observations_user }}" title="Observación">Observación</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

    @foreach($creditoafavors as $creditoafavor)

        @php $estudiant = $creditoafavor->estudiant; @endphp
        @php $registropago = $creditoafavor->registropago; @endphp
        @php $registro_pago_combinado = ($registropago) ? $registropago->registro_pago_combinado : null; @endphp
        @php $representant = $creditoafavor->representant; @endphp
        @php $exchange_rate = $creditoafavor->exchange_rate @endphp
        @php $exchange_ammount = $creditoafavor->exchange_ammount @endphp
        @php $credito_aplicado = $creditoafavor->credito_aplicado @endphp
        @php $omitted = $creditoafavor->status_omitted @endphp

        <tr data-id="{{$creditoafavor->id ?? ''}}" class="{{($creditoafavor->id == $creditoafavor_id)? 'table-secondary font-weight-bold':'' }} ">

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td id="td-count" class="{{ $class_N }} font-weight-bold {{ ($creditoafavor->status_omitted == 'true') ? 'text-danger' : null}}">
                {{ ($creditoafavor->status_omitted == 'true') ? 'SI' : 'NO'}}
            </td>

            <td class="{{ $class_estudiant  ?? ''}}">
                <a class="btn-link text-dark" href="{{ route('administracion.representants.index',['search'=>$representant->ci_representant]) }}">
                    <b>{{$representant->name}}</b>
                </a>
                <span class="small text-muted d-block">{{ $representant->ci_representant ?? ''}}</span>
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
                    {{round($exchange_ammount,2)}}
                </span>
            </td>

            <td class="{{ $observations_user ?? '' }}">
                {{ $creditoafavor->observations_user ?? ''}}
            </td>
            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">

                    {{-- <a title="Mostrar detalles del credito a favor" class="btn-modal btn btn-info btn-xs" href="#">
                        <i class="fas fa-info"></i>
                    </a> --}}
                    @php
                        $inputs = [
                            'finicial'=>$finicial,
                            'ffinal'=>$ffinal,
                            'ci'=>$ci,
                            'active'=>$active,
                            'status_omitted_request'=>$status_omitted_request,
                            'creditoafavor_id'=>$creditoafavor->id,
                        ];
                    @endphp
                    <a title="Editar Registro" class="btn btn-warning btn-sm" href="{{ route('administracion.creditoafavors.omit',$inputs) }}" role="button">
                        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
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
    </script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}

@include('administracion.datatables.default')
