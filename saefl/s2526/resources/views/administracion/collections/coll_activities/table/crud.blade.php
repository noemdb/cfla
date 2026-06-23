@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']="d-none d-sm-table-cell";
    $class['coll_nivel_id']="d-none d-sm-table-cell";
    $class['representant_id']="d-none d-sm-table-cell";
    $class['description']="d-none d-md-table-cell";
    $class['status_id']="d-none d-sm-table-cell";
    $class['status_messege']="d-none d-sm-table-cell";
    $class['status_call']="d-none d-sm-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{-- 'user_id','coll_nivel_id','representant_id','description','status_id','status_messege','status_call' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['user_id'] ?? ''}}">{{$list_comment['user_id'] ?? ''}}</th>
            <th class="{{ $class['coll_nivel_id'] ?? ''}}">{{$list_comment['coll_nivel_id'] ?? ''}}</th>
            <th class="{{ $class['representant_id'] ?? ''}}">{{$list_comment['representant_id'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['status_id'] ?? ''}}">{{$list_comment['status_id'] ?? ''}}</th>
            <th class="{{ $class['status_messege'] ?? ''}}">{{$list_comment['status_messege'] ?? ''}}</th>
            <th class="{{ $class['status_call'] ?? ''}}">{{$list_comment['status_call'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($coll_activities as $coll_activity)

    @php
        $user = ($coll_activity->user) ? $coll_activity->user : null;
        $coll_nivel = ($coll_activity->coll_nivel) ? $coll_activity->coll_nivel : null;
        $representant = ($coll_activity->representant) ? $coll_activity->representant : null;
        $status = ($coll_activity->status) ? $coll_activity->status : null;
    @endphp

        <tr data-id="{{$coll_activity->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['user_id'] ?? ''}}">{{ ($user) ? $user->username : null}}</td>
            <td class="{{ $class['coll_nivel_id'] ?? ''}}">{{ ($coll_nivel) ? $coll_nivel->fullname : null}}</td>
            <td class="{{ $class['representant_id'] ?? ''}}">{{ ($representant) ? $representant->name : null}}</td>
            <td class="{{ $class['description'] ?? ''}}">{{$coll_activity->description ?? ''}}</td>
            <td class="{{ $class['status_id'] ?? ''}}">{{ ($status) ? $status->name : null}}</td>
            <td class="{{ $class['status_messege'] ?? ''}}">{{ ($coll_activity->status_messege=='true') ? 'SI':'NO' }}</td>
            <td class="{{ $class['status_call'] ?? ''}}">{{ ($coll_activity->status_call=='true') ? 'SI':'NO' }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.collections.coll_activities.edit',$coll_activity->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$coll_activity->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.collections.coll_activities.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
