@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['pescolar_id']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['code']="d-none d-sm-table-cell";
    $class['description']="d-none d-md-table-cell";
    $class['finicial']="d-none d-sm-table-cell";
    $class['ffinal']="d-none d-sm-table-cell";
    $class['status']="d-none d-sm-table-cell";
    $class['status_approved']="d-none d-sm-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{-- 'pescolar_id','name','code','description','finicial','ffinal','status','status_approved' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['pescolar_id'] ?? ''}}">{{$list_comment['pescolar_id'] ?? ''}}</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['code'] ?? ''}}">{{$list_comment['code'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['finicial'] ?? ''}}">{{$list_comment['finicial'] ?? ''}}</th>
            <th class="{{ $class['ffinal'] ?? ''}}">{{$list_comment['ffinal'] ?? ''}}</th>
            <th class="{{ $class['status'] ?? ''}}">{{$list_comment['status'] ?? ''}}</th>
            <th class="{{ $class['status_approved'] ?? ''}}">{{$list_comment['status_approved'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($coll_politicals as $coll_political)

        <tr data-id="{{$coll_political->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['pescolar_id'] ?? ''}}">{{$coll_political->pescolar->name ?? ''}}</td>
            <td class="{{ $class['name'] ?? ''}}">{{$coll_political->name ?? ''}}</td>
            <td class="{{ $class['code'] ?? ''}}">{{$coll_political->code ?? ''}}</td>
            <td class="{{ $class['description'] ?? ''}}">{{$coll_political->description ?? ''}}</td>
            <td class="{{ $class['finicial'] ?? ''}}">{{  f_date($coll_political->finicial) }}</td>
            <td class="{{ $class['ffinal'] ?? ''}}">{{ f_date($coll_political->ffinal) }}</td>
            <td class="{{ $class['status'] ?? ''}}">{{ ($coll_political->status=='true') ? 'Activa':'Desactiva' }}</td>
            <td class="{{ $class['status_approved'] ?? ''}}">{{ ($coll_political->status_approved=='true') ? 'Aprobada':'Desaprobada' }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.collections.coll_politicals.edit',$coll_political->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$coll_political->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.collections.coll_politicals.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

<div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

