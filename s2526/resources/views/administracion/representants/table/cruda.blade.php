@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="text-nowrap";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_contacto="d-none d-lg-table-cell";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-ajax">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_ci }}">Identificador</th>
                <th class="{{ $class_representant }}">Representante</th>
                <th class="{{ $class_contacto }}">Información de contacto</th>
            </tr>
        </thead>

    </table>

{{-- </div> --}}

@section('stylesheet')
    @parent

    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">

@endsection

@section('scripts')
    @parent

    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js") }}"></script>
    {{-- <script src="{{ asset("js/models/datatable/default.js") }}"></script> --}}

    <script>
        $(document).ready(function() {
            $('#table-data-ajax').DataTable({
                "serverSide": true,
                "ajax": "{{ url('api/users') }}",
                "columns": [
                    {data: 'N'},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'btn'},
                ],
                "language": {
                    "info": "_TOTAL_ registros",
                    "search": "Buscar",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior",
                    },
                    "lengthMenu": 'Mostrar <select >'+
                                '<option value="10">10</option>'+
                                '<option value="30">30</option>'+
                                '<option value="-1">Todos</option>'+
                                '</select> registros',
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "emptyTable": "No hay datos",
                    "zeroRecords": "No hay coincidencias",
                    "infoEmpty": "",
                    "infoFiltered": ""
                }
            });
        });
    </script>

@endsection
