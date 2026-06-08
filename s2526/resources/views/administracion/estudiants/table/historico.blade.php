@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm p-1" id="table-data-default">
        <thead>
            <tr class=" text-uppercase">
                <th class="{{ $class_estudiant }}">Fec. Registro</th>
                <th class="{{ $class_action }}">Conceptos</th>
                <th class="{{ $class_grado }}">Pagado (Bs.)</th>
                <th class="{{ $class_grado }}">CAF (Bs.)</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($registropagos as $registropago)

                <tr data-id="{{$registropago->id}}">
                    @php $estudiant = $registropago->estudiant; @endphp
                    @php $pagos = $registropago->pagos; @endphp
                    @php $creditoafavor = $registropago->creditoafavor; @endphp

                    <td>{{f_date($registropago->created_at)}}</td>
                    <td>{{$registropago->cuentaxpagar->name ?? ''}}</td>
                    <td>{{ f_float($pagos->sum('pagos_ammount'))}}</td>

                    <td>
                        {{$registropago->ammount_creditos_generados ?? ''}}

                        {{-- {{ (!(empty($creditoafavor->credito_ammount))) ? f_float($creditoafavor->credito_ammount) : '' }} --}}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                            @php $id_modal = 'modal_registropago_'.$registropago->id; @endphp
                            @php $id_container = 'container_modal_'.$registropago->id; @endphp

                            <a title="Mostrar detalles del registro de pago" class="btn-modal btn btn-info btn-xs" href="#">
                                <i class="{{ $icon_menus['show'] ?? '' }} fa-1x"></i>
                            </a>
                            <div id="{{$id_container ?? ''}}"></div>

                        </div>

                    </td>
                </tr>

            @endforeach

        </tbody>
    </table>

{{-- </div> --}}

@section('scripts')
    @parent
    <script>
        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  console.log(id);
            var modal = '#modal_registropago_'+id;  console.log(modal);
            var container = '#container_modal_'+id;  console.log(container);
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
    </script>
@endsection

{{-- @section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection --}}
