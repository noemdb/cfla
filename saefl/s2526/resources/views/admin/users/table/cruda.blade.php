<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-ajax" data-url="{{ url('api/users') }}">
    <thead>
        <tr>
            <th width="10px">ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Active</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th width="120px">&nbsp;</th>
        </tr>
    </thead>

</table>

{!! Form::open(['route' => ['users.destroy',':USER_ID'], 'method' => 'DELETE', 'id'=>'form-delete', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/users/delete.js") }}"></script>
@endsection


@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/users/cruda.js") }}"></script>

    <script>
        $(document).ready(function() {
            // data_tables_ajax( "{{ route('users.cruda') }}" );
            data_tables_ajax( "{{ route('api.admin.users.cruda') }}" );
        });
    </script>


@endsection
