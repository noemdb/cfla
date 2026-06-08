@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_estudiant="";
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
                <th class="{{ $class_estudiant }}">Estudiantes</th>
                <th class="{{ $class_estudiant }}">N.Estudiantes</th>
                <th class="{{ $class_estudiant }}">Cuotas</th>
                <th class="{{ $class_deuda ?? '' }}" >R.Morosidad</th>
                <th class="{{ $class_deuda }}" title="Deuda Vencida Divisas">D. Cambiaria</th>
                <th class="{{ $class_deuda }}" >SAF</th>
                <th class="{{ $class_fecha }} text-muted">Contacto</th>
                @admin
                <th class="{{ $class_action }}">N. Usuario</th>
                <th class="{{ $class_action }}">GSEmail</th>
                <th class="{{ $class_action }}">Contraseña</th>
                @endadmin
            </tr>
        </thead>

        <tbody id="tdatos">

        @foreach($representants as $representant)
            @if (array_key_exists($representant->id, $deuda_ex_arr))
                @php
                    $exchange_expire_bills = $representant->exchange_expire_bill_pendientes ;
                    $count_exchange_expire_bills = $exchange_expire_bills->count() ;
                    $exchange_expire_bill_pendientes = $representant->exchange_expire_bill_pendientes;
                    $count_exchange_expire_bill_pendientes = $exchange_expire_bill_pendientes->count();

                    // Omitir cuotas pendientes con monto menor a USD 0.01
                    $exchange_expire_bills_sin_rmorosidad = $representant->exchange_expire_bill_pendientes->where('status_late_payment',false) ->where('ammount','>=',0.01);
                    $count_exchange_expire_bills_sin_rmorosidad = $exchange_expire_bills_sin_rmorosidad->count() ;

                    $total_credito_exchange = $representant->total_credito_exchange ;
                    $total_abono_exchange = $representant->total_abono_exchange ;
                    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;                
                    $ammount_expire_bill = $deuda_bs_arr[$representant->id]; 
                    $exchange_ammount_expire_bill = $deuda_ex_arr[$representant->id]; 
                    $deuda_total_exchange = $exchange_ammount_expire_bill - $saldo_a_favor_exchange;
                    $estudiants_formaly = $representant->estudiants_formaly;
                    $recargosMorosidad = $count_exchange_expire_bills - $count_exchange_expire_bills_sin_rmorosidad
                @endphp

                @if ($exchange_ammount_expire_bill)

                    <tr data-representant_id="{{$representant->id ?? ''}}" class="{{ ($deuda_total_exchange <= 0) ? 'table-danger' : null}}">

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

                        <td id="td-count" class="{{ $class_estudiant ?? '' }}">
                            @foreach ($estudiants_formaly as $estudiant)
                                @php $inscripcion = $estudiant->fullInscripcion; @endphp
                                <div class="small pl-2">-. {{$estudiant->short_name ?? ''}} || {{$estudiant->ci_estudiant ?? ''}} || {{$inscripcion}}</div>
                            @endforeach
                        </td>

                        <td id="td-count" class="{{ $class_estudiant ?? '' }}">
                            {{ $estudiants_formaly->count() ?? ''}}
                        </td>

                        <td id="td-count" class="{{ $class_estudiant ?? '' }}">

                            {{ $count_exchange_expire_bills_sin_rmorosidad ?? ''}}
                            {{-- <pre>{{ json_encode($exchange_expire_bills_sin_rmorosidad, JSON_PRETTY_PRINT) }}</pre> --}}

                        </td>

                        <td id="td-count" class="{{ $class_N }}">
                            {{$recargosMorosidad}} 
                        </td>                    
                        
                        <td id="td-ammount_expire_bill-{{ $representant->id }}" class="{{ $class_deuda ?? '' }} align-top">
                            <b class=" text-dark">
                                {{ ($exchange_ammount_expire_bill) ? round($exchange_ammount_expire_bill,2) : null }}
                            </b>
                        </td>

                        <td class="{{ $class_deuda ?? '' }} align-top">
                            {{ ($saldo_a_favor_exchange) ? round($saldo_a_favor_exchange,2) : null }}
                        </td>

                        <td class="{{ $class_fecha }}">
                            <span class="small">{{ $representant->phone ?? ''}}<br>{{ $representant->email ?? ''}}</span>
                        </td>

                        @admin
                            <td style="white-space: wrap !important">
                                @php $user = ($representant->user) ? $representant->user:null ; @endphp
                                {{ ($user) ? $user->username : null }}
                            </td>
                            
                            <td style="white-space: wrap !important">
                                {{ $representant->gsemail ?? ''}}
                            </td>
                            <td style="white-space: wrap !important">
                                @php if ($user) $password = ($user->status_update) ? '###':$user->username ; @endphp
                                {{ $password ?? ''}}
                            </td>
                        @endadmin

                    </tr>

                @endif

            @endif
        @endforeach

        </tbody>
    </table>

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
