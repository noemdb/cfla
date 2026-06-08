@php ($class_N="")
@php ($class_username="")
@php ($class_laction="d-none d-sm-table-cell")
@php ($class_model_class="")
@php ($class_ip="")
@php ($class_created_at="d-none d-lg-table-cell")
@php ($class_updated_at="d-none d-lg-table-cell")
@php ($class_action="nosort text-center")

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_username }}">Username</th>
            {{-- <th class="{{ $class_username }}">URL</th> --}}
            <th class="{{ $class_laction ?? ''}}">Action</th>
            <th class="{{ $class_model_class ?? ''}}">Model Class</th>
            {{-- <th class="{{ $class_ip ?? ''}}">IP</th> --}}
            <th class="{{ $class_ip ?? ''}}">DATA</th>
            <th class="{{ $class_created_at ?? ''}}">Creado</th>
            {{-- <th class="{{ $class_updated_at ?? '' }}">Actualizado</th> --}}
            <th align="right" class="{{$class_action}}"><strong>Aciones</strong></th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($logdbs as $logdb)
        {{-- @foreach($logdbs->take(10) as $logdb) --}}

            @php ($user = $logdb->user)

            <tr data-logdb="{{$logdb->id ?? ''}}" data-user="{{$user->id ?? ''}}" class="table-{{ $logdb->tipo ?? '' }} p-0 m-0">

                <td>
                    {{ ($loop->index + 1) }}
                </td>
                <td>
                    {{$user->username ?? ''}}
                </td>

                <td  id="td-logdbs-action-{{$logdb->id ?? ''}}" title="{{ $logdb->action ?? ''}} " class="{{ $class_laction ?? ''}}">
                    {{$logdb->url ?? ''}}
                    {{-- {{$logdb->action ?? ''}} --}}
                </td>

                <td  id="td-logdbs-model_class-{{$logdb->id ?? ''}}" title="{{ $logdb->model_class ?? ''}}"  class="{{ $class_model_class ?? ''}}">
                    {{$logdb->model_class ?? ''}}
                </td>

                <td  id="td-logdbs-ip-{{$logdb->id ?? ''}}" title="{{ $logdb->ip ?? ''}} " class="{{ $class_ip ?? ''}}">
                    {{$logdb->data ?? ''}}
                    @php ($data = explode('=>',$logdb->data))
                    
                    {{-- {{print_r($data,true)}} --}}

                    {{-- {{$logdb->ip ?? ''}} --}}
                </td>

                <td id="td-logdbs-created_at-{{ $logdb->id ?? ''}}" class="{{ $class_created_at ?? ''}}">
                    {{ (isset($logdb->created_at)) ? Carbon\Carbon::parse($logdb->created_at)->format('d-m-Y') : '' }}
                </td>

                {{-- boton para mostrar en un modal de info de regsitro --}}

                <td style="padding: 2px; vertical-align: middle;" id="btn-action-{{ $logdb->id }}" class="text-center">

                    <div class="btn-group btn-group-sm ">

                        <a title="Mostrar detalles" class="btn btn-info btn-xs" href="{{ route('logdbs.show',$logdb->id) }}">
                            <i class="fas fa-info"></i>
                        </a>

                        {{-- 
                        <a title="Editar resgistro" class="btn btn-warning btn-xs btn-action-group-{{ $logdb->id }}" href="{{ route('logdbs.edit',$logdb->id) }}" id="btn-edituser_{{$logdb->id}}">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <a title="Eliminar {{(isset($logdb->deleted_at) ? 'DEFINITIVAMENTE':'')}}" class="btn-delete btn btn-danger btn-xs" href="{{ route('logdbs.destroy',$logdb->id) }}" id="btn-delete-taskid_{{$logdb->id}}">
                            <i class="fas fa-trash"></i>
                        </a> 
                        --}}

                    </div>

                </td>


            </tr>
        @endforeach
    </tbody>
</table>

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection