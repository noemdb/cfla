@php ($class_N="")
@php ($class_name="d-flex flex-md-wrap")
@php ($class_value="d-none d-sm-table-cell")
@php ($class_created_at="d-none d-md-table-cell")
@php ($class_updated_at="d-none d-md-table-cell")
@php ($class_action="nosort text-center")

<table width="100%" class="table {{-- table-striped table-hover --}} table-sm" id="table-data-settings">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th>Usuario</th>
            <th class="{{ $class_name ?? ''}}">Nombre</th>
            <th class="{{ $class_value ?? ''}}">Valor</th>
            <th class="{{ $class_created_at ?? ''}}">Creado</th>
            <th class="{{ $class_updated_at ?? '' }}">Actualizado</th>
            <th align="right" class="{{$class_action}}"><strong>Aciones</strong></th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($settings as $setting)

            @php ($user = $setting->user)

            <tr data-setting="{{$setting->id ?? ''}}" data-user="{{$user->id ?? ''}}" class="table-{{ $setting->tipo ?? '' }} p-0 m-0">

                <td>
                    {{ ($loop->index + 1) }}
                </td>

                <td id="td-users-username-{{ $user->id ?? '' }}">
                    {{$user->username ?? ''}}
                </td>

                <td  id="td-settings-name-{{$setting->id ?? ''}}" title="{{ $setting->name ?? ''}} " class="{{ $class_name ?? ''}}">
                    {{$setting->name ?? ''}}
                </td>

                <td  id="td-settings-value-{{$setting->id ?? ''}}" title="{{ $setting->value ?? ''}}"  class="{{ $class_value ?? ''}}">
                    {{$setting->value ?? ''}}
                </td>

                <td id="td-settings-created_at-{{ $setting->id ?? ''}}" class="{{ $class_created_at ?? ''}}">
                    {{ (isset($setting->created_at)) ? Carbon\Carbon::parse($setting->created_at)->format('d-m-Y') : '' }}
                </td>

                <td id="td-settings-updated_at-{{ $setting->id ?? ''}}" class="{{ $class_updated_at ?? ''}}">
                    {{ (isset($setting->updated_at)) ? Carbon\Carbon::parse($setting->updated_at)->format('d-m-Y') : '' }}
                </td>

                <td style="padding: 2px; vertical-align: middle;" id="btn-action-{{ $setting->id }}" class="text-center">

                    <div class="btn-group btn-group-sm ">

                        {{-- boton para mostrar en un modal de info de regsitro --}}

                        <a title="Mostrar detalles" class="btn btn-info btn-xs" href="{{ route('settings.show',$setting->id) }}">
                            <i class="fas fa-info"></i>
                        </a>

                        <a title="Editar resgistro" class="btn btn-warning btn-xs btn-action-group-{{ $setting->id }}" href="{{ route('settings.edit',$setting->id) }}" id="btn-edituser_{{$setting->id}}">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <a title="Eliminar {{(isset($setting->deleted_at) ? 'DEFINITIVAMENTE':'')}}" class="btn-delete btn btn-danger btn-xs" href="{{ route('settings.destroy',$setting->id) }}" id="btn-delete-taskid_{{$setting->id}}">
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
            $('#table-data-settings').DataTable( {
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