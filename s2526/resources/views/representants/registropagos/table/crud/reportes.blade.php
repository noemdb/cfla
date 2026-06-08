@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="";
    $class_ammount=" alert-primary";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_action="";
@endphp


<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N ?? '' }}">N</th>
            <th class="{{ $class_fecha ?? '' }}" title="Fecha del Rporte de Pago">Fecha</th>
            <th class="{{ $class_planpago ?? '' }}" title="Bancos">Bancos</th>
            <th class="{{ $class_planpago ?? '' }}" title="Referencias">Referencias</th>
            <th class="{{ $class_ammount ?? '' }}" title="Referencias">Montos</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($payments as $payment)

        @php
            // $estudiant = $payment->estudiant;
            $representant = $payment->representant;
            $bancos = $payment->bancos;
            // $method_pays = $payment->method_pays;
            $number_i_pays = $payment->number_i_pays;
            $ammounts = $payment->ammounts;
        @endphp

        <tr data-id="{{$payment->id}}" data-representant_id="{{$representant->id ?? ''}}">

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td class="{{ $class_fecha ?? '' }}">
                {{ $payment->created_at->format('d-m-Y') ?? ''}}
            </td>

            <td class="{{ $class_planpago ?? '' }}">
                {{-- <ul class="pl-0"> --}}
                    @foreach ($bancos as $banco)
                    @if ($banco)
                        <div>
                            -. {{$banco ?? ''}}
                        </div>
                    @endif
                    @endforeach
                {{-- </ul> --}}
            </td>
            <td class="{{ $class_planpago ?? '' }}">
                {{-- <ul class="pl-0"> --}}
                    @foreach ($number_i_pays as $number_i_pay)
                    @if ($number_i_pay)
                        <div>
                            -. {{$number_i_pay ?? ''}}
                        </div>
                    @endif
                    @endforeach
                {{-- </ul> --}}
            </td>
            <td class="{{ $class_ammount ?? '' }}">
                {{-- <ul class="pl-0"> --}}
                    @foreach ($ammounts as $ammount)
                    @if ($ammount)
                        <div>
                            -. {{f_float($ammount) ?? ''}}
                        </div>
                    @endif
                    @endforeach
                {{-- </ul> --}}
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
            $('#table-data-reportes').DataTable( {
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
