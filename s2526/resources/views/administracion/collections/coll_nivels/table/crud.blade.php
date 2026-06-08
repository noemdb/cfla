@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['coll_political_id']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['order']="d-none d-sm-table-cell";
    $class['weighing']="d-none d-sm-table-cell";
    $class['description']="d-none d-md-table-cell";
    $class['status']="d-none d-sm-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{-- 'coll_political_id','name','order','weighing','description','status' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['coll_political_id'] ?? ''}}">{{$list_comment['coll_political_id'] ?? ''}}</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['order'] ?? ''}}">{{$list_comment['order'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['weighing'] ?? ''}}">{{$list_comment['weighing'] ?? ''}}</th>
            <th class="{{ $class['status'] ?? ''}}">{{$list_comment['status'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($coll_nivels as $coll_nivel)

        <tr data-id="{{$coll_nivel->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['coll_political_id'] ?? ''}}">{{ $coll_nivel->coll_political->fullname ?? '' }}</td>
            <td class="{{ $class['name'] ?? ''}}">{{$coll_nivel->name ?? ''}}</td>
            <td class="{{ $class['order'] ?? ''}}">{{$coll_nivel->order ?? ''}}</td>
            <td class="{{ $class['description'] ?? ''}}">{{$coll_nivel->description ?? ''}}</td>
            <td class="{{ $class['weighing'] ?? ''}}">{{  $coll_nivel->weighing ?? '' }}</td>
            <td class="{{ $class['status'] ?? ''}}">{{ ($coll_nivel->status=='true') ? 'Activo':'Desactivo' }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.collections.coll_nivels.edit',$coll_nivel->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$coll_nivel->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.collections.coll_nivels.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

<div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

