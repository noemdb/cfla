
<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-ajax" data-url="{{ url('api/users') }}">
    <thead>
        <tr>
            <th width="10px">Estudiante</th>
            <th>Fecha</th>
            <th>Concepto Cobro</th>
            <th>Pagado (Bs.)</th>
            {{-- <th>CAF</th> --}}
            <th width="120px">Acción</th>
        </tr>
    </thead>

</table>

{!! Form::open(['route' => ['administracion.registropagos.anular',':REGISTROPAGO_ID'], 'method' => 'POST', 'id'=>'form-registro-pago-anular', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/registropagos/anular.js") }}"></script>
@endsection

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/registropagos/cruda.js") }}"></script>

    <script>
        $(document).ready(function() {
            data_tables_ajax( "{{ route('api.administracion.registropagos.cruda') }}" );
        });
    </script>


@endsection
