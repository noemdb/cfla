@php
    $class['index']="";
    $class['grado_id']="d-none d-lg-table-cell";
    $class['name']="d-none d-lg-table-cell";
    $class['description']="d-none d-lg-table-cell";
    $class['max']="dd-none d-lg-table-cell";
    $class['min']="d-none d-lg-table-cell";
    $class['status_active']="d-none d-lg-table-cell";
    $class['action']="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['index'] }}">N</th>
            <th class="{{ $class['grado_id'] }}">{{$list_comment['grado_id']}}</th>
            <th class="{{ $class['name'] }}">{{$list_comment['name']}}</th>
            <th class="{{ $class['description'] }}">{{$list_comment['description']}}</th>
            <th class="{{ $class['max'] }}">{{$list_comment['max']}}</th>
            <th class="{{ $class['min'] }}">{{$list_comment['min']}}</th>
            <th class="{{ $class['status_active'] }}">{{$list_comment['status_active']}}</th>
            <th class="{{ $class['action'] }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($catchment_groups as $group)

        @php 
            $grado = $group->grado;
            $pestudio = ($grado) ? $grado->pestudio : null ;
        @endphp

        <tr data-id="{{$group->id}}">
            <td id="td-count" class="{{ $class['index'] ?? null }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class['grado_id']  ?? null}}">
                {{ ($grado) ? $grado->name : null}}
                <div class="text-muted small">{{ ($pestudio) ? $pestudio->name : null}}</div>
            </td>
            <td class="{{ $class['name']  ?? null}}">
                {{$group->name?? null}}
            </td>
            <td class="{{ $class['description']  ?? null}}">
                {{$group->description?? null}}
            </td>
            <td class="{{ $class['max']  ?? null}}">
                {{$group->max?? null}}
            </td>
            <td class="{{ $class['min']  ?? null}}">
                {{$group->min ?? null}}
            </td>
            <td class="{{ $class['status_active']  ?? null}}">
                {{ ($group->status_active) ? '[Activo]' : '[Desactivo]'}}
            </td>
            <td class="{{ $class['action'] ?? null }}">
                <div class="btn-group btn-group-sm">

                    {{-- <a name="info" id="info" class="btn btn-info disabled btn-sm" href="{{route('administracion.matriculations.catchment_groups.show',$group->id)}}" role="button">
                        <i class="fa fa-info" aria-hidden="true"></i>
                    </a> --}}

                    <a name="edit" id="edit" class="btn btn-warning btn-sm" href="{{route('administracion.matriculations.catchment_groups.edit',$group->id)}}" role="button">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
