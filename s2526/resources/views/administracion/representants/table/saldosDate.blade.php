@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm p-1 small" id="table-data-default">
        <caption style="caption-side: top">Listado de saldos por representante - Total: {{ count($deuda_ex_arr) ?? ''}} - Montos en Divisas ($)</caption>
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_representant }}">CI</th>
                <th class="{{ $class_representant }}">Representante</th>
                <th class="{{ $class_deuda }}" title="Créditos a Favor">CAF</th>
                <th class="{{ $class_deuda }}" title="Abonos en tránsito">ABN</th>
                <th class="{{ $class_deuda }}" >SAF</th>
                <th class="{{ $class_deuda }}" title="Deuda Parcial">D.Parcial</th>
                <th class="{{ $class_deuda }}" title="Deuda total de la cuota">D.Cuota</th>
                <th class="{{ $class_fecha }} text-muted">Contacto</th>
            </tr>
        </thead>

        <tbody id="tdatos">

        @foreach($representants as $representant)
            @if (array_key_exists($representant->id, $deuda_ex_arr_date))
                @php
                    $total_credito_exchange = $representant->total_credito_exchange ;
                    $total_abono_exchange = $representant->total_abono_exchange ;
                    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;

                    $deuda_total_exchange_date = (array_key_exists($representant->id, $deuda_ex_arr_date)) ? $deuda_ex_arr_date[$representant->id] : null;
                    $deuda_total_exchange  = $deuda_total_exchange_date - $saldo_a_favor_exchange;
                @endphp

                <tr data-representant_id="{{$representant->id ?? ''}}" class="{{ ($deuda_total_exchange_date <= 0) ? 'table-danger' : null}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td id="td-count" class="{{ $class_representant ?? '' }}">
                        {{ $representant->ci_representant ?? ''}}
                    </td>

                    <td id="td-representant-{{ $representant->id }}" class="{{ $class_representant  ?? ''}} align-top">
                        <a class="btn-link text-dark" href="{{ route('administracion.representants.index',['search'=>$representant->ci_representant]) }}">
                            <b>{{$representant->name}}</b>
                        </a>
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        {{ ($total_credito_exchange) ? round($total_credito_exchange,2) : null }}
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        {{ ($total_abono_exchange) ? round($total_abono_exchange,2) : null }}
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        {{ ($saldo_a_favor_exchange) ? round($saldo_a_favor_exchange,2) : null }}
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        <span class="{{ ($deuda_total_exchange_date <= 0) ? 'text-dark font-weight-bold' : null}}">{{ round($deuda_total_exchange_date,2) }}</span>
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        <span class="{{ ($deuda_total_exchange <= 0) ? 'text-danger font-weight-bold' : null}}">{{ round($deuda_total_exchange,2) }}</span>
                    </td>

                    <td class="{{ $class_fecha }}">
                        <span class="small">{{ $representant->fullphone ?? ''}}<br>{{ $representant->email ?? ''}}</span>
                    </td>

                </tr>

            @endif
        @endforeach

        </tbody>
    </table>

{{-- </div> --}}

{{-- @include('administracion.datatables.exportBootstrap') --}}
@include('administracion.datatables.exportBootstrapCustom')

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var table = $('#table-data-default').DataTable( {
                "bPaginate": true,
                "lengthMenu": [[10, 25, 50, 100 , 500, -1], [10, 25, 50, 100 , 500, "Todos"]],
                lengthChange: true,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        pageSize: 'LEGAL',
                        text: 'CSV',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        // pageSize: 'LEGAL',
                        pageSize: 'LETTER',
                        exportOptions: {
                            columns: [ 1,2,4,7,8,9]
                        }
                    },

                    // { extend: 'print', text: 'Imprimir' },
                    { extend: 'colvis', text: 'Columnas' }
                ]
            } );

            table.buttons().container()
                .appendTo( '#table-data-default_wrapper .col-md-6:eq(0)' );
        } );
    </script>
@endsection
