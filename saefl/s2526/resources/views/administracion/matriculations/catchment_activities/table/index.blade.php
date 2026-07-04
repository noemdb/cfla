@php
    $class['index']="";
    $class['group_id']="d-none d-lg-table-cell";
    $class['name']="d-none d-lg-table-cell";
    $class['description']="d-none d-lg-table-cell";
    $class['date']="dd-none d-lg-table-cell";
    $class['time']="d-none d-lg-table-cell";
    $class['status_active']="d-none d-lg-table-cell";
    $class['action']="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['index'] }}">N</th>
            <th class="{{ $class['group_id'] }}">{{$list_comment['group_id']}}</th>
            <th class="{{ $class['name'] }}">{{$list_comment['name']}}</th>
            <th class="{{ $class['description'] }}">{{$list_comment['description']}}</th>
            <th class="{{ $class['date'] }}">{{$list_comment['date']}}</th>
            <th class="{{ $class['time'] }}">{{$list_comment['time']}}</th>
            <th class="{{ $class['status_active'] }}">{{$list_comment['status_active']}}</th>
            <th class="{{ $class['action'] }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($catchment_activities as $activity)

        @php 
            $group = $activity->catchment_group;
            $grado = $group->grado;
            $pestudio = ($grado) ? $grado->pestudio : null ;
        @endphp

        <tr data-id="{{$activity->id}}">
            <td id="td-count" class="{{ $class['index'] ?? null }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class['group_id']  ?? null}}">
                {{ ($group) ? $group->name : null}}
                <div class="text-muted small">{{ ($grado) ? $grado->name : null}}</div>
                <div class="text-muted small">{{ ($pestudio) ? $pestudio->name : null}}</div>
            </td>
            <td class="{{ $class['name']  ?? null}}">
                {{$activity->name?? null}}
            </td>
            <td class="{{ $class['description']  ?? null}}">
                {{$activity->description?? null}}
            </td>
            <td class="{{ $class['date']  ?? null}}">
                {{$activity->date?? null}}
            </td>
            <td class="{{ $class['time']  ?? null}}">
                {{$activity->time ?? null}}
            </td>
            <td class="{{ $class['status_active']  ?? null}}">
                {{ ($activity->status_active) ? '[Activo]' : '[Desactivo]'}}
            </td>
            <td class="{{ $class['action'] ?? null }}">
                <div class="btn-activity btn-activity-sm">

                    {{-- <a name="info" id="info" class="btn btn-info disabled btn-sm" href="{{route('administracion.matriculations.catchment_activities.show',$activity->id)}}" role="button">
                        <i class="fa fa-info" aria-hidden="true"></i>
                    </a> --}}

                    <a name="edit" id="edit" class="btn btn-warning btn-sm" href="{{route('administracion.matriculations.catchment_activities.edit',$activity->id)}}" role="button">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
