@php ($class_N="d-none d-sm-table-cell")
@php ($class_user="")
@php ($class_email="d-none d-lg-table-cell")
@php ($class_state="")
@php ($class_rols="")
@php ($class_area="d-none d-md-table-cell")
@php ($class_action="nosort")

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-user">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_user }}">Usuario</th>
            <th class="{{ $class_email }}">Email</th>
            <th class="{{ $class_state }}">Estado</th>
            <th class="{{ $class_rols }}"><strong>Rol</strong></th>
            <th class="{{ $class_area }}" title="area">Área</th>
            <th class="{{ $class_action }}">Aciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @php ($n=1)
    @foreach($users as $user)

        @php ($profile = $user->profile)

        @php ($rol = $user->rols->last())

        <tr data-user="{{$user->id}}" data-profile="{{$profile->id ?? ''}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$n++}}
            </td>
            <td id="td-users-username-{{ $user->id }}" class="{{ $class_user  ?? ''}}">
                <span class="text-users-username-{{ $user->id }} text-{{ $user->is_active }}">
                    {{$user->username}}
                </span>
            </td>
            <td id="td-profiles-email-{{ $user->id }}" class="{{ $class_email ?? '' }}">
                <span class="text-profiles-email-{{ $profile->id ?? ''}}">
                    {{ $user->email ?? ''}}
                </span>
            </td>

            <td id="td-users-is_active-{{ $user->id }}" class="{{ $class_state ?? '' }}">
                <span class="text-users-is_active-{{ $user->id }} text-{{ $user->is_active }}">
                    {{$user->is_active}}
                </span>
            </td>

            <td id="td-rols-area-{{$user->id}}" class="{{ $class_rols ?? '' }}">
                @isset($rol)
                    <span class="text-rols-rol-{{ $rol['id'] }} rol-{{ $rol['rol'] ?? '' }}">
                        {{$rol['rol']}}
                    </span>
                @endisset
            </td>

            <td id="td-area-{{$user->id}}" class="{{ $class_area ?? '' }}">
                @isset($rol)
                    <span class="text-rols-area-{{ $rol['id'] }} area-{{ $rol['area'] ?? '' }}">
                        {{$rol['area']}}
                    </span>
                @endisset
            </td>

            <td class="{{ $class_action }}" id="btn-action-{{ $user->id }}">
                <div class="btn-group btn-group-sm">

                    <a title="Mostrar detalles" class="btn btn-info btn-xs" href="{{ route('users.show',$user->id) }}">
                        <i class="fas fa-info"></i>
                    </a>

                    <a title="Editar resgistro" class="btn btn-warning btn-xs btn-action-group-{{ $user->id }}" href="{{ route('users.edit',$user->id) }}" id="btn-edituser_{{$user->id}}">
                        <i class="fas fa-pencil-alt"></i>
                    </a>

                    <a title="Eliminar {{(isset($user->deleted_at) ? 'DEFINITIVAMENTE':'')}}" class="btn-delete btn btn-danger btn-xs" href="{{ route('users.destroy',$user->id) }}" id="btn-delete-userid_{{$user->id}}">
                        <i class="fas fa-trash"></i>
                    </a>

                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
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

   <script type="text/javascript" class="init">

        $(document).ready(function() {
            $('#table-data-user').DataTable( {
                "pageLength": 10,
                "responsive": false,
                // "searching": false,
                "columnDefs": [ {
                    "targets": 'nosort',
                    "orderable": false
                } ],
                "language": {
                    "url": "{{ asset("vendor/datatables/lang/spanish.json") }}"
                },
                // "dom": '<"top"ifl>rt<"bottom"p><"clear">',
            } );
        } );

   </script>

@endsection
