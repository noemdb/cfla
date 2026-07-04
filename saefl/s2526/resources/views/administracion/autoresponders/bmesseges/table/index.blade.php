@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['bmain_id']="d-none d-sm-table-cell";
    $class['app_package_name']="d-none d-sm-table-cell";
    $class['messenger_package_name']="d-none d-sm-table-cell";
    $class['sender']="d-none d-sm-table-cell";
    $class['message']="d-none d-md-table-cell";
    $class['created_at']="d-none d-sm-table-cell";
@endphp

{{-- 'app_package_name','messenger_package_name','sender','message','is_group','rule_id', --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['bmain_id'] ?? ''}}">{{$list_comment['bmain_id'] ?? ''}}</th>
            <th class="{{ $class['created_at'] ?? ''}}">{{$list_comment['created_at'] ?? ''}}</th>
            <th class="{{ $class['app_package_name'] ?? ''}}">{{$list_comment['app_package_name'] ?? ''}}</th>
            <th class="{{ $class['messenger_package_name'] ?? ''}}">{{$list_comment['messenger_package_name'] ?? ''}}</th>
            <th class="{{ $class['sender'] ?? ''}}">{{$list_comment['sender'] ?? ''}}</th>
            <th class="{{ $class['message'] ?? ''}}">{{$list_comment['message'] ?? ''}}</th>
            @admin<th class="{{ $class['is_group'] ?? ''}}">{{$list_comment['is_group'] ?? ''}}</th>@endadmin
            @admin<th class="{{ $class['rule_id'] ?? ''}}">{{$list_comment['rule_id'] ?? ''}}</th>@endadmin
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($bmesseges as $bmessege)

        <tr data-id="{{$bmessege->id}}" class="{{ ($bmessege->status_active=='true') ? 'table-success' : null }}">
            <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
            <td class="{{ $class['bmain_id'] ?? ''}}">{{$bmessege->FullName ?? ''}}</td>
            <td class="{{ $class['created_at'] ?? ''}}">{{$bmessege->created_at ?? ''}}</td>
            <td class="{{ $class['app_package_name'] ?? ''}}">{{$bmessege->app_package_name ?? ''}}</td>
            <td class="{{ $class['messenger_package_name'] ?? ''}}">{{$bmessege->messenger_package_name ?? ''}}</td>
            <td class="{{ $class['sender'] ?? ''}}">{{$bmessege->sender ?? ''}}</td>
            <td class="{{ $class['message'] ?? ''}}">{{$bmessege->message ?? ''}}</td>
            @admin<td class="{{ $class['is_group'] ?? ''}}">{{$bmessege->is_group ?? ''}}</td>@endadmin
            @admin<td class="{{ $class['rule_id'] ?? ''}}">{{ $bmessege->rule_id ?? '' }}</td>@endadmin
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')

