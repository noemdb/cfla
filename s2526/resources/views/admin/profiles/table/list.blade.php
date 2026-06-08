@php ($class_N="d-none d-sm-block")
@php ($class_user="")
@php ($class_name="")
@php ($class_state="")
@php ($class_action="nosort")

<table width="100%" class="table table-striped table-hover table-sm" id="table-data-profile">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_user }}">Usuario</th>
            <th class="{{ $class_name }}">Nombre</th>
            <th class="{{ $class_state }}">Estado</th>
            <th class="{{ $class_action }}">Aciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">

    @php ($n=1)

    @foreach($profiles as $profile)

        @php ($user = $profile->user)
        
        <tr data-profile="{{$profile->id}}" data-user="{{$user->id ?? ''}}">

            <td class="{{ $class_N }}">
                {{$n++}}
            </td>

            <td class="{{ $class_user }}">
                <span class="text-users-username-{{ $user->id ?? '' }}">
                    {{$user->username ?? ''}}
                </span>
            </td>
            <td  id="td-fullname-{{$profile->id}}" class="{{ $class_name }}">
                <span class="text-profiles-firstname-{{ $profile->id }}">{{$profile->firstname}}</span>
                <span class="text-profiles-lastname-{{ $profile->id }}">{{$profile->lastname}}</span>                
            </td>

            <td id="td-users-is_active-{{ $user->id ?? ''}}" class="{{ $class_state }}">
                <span class="text-users-is_active-{{ $user->id ?? '' }} text-{{ $user->is_active ?? '' }}">
                    {{$user->is_active ?? ''}}
                </span>
            </td>


            <td  class="{{ $class_action }}" id="btn-action-{{ $profile->id }}">
                
                <div class="btn-group btn-group-sm">
                    
                    {{-- boton para mostrar en un modal de info de regsitro --}}

                    <a title="Mostrar detalles" class="btn btn-info btn-xs" href="{{ route('profiles.show',$profile->id) }}">
                        <i class="fas fa-info"></i>
                    </a>

                    <a title="Editar resgistro" class="btn btn-warning btn-xs btn-action-group-{{ $profile->id }}" href="{{ route('profiles.edit',$profile->id) }}" id="btn-edituser_{{$profile->id}}">
                        <i class="fas fa-pencil-alt"></i>
                    </a>

                    <a title="Eliminar {{(isset($profile->deleted_at) ? 'DEFINITIVAMENTE':'')}}" class="btn-delete btn btn-danger btn-xs" href="{{ route('profiles.destroy',$profile->id) }}" id="btn-delete-profileid_{{$user->id}}">
                        <i class="fas fa-trash"></i>
                    </a>

                </div>
                
            </td>

            
        </tr>
        @endforeach
    </tbody>
</table>

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
            $('#table-data-profile').DataTable( {
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