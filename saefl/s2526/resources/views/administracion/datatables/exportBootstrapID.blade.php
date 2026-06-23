@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/exportBootstrap/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/exportBootstrap/buttons.bootstrap4.min.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/pdfmake.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/vfs_fonts.js") }}"></script>

    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/dataTables.bootstrap4.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/dataTables.buttons.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/buttons.bootstrap4.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/jszip.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/buttons.html5.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/buttons.print.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/exportBootstrap/buttons.colVis.min.js") }}"></script>
    {{-- <script src="{{ asset("vendor/datatables/plugins/currency.js") }}"></script> --}}

    <script>

        $(document).ready(function() {
            var table = $('{{$table_id}}').DataTable( {
                // "pageLength": 10,
                "bPaginate": true,
                "lengthMenu": [[10, 25, 50, 100 , 500, -1], [10, 25, 50, 100 , 500, "Todos"]],
                lengthChange: true,
                // columnDefs: [ { type: 'currency', targets: 6 } ],
                // language: {
                //     "url": "/vendor/datatables/lang/spanish.json"
                // },
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
                        // exportOptions: {
                        //     columns: [ 1,2,3,6,7,8]
                        // }
                    },

                    // { extend: 'print', text: 'Imprimir' },
                    { extend: 'colvis', text: 'Columnas' }
                ]
            } );

            table.buttons().container()
                .appendTo( '#{{$table_id}}_wrapper .col-md-6:eq(0)' );
        } );

    </script>

@endsection
