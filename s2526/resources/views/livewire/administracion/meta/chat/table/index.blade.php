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
            <th class="{{ $class['profile_name'] ?? ''}}">Remitente</th>
            <th class="{{ $class['body'] ?? ''}}">{{$list_comment['body'] ?? ''}}</th>
            <th class="{{ $class['answer'] ?? ''}}">{{$list_comment['answer'] ?? ''}}</th>
        </tr>
    </thead>

    <tbody>

        @forelse($messeges as $item)
        @php $representant = $item->representant; @endphp               

            <tr class="{{ ($item->profile_name == "SAEFL Bot") ? 'alert alert-success font-weight-bold':null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="text-nowrap {{ $class['timestamp'] ?? ''}}">{{$item->timestamp->format('d-m-Y h:i:s')}}</td>
                <td class="text-nowrap {{ $class['profile_name'] ?? ''}}">
                    <div class="font-weight-bold">{{$item->profile_name}}</div>                     
                    <div class="text-muted font-weight-bold">{{$representant->name ?? null}}</div>
                    <div class="text-muted font-weight-bold">{{$item->from ?? null}}</div>
                </td>
                <td class="{!! $class['body'] ?? '' !!}">
                    @switch($item->type)
                        @case('text') {{$item->body}} @break
                        {{-- @case('image')  Image @break --}}
                        @case('image')  <div>{{ $item->processImageMessage() ?? null}}</div> @break
                        @default                            
                    @endswitch
                </td>                
                <td class="{{ $class['answer'] ?? '' }}">{{$item->answer->body ?? null}}</td> 
                                
            </tr>

        @empty

            <tr>
                <td colspan="8" align="center">
                    <strong>No hay datos</strong>
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{ $messeges->links() }}

{{-- @include('administracion.datatables.exportBootstrap') --}}
