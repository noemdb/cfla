@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['area']="d-none d-sm-table-cell";
    $class['description']="d-none d-sm-table-cell";
    $class['platform']="d-none d-sm-table-cell";
    $class['header_key']="d-none d-md-table-cell";
    $class['header_value']="d-none d-sm-table-cell";
    $class['user']="d-none d-sm-table-cell";
    $class['password']="d-none d-sm-table-cell";
    $class['status_active']="d-none d-sm-table-cell";
@endphp

{{-- 'name','description','platform','header_key','header_value','user','password','status_active' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            @admin<th class="{{ $class['area'] ?? ''}}">{{$list_comment['area'] ?? ''}}</th>@endadmin
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['platform'] ?? ''}}">{{$list_comment['platform'] ?? ''}}</th>
            @admin<th class="{{ $class['header_key'] ?? ''}}">{{$list_comment['header_key'] ?? ''}}</th>@endadmin
            @admin<th class="{{ $class['header_value'] ?? ''}}">{{$list_comment['header_value'] ?? ''}}</th>@endadmin
            @admin<th class="{{ $class['user'] ?? ''}}">{{$list_comment['user'] ?? ''}}</th>@endadmin
            @admin<th class="{{ $class['password'] ?? ''}}">{{$list_comment['password'] ?? ''}}</th>@endadmin
            <th class="{{ $class['status_active'] ?? ''}}">{{$list_comment['status_active'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($bmains as $bmain)

        <tr data-id="{{$bmain->id}}" class="{{ ($bmain->status_active=='true') ? 'table-success' : null }}">
            <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
            <td class="{{ $class['name'] ?? ''}}">{{$bmain->name ?? ''}}</td>
            @admin<td class="{{ $class['area'] ?? ''}}">{{$bmain->area ?? ''}}</td>@endadmin
            <td class="{{ $class['description'] ?? ''}}">{{$bmain->description ?? ''}}</td>
            <td class="{{ $class['platform'] ?? ''}}">{{$bmain->platform ?? ''}}</td>
            @admin<td class="{{ $class['header_key'] ?? ''}}">{{$bmain->header_key ?? ''}}</td>@endadmin
            @admin<td class="{{ $class['header_value'] ?? ''}}">{{$bmain->header_value ?? ''}}</td>@endadmin
            @admin<td class="{{ $class['user'] ?? ''}}">{{ $bmain->user ?? '' }}</td>@endadmin
            @admin<td class="{{ $class['password'] ?? ''}}">{{ $bmain->password ?? '' }}</td>@endadmin
            <td class="{{ $class['status_active'] ?? ''}}">{{ $bmain->status_active ?? '' }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a header_key="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.autoresponders.bmains.edit',$bmain->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        <a header_key="Mostrar vista previa del mensaje" class="btn-preview btn btn-info btn-sm"  href="#" role="button">
                            <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
                        </a>

                        {{-- <a header_key="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$bmain->id}}">
                            <i class="fas fa-trash"></i>
                        </a> --}}

                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.autoresponders.bmains.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

