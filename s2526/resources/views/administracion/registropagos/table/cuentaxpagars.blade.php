@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = 'd-none d-md-table-cell';
    $class_fecha = 'd-none';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_deuda = 'd-none d-lg-table-cell text-nowrap';
    $class_grado = 'd-none d-lg-table-cell';
    $class_fecha = 'text-nowrap';
    $class_action = '';
    $total_deuda = 0;
@endphp


<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Representante/Estudiantes</th>
            <th class="{{ $class_deuda }}">CI</th>
            <th class="{{ $class_deuda }}" title="Deuda Vencida Divisas" style="text-align: left !important">Deuda</th>
            <th class="{{ $class_fecha }} text-muted">ABN</th>
            <th class="{{ $class_fecha }} text-muted">CAF</th>
            <th class="{{ $class_deuda }}">SAF</th>
            <th class="{{ $class_deuda }}" title="Deuda Total">D.Total</th>
            <th class="{{ $class_fecha }} text-muted">Contacto</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach ($representants as $fullRepresentant)
            @php
                $representant = $fullRepresentant['representant'];
                $estudiants = $fullRepresentant['estudiants'];
                $monto_exchange = $estudiants->sum('monto_exchange');

                $total_abono_exchange = $representant['total_credito_exchange'];
                $total_credito_exchange = $representant['total_abono_exchange'];
                $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;
                $deuda_total_exchange = $monto_exchange - $saldo_a_favor_exchange;
                $deuda_total_exchange = round($deuda_total_exchange, 2);
            @endphp

            @if ($deuda_total_exchange > 0)
                <tr data-representant_id="{{ $representant->id }}"
                    class="{{ $deuda_total_exchange <= 0 ? 'table-danger' : null }}">
                    <td class="{{ $class_N }}">
                        {{ $loop->iteration }}

                    </td>
                    <td class="{{ $class_estudiant }}">
                        <a class="btn-link text-dark"
                            href="{{ route('administracion.representants.index', ['search' => $representant->ci_representant]) }}"><b>{{ $representant->name }}</b></a>
                        <hr class=" m-0 p-0">
                        @foreach ($estudiants as $fullestudiant)
                            @php $estudiant = $fullestudiant['estudiant']; @endphp
                            <div style="font-size: 0.7rem; margin-left: 0.2rem">
                                <em>
                                    {{-- {{ $fullestudiant['estudiants'] }} -*- <br> --}}

                                    {{-- {{ $fullestudiant['cuentaxpagarId'] }} -*-  <br> --}}

                                    {{ $estudiant->short_name ?? '' }} <br>
                                    
                                    {{-- - {{ $estudiant->fullinscripcion ?? '' }} --}}
                                </em>
                            </div>
                        @endforeach
                    </td>
                    <td class="{{ $class_ci }}">
                        <span class=" font-weight-bold">{{ $representant->ci_representant ?? '' }}</span>
                        <dl class=" text-muted ml-2  mb-0 pb-0pl-2">
                            @foreach ($estudiants as $fullestudiant)
                                @php $estudiant = $fullestudiant['estudiant']; @endphp
                                <dd class=" m-0 p-0 nowrap"><span class=" text-muted"
                                        style="font-size: 0.7rem; margin-left: 0.2rem">{{ $estudiant->ci_estudiant ?? '' }}</span>
                                </dd>
                            @endforeach
                        </dl>
                    </td>

                    <td class="{{ $class_fecha }}">
                        <span class=" font-weight-bold">{{ round($monto_exchange, 2) ?? '' }}</span>
                        <dl class=" text-muted ml-2 pl-2 mb-0 pb-0">
                            @foreach ($estudiants as $fullestudiant)
                                @php $monto_exchange = $fullestudiant['monto_exchange']; @endphp
                                <dd class=" m-0 p-0 nowrap">
                                    <span class=" text-muted" style="font-size: 0.7rem; margin-left: 0.2rem">
                                        <em>
                                            
                                            {{ f_float($monto_exchange) ?? '' }}
                                        </em>
                                    </span>
                                </dd>
                            @endforeach
                        </dl>
                    </td>
                    <td class="{{ $class_fecha }}">
                        <span
                            class=" text-muted font-weight-bold">{{ round($representant['total_abono_exchange'], 2) ?? '' }}</span>
                    </td>
                    <td class="{{ $class_fecha }}">
                        <span
                            class=" text-muted font-weight-bold">{{ round($representant['total_credito_exchange'], 2) ?? '' }}</span>
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        <span>{{ $saldo_a_favor_exchange ? round($saldo_a_favor_exchange, 2) : null }}</span>
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        <span class="{{ $deuda_total_exchange <= 0 ? 'text-danger font-weight-bold' : null }}">
                            {{ $deuda_total_exchange ? round($deuda_total_exchange, 2) : null }}
                            @php $total_deuda = $total_deuda + $deuda_total_exchange; @endphp
                        </span>
                    </td>
                    <td class="{{ $class_fecha }}">
                        <span
                            class="small">{{ $representant->fullphone ?? '' }}<br>{{ $representant->email ?? '' }}</span>
                    </td>
                    <td class="{{ $class_action }}">
                        <div class="btn-group btn-group-sm">
                            <a title="Histórico de pagos" class="btn-nodal-historico btn btn-sm btn-light btn-sm "
                                href="{{ route('administracion.representants.historico', ['representant_id' => $estudiant->representant->id]) }}"
                                role="button"><i
                                    class="{{ $icon_menus['historico'] ?? '' }} fa-1x text-primary"></i></a>
                        </div>
                    </td>
                </tr>
            @endif
        @endforeach

    </tbody>
</table>

@section('scripts')
    @parent
    <script>
        $('.btn-nodal-historico').click(function(e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('representant_id'); //console.log(id);
            var modal = '#modal_historico'; //console.log(modal);
            var container = '#container_modal'; //console.log(container);
            var ajaxurl = '{{ route('administracion.ajax.fill.modal.representant_historico_pago', '_id_') }}';
            ajaxurl = ajaxurl.replace('_id_', id);

            var $this = $(this);
            var loadingText =
                '<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>';
            $this.data('original-text', $(this).html());
            $this.html(loadingText);

            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });
    </script>
    <script>
        $('.btn-card').click(function(e) {
            e.preventDefault();
            var row = $(this).parents('dd');
            console.log(row) //fila contentiva de la data
            var id = row.data('id');
            console.log(id);
            var modal = '#modal_card'; //console.log(modal);
            var container = '#container_modal'; //console.log(container);
            var ajaxurl = '{{ route('administracion.ajax.fill.modal.estudiant_card', '_id_') }}';
            ajaxurl = ajaxurl.replace('_id_', id);

            var $this = $(this);
            var loadingText =
                '<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>';
            $this.data('original-text', $(this).html());
            $this.html(loadingText);

            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });
    </script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.exportBootstrap') --}}

@include('administracion.datatables.exportBootstrapCustom')

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var table = $('#table-data-default').DataTable({
                "bPaginate": true,
                "lengthMenu": [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "Todos"]
                ],
                lengthChange: true,
                buttons: [{
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
                            columns: [1, 2, 3, 6, 7, 8]
                        }
                    },

                    // { extend: 'print', text: 'Imprimir' },
                    {
                        extend: 'colvis',
                        text: 'Columnas'
                    }
                ]
            });

            table.buttons().container()
                .appendTo('#table-data-default_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
