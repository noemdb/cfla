{{-- 'user_id','date','name','description','observations','icon','status_holidays' --}}
@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']="d-none d-sm-table-cell";
    $class['profile_name']="d-none d-sm-table-cell";
    $class['timestamp']="d-none d-sm-table-cell";
    $class['status_holidays']="d-none d-sm-table-cell";

    $class['body']= "d-none d-sm-table-cell";
    $class['from']= "d-none d-sm-table-cell";
    $class['answer']= "d-none d-sm-table-cell";
    $class['representant']= "d-none d-sm-table-cell";

    $class['action']="d-none d-sm-table-cell";
    
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['timestamp'] ?? ''}}">{{$list_comment['timestamp'] ?? ''}}</th>
            <th class="{{ $class['event_name'] ?? ''}}">{{$list_comment['event_name'] ?? ''}}</th>
            <th class="{{ $class['payload'] ?? ''}}">{{$list_comment['payload'] ?? ''}}</th>
        </tr>
    </thead>

    <tbody>

        @forelse($messeges as $item)
            <tr>
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="text-nowrap {{ $class['timestamp'] ?? ''}}">{{$item->created_at->format('d-m-Y h:i:s')}}</td>
                <td class="{{ $class['event_name'] ?? ''}}">{{$item->event_name ?? null}}</td>
                <td class="{{ $class['payload'] ?? ''}}">{{$item->payload ?? null}}</td>                                
            </tr>
        @empty
            <tr>
                <td colspan="4" align="center">
                    <strong>No hay datos</strong>
                </td>
            </tr>
        @endforelse

    </tbody>

</table>

{{ $messeges->links() }}