@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['description']= ($modeIndex) ? "d-none d-lg-table-cell" : "d-none";
    $class['grado_seccion']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['fecha']="d-none d-md-table-cell";
    $class['title']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['status']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['status_sender']="d-none d-md-table-cell";
    $class['status_ready']="d-none d-md-table-cell";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default-mailers';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['user_id'] ?? ''}}">{{$list_comment['user_id'] ?? ''}}</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['grado_seccion'] ?? ''}}">{{$list_comment['grado_seccion'] ?? ''}}</th>
            <th class="{{ $class['fecha'] ?? ''}}">{{$list_comment['fecha'] ?? ''}}</th>
            <th class="{{ $class['title'] ?? ''}}">{{$list_comment['title'] ?? ''}}</th>
            <th class="{{ $class['status'] ?? ''}}">{{$list_comment['status'] ?? ''}}</th>
            <th class="{{ $class['status_ready'] ?? ''}}">{{$list_comment['status_ready'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($mailers as $mailer)

            <tr data-id="{{$mailer->id}}" class="{{($mailer->id == $mailer_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['user_id'] ?? ''}}">{{$mailer->username}}</td>
                <td class="{{ $class['name'] ?? ''}}">
                    {{$mailer->name ?? ''}}
                    @if (!$modeIndex)
                        <div class="text-dark">{{$mailer->pestudio_grado_seccion ?? ''}}</div>
                        @if ($mailer->status_adviders=='true') <h6><span class=" badge badge-light">Delegados </span></h6> @endif
                    @endif
                </td>
                <td class="{{ $class['description'] ?? ''}}" title="{{$mailer->description ?? ''}}">{{Str::limit($mailer->description,40,'...') ?? ''}}</td>
                <td class="{{ $class['grado_seccion'] ?? ''}}">
                    {{$mailer->pestudio_grado_seccion ?? ''}}
                    @if ($mailer->status_adviders=='true') <h6><span class=" badge badge-light">Delegados </span></h6> @endif
                    @if ($mailer->status_quota) <div class="font-weight-bold">Filtrado por CI </div> @endif
                </td>
                <td class="{{ $class['fecha'] ?? ''}}">{{$mailer->date->format('d-m-Y g:i A') ?? ''}}</td>
                <td class="{{ $class['title'] ?? ''}}">{{$mailer->title ?? ''}}</td>
                <td class="{{ $class['status'] ?? ''}}">{{ ($mailer->status=='true') ? 'Activo':'Desactivo' }}</td>
                <td class="{{ $class['status_ready'] ?? ''}}">{{ ($mailer->status_ready) ? 'SI' : 'NO' ?? ''}}</td>
                <td class="{{ $class['action'] ?? ''}}">

                    @includeWhen($modeIndex,'livewire.academico.mailer.table.partials.btnModeIndex',['key'=>'table'])
                    @includeWhen(!$modeIndex,'livewire.academico.mailer.table.partials.btnModeOthers')

                </td>
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

{{-- @include('administracion.datatables.exportBootstrap') --}}
