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

@endsection
