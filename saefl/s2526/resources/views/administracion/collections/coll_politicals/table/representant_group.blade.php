@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['ci_representant']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['phone']="d-none d-sm-table-cell";
    $class['cellphone']="d-none d-md-table-cell";
    $class['pmovilphone']="d-none d-md-table-cell";
    $class['email']="d-none d-sm-table-cell";
@endphp

{{-- 'user_id','ci_representant','name','phone','cellphone','email','gsemail','status_active' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['ci_representant'] ?? ''}}">{{$list_comment['ci_representant'] ?? ''}}</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['phone'] ?? ''}}">{{$list_comment['phone'] ?? ''}}</th>
            <th class="{{ $class['cellphone'] ?? ''}}">{{$list_comment['cellphone'] ?? ''}}</th>
            <th class="{{ $class['pmovilphone'] ?? ''}}">{{$list_comment['pmovilphone'] ?? ''}}</th>
            <th class="{{ $class['email'] ?? ''}}">{{$list_comment['email'] ?? ''}}</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($representants as $representant)

        <tr data-id="{{$representant->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['ci_representant'] ?? ''}}">{{$representant->ci_representant ?? ''}}</td>
            {{-- <td class="{{ $class['name'] ?? ''}}">{{$representant->name ?? ''}}</td> --}}
            <td class="{{ $class['name'] ?? ''}}">@include('administracion.representants.partials.href')</td>
            <td class="{{ $class['phone'] ?? ''}}">{{$representant->phone ?? ''}}</td>
            <td class="{{ $class['cellphone'] ?? ''}}">{{$representant->description ?? ''}}</td>
            <td class="{{ $class['pmovilphone'] ?? ''}}">{{$representant->pmovilphone ?? ''}}</td>
            <td class="{{ $class['email'] ?? ''}}">{{ $representant->email }}</td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

