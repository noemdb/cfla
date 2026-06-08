@php
    //'user_id','ci_representant','name','phone','cellphone','pmovilphone','email','gsemail','status_active','status_blacklist'
    $class['iteration']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['ci_representant']="d-none d-sm-table-cell";
    $class['email']="d-none d-sm-table-cell";
    $class['status_sender']="d-none d-sm-table-cell text-right";
    $table_id = 'table-data-default-show';
@endphp

{{--

'representant_id','name','code','description','seccion_id','grado_id','finicial','finicial',
'subject','title','subtitle','greeting','body','footer',
'status','status_adviders'

--}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment_representant['name'] ?? ''}}</th>
            {{-- <th class="{{ $class['ci_representant'] ?? ''}}">{{$list_comment_representant['ci_representant'] ?? ''}}</th> --}}
            <th class="{{ $class['email'] ?? ''}}">{{$list_comment_representant['email'] ?? ''}}</th>
            <th class="{{ $class['status_sender'] ?? ''}}">{{$list_comment_representant['status_sender'] ?? ''}}</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($representants as $representant)

            <tr data-id="{{$representant->id}}" class="{{($representant->status_sender($mailer->id)) ? 'font-weight-bold text-success' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['name'] ?? ''}}">{{$representant->name_full ?? ''}}<br><span class="text-small text-muted">CI: {{$representant->ci_representant ?? ''}}</span></td>
                {{-- <td class="{{ $class['ci_representant'] ?? ''}}">{{$representant->ci_representant ?? ''}}</td> --}}
                <td class="{{ $class['email'] ?? ''}}">
                    {{$representant->email ?? ''}}
                </td>                
                <td class="{{ $class['status_sender'] ?? ''}}">{{($representant->status_sender($mailer->id)) ? 'SI' : 'NO'}}</td>                
                {{-- <td class="{{ $class['status_sender'] ?? ''}}">{{ $representant->status_sender($mailer->id) }}</td>                 --}}
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

{{-- @include('academicos.datatables.default') --}}
{{-- @include('administracion.datatables.exportBootstrap') --}}

