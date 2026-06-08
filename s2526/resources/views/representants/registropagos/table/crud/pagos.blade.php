@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="d-none";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_ammount=" alert-success";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp


<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N ?? '' }}">N</th>
            <th class="{{ $class_fecha ?? '' }}" title="Fecha del Registro de Pago">Fecha</th>
            <th class="{{ $class_planpago ?? '' }}" title="Concepto de Cobro">Cuota</th>
            <th class="{{ $class_ammount ?? '' }}">Pagado</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($registropagos as $registropago)

        @php
            $estudiant = $registropago->estudiant;
            $representant = $registropago->representant;
        @endphp

        <tr data-id="{{$registropago->id}}" data-representant_id="{{$representant->id ?? ''}}">

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td class="{{ $class_fecha ?? '' }}">
                {{ $registropago->created_at->format('d-m-Y') ?? ''}}
            </td>

            <td class="{{ $class_planpago ?? '' }}">
                {{$registropago->cuentaxpagar->name ?? ''}}
            </td>

            <td class="{{ $class_ammount ?? '' }}">
                {{ ($registropago->total_exchange_pagos_ammount) ? '$ '.f_float($registropago->total_exchange_pagos_ammount) : 'Bs '.f_float($registropago->total_pagos_ammount)}}
            </td>

        </tr>

        @endforeach

    </tbody>
</table>

{{-- partials contentivo de los scripts datatables --}}
@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-pagos').DataTable( {
                "pagingType": "numbers",
                "pageLength": 8,
                "bLengthChange": false,
                "bPaginate": false,
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
