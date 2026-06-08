@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']="d-none d-sm-table-cell";
    $class['coll_nivel_id']="d-none d-sm-table-cell";
    $class['representant_id']="d-none d-sm-table-cell";
    $class['exchange_ammount']="d-none d-sm-table-cell";
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
            {{-- <th class="{{ $class['user_id'] ?? ''}}">{{$list_comment['user_id'] ?? ''}}</th> --}}
            {{-- <th class="{{ $class['coll_nivel_id'] ?? ''}}">{{$list_comment['coll_nivel_id'] ?? ''}}</th> --}}
            {{-- <th class="{{ $class['representant_id'] ?? ''}}">CI</th> --}}
            <th class="{{ $class['representant_id'] ?? ''}}">Representante</th>
            {{-- <th class="{{ $class['representant_id'] ?? ''}}">Empleado</th> --}}
            <th class="{{ $class['representant_id'] ?? ''}}">Email</th>
            <th class="{{ $class['representant_id'] ?? ''}}">GSEmail</th>
            <th class="{{ $class['exchange_ammount'] ?? ''}}">Deuda</th>
            {{-- <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th> --}}
            {{-- <th class="{{ $class['status_id'] ?? ''}}">{{$list_comment['status_id'] ?? ''}}</th> --}}
            <th class="{{ $class['status_messege'] ?? ''}}">Notificación Email</th>
            <th class="{{ $class['status_call'] ?? ''}}">Notificación Telefónica</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($representants as $item)

    @php
        // $user = ($coll_activity->user) ? $coll_activity->user : null;
        // $coll_nivel = ($coll_activity->coll_nivel) ? $coll_activity->coll_nivel : null;
        $representant = $item['representant'];
        $exchange_ammount = $item['exchange_ammount'];
        // $status = ($coll_activity->status) ? $coll_activity->status : null;
    @endphp

        <tr data-id="{{$representant->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
            {{-- <td class="{{ $class['user_id'] ?? ''}}">{{ ($user) ? $user->username : null}}</td> --}}
            {{-- <td class="{{ $class['coll_nivel_id'] ?? ''}}">{{ ($coll_nivel) ? $coll_nivel->fullname : null}}</td> --}}
            {{-- <td class="{{ $class['representant_id'] ?? ''}}">{{ $representant->ci_representant }}</td> --}}
            <td class="{{ $class['representant_id'] ?? ''}}">{{ $representant->name }}<br>{{ $representant->ci_representant }}</td>
            {{-- <td class="{{ $class['representant_id'] ?? ''}}">{{ ($representant->status_employ) ? 'SI':'NO' }}</td> --}}
            <td class="{{ $class['representant_id'] ?? ''}}">{{ $representant->email }}</td>
            <td class="{{ $class['representant_id'] ?? ''}}">{{ $representant->gsemail }}</td>
            <td class="{{ $class['exchange_ammount'] ?? ''}}" title="{{$exchange_ammount}}">$ {{f_float($exchange_ammount)}}</td>
            {{-- <td class="{{ $class['description'] ?? ''}}">{{$coll_activity->description ?? ''}}</td> --}}
            {{-- <td class="{{ $class['status_id'] ?? ''}}">{{ ($status) ? $status->name : null}}</td> --}}
            <td class="{{ $class['status_messege'] ?? ''}}">{{ ($representant->status_messege=='true') ? 'SI':'NO' }}</td>
            <td class="{{ $class['status_call'] ?? ''}}">{{ ($representant->status_call=='true') ? 'SI':'NO' }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a title="Enviar notificación" class="btn btn-success btn-sm"  href="{{route('email.collections.coll_politicals',[$representant->id,1])}}" role="button">
                            <i class="{{ $icon_menus['mail'] ?? ''}} fa-1x"></i>
                        </a>
                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
