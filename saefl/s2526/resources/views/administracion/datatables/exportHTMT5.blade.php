@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/export/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/export/buttons.bootstrap4.min.css') }}">

@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/export/pdfmake.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/export/vfs_fonts.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/export/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/export/dataTables.bootstrap4.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/export/dataTables.buttons.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/export/buttons.bootstrap4.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/export/buttons.flash.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/export/buttons.html5.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/concatenate/buttons.colVis.min.js") }}"></script>
    <script src="{{ asset("vendor/datatables/plugins/currency.js") }}"></script>
    {{-- /home/nuser/code/s2021/public/vendor/datatables/plugin/currency.js --}}

    <script>

        $(document).ready(function() {
            $('#table-data-default').DataTable( {
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100 , 500, -1], [10, 25, 50, 100 , 500, "Todos"]],
                "responsive": false,
                // "searching": false,
                "columnDefs": [ {
                    "targets": 'nosort',
                    "orderable": false
                } ],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                },
                dom: 'Blfrtip',
                // dom: '<"top"Bf>rt<"bottom"lip><"clear">',
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
                        text: 'CSV',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: [ 0,1,3,5,6]
                        }
                    },
                    // { extend: 'print', text: 'Imprimir' },
                    { extend: 'colvis', text: 'Columnas' }
                ]
                // "dom": '<"top"ifl>rt<"bottom"p><"clear">',
            } );
        } );
    </script>

@endsection

{{--


                buttons: [
                    { extend: 'excel', text: 'Excel' },
                    { extend: 'csv', text: 'CSV' },
                    { extend: 'print', text: 'Imprimir' }
                ]


$(document).ready(function() {
    $('#table-data-default').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            // 'copy', 'csv', 'excel', 'pdf', 'print'
            'copy', 'excel', 'print'
        ]
    } );
} );



--}}
