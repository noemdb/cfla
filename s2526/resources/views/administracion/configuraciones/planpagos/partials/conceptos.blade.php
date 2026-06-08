@php
    $class_N="d-none d-sm-table-cell";
    $cuentaxpagar_name="d-none d-sm-table-cell";
    $concepto_description="d-none d-lg-table-cell";
    $date_expiration="d-none d-sm-table-cell";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-concepto_pagos">
        <thead>
            <tr>
                <th class="{{ $class_N ?? '' }}">N</th>
                <th class="{{ $class_estudiant ?? '' }}">Nombre</th>
                <th class="{{ $class_ci ?? '' }}">Concepto</th>
                {{-- <th class="{{ $concepto_description ?? '' }}">Descripción</th> --}}
                <th class="{{ $date_expiration  ?? ''}}">F.Vencimiento</th>
                <th class="{{ $class_ci ?? '' }}">Monto(Bs.)</th>

                {{-- <th class="{{ $class_ci }}" title="Número de conceptos cancelados">N.C.Cancelados</th> --}}
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($conceptopagos as $concepto_pago)

            <tr data-concepto_pago="{{$concepto_pago->id}}" data-concepto_pago="{{$concepto_pago->id ?? ''}}" class="table-{{(empty($concepto_pago->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_email ?? '' }}">
                    {{$concepto_pago->nomconceptopago->name ?? ''}}
                </td>
                <td class="{{ $cuentaxpagar_name ?? '' }}">
                    <small>
                        <span class="badge badge-secondary">
                            {{$concepto_pago->cuentaxpagar->name ?? ''}}
                        </span>
                    </small>
                </td>
                {{-- <td class="{{ $concepto_description ?? '' }}">
                    {{$concepto_pago->concepto_description ?? ''}}
                </td> --}}
                <td class="{{ $date_expiration ?? '' }}">
                    {{ f_date($concepto_pago->cuentaxpagar->date_expiration) ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ f_float($concepto_pago->concepto_ammount) ?? ''}}
                </td>
                {{-- <td class="{{ $class_user  ?? ''}}">
                    {{ $concepto_pago->conceptocancelados->count() ?? ''}}
                </td> --}}

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $concepto_pago->id }}">
                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.concepto_pagos.edit',$concepto_pago->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        {{-- @php $disabled = ( $concepto_pago->status_delete) ? null : ' disabled '; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$concepto_pago->id}}">
                            <i class="fas fa-trash"></i>
                        </a> --}}
                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

@section('stylesheet')
    @parent <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection
@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-concepto_pagos').DataTable( {
                "pagingType": "simple",
                "pageLength": 10,
                "bLengthChange": false,
                "bPaginate": true,
                "searching": false,
                "bInfo" : false,
                "responsive": false,
                "columnDefs": [ {
                    "targets": 'nosort',
                    "orderable": false
                } ],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                }
            } );
        } );
    </script>
@endsection
