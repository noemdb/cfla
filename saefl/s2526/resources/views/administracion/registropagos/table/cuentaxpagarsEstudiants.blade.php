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
    {{-- <caption style="caption-side: top">Listado de Representantes con conceptos de cobro pendiente {{ $cuentaxpagar->name ?? '' }} - Total: {{ $representants->count() ?? ''}} - Montos en Divisas ($)</caption> --}}
    {{-- <caption style="caption-side: top">Listado de Representantes con conceptos de cobro pendiente</caption> --}}
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_deuda }}">CI</th>
            <th class="{{ $class_deuda }}" title="Deuda Total">D.Total</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach ($datas as $k => $data)
            @php
                //$estudiant = $allEstudiants['estudiant'];
                //dd($estudiant);
                // $monto_total = $estudiants->sum('monto');
                $estudiant = $data['estudiant']; //dd($monto_exchange);
                $monto_exchange = $data['monto_exchange'];


            @endphp

            @if ($monto_exchange > 0)
                <tr >
                    <td class="{{ $class_N }}">{{ $loop->iteration }}</td>

                    <td class="{{ $class_ci }}">
                        <span class=" font-weight-bold">{{ $estudiant->fullname ?? '' }}</span>
                    </td>

                    <td class="{{ $class_estudiant }}">
                        {{ $estudiant->ci_estudiant ?? '' }}
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        <span class="{{ $monto_exchange <= 0 ? 'text-danger font-weight-bold' : null }}">
                            {{$monto_exchange}}
                        </span>
                    </td>
                </tr>
            @endif
        @endforeach

    </tbody>
</table>

{{-- <div id="">
    Total deuda : {{$total_deuda }}
</div> --}}

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
