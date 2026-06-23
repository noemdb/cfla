@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['fullname']="d-none d-sm-table-cell";
    $class['number_id']="d-none d-sm-table-cell";
    $class['ident']="d-none d-md-table-cell";
    $class['assit_schedule']="d-none d-md-table-cell";
    $class['action']="d-none d-sm-table-cell";
    $table_id = ($assit_schedule_id) ? 'table-data-default-'.$assit_schedule_id : 'table-data-default';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['fullname'] ?? ''}}">{{$list_comment_user['fullname'] ?? ''}}</th>
            <th class="{{ $class['number_id'] ?? ''}}">{{$list_comment_user['number_id'] ?? ''}}</th>
            <th class="{{ $class['ident'] ?? ''}}">{{$list_comment_user['ident'] ?? ''}}</th>
            {{-- <th class="{{ $class['assit_schedule'] ?? ''}}">{{$list_comment_user['assit_schedule'] ?? ''}}</th> --}}
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($workers as $user)

    @php $assit_schedule = $user->assit_schedule; @endphp

        <tr data-id="{{$user->id}}" class="{{($user->work_id == $work_id) ? 'table-secondary' : null}}">
            <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
            <td class="{{ $class['fullname'] ?? ''}}">{{$user->fullname ?? ''}}</td>
            <td class="{{ $class['number_id'] ?? ''}}">{{$user->number_id ?? ''}}</td>
            <td class="{{ $class['ident'] ?? ''}}">{{$user->ident ?? ''}}</td>
            {{-- <td class="{{ $class['assit_schedule'] ?? ''}}">{{$assit_schedule->name ?? ''}}</td> --}}

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <button wire:click="edit({{$user->rol_id}})" class="btn btn-warning btn-sm"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>
