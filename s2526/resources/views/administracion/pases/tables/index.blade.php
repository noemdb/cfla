@php
    $class['index']="";
    $class['estudiant_id']="d-none d-lg-table-cell";
    $class['type']="d-none d-lg-table-cell";
    $class['motive']="d-none d-lg-table-cell";
    $class['description']="dd-none d-lg-table-cell";
    $class['date']="d-none d-lg-table-cell";
    $class['status']="d-none d-lg-table-cell";
    $class['action']="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['index'] }}">N</th>
            <th class="{{ $class['estudiant_id'] }}">{{$list_comment['estudiant_id'] ?? null}}</th>
            <th class="{{ $class['type'] }}">{{$list_comment['type'] ?? null}}</th>
            <th class="{{ $class['motive'] }}">{{$list_comment['motive'] ?? null}}</th>
            <th class="{{ $class['description'] }}">{{$list_comment['description'] ?? null}}</th>
            <th class="{{ $class['date'] }}">{{$list_comment['date'] ?? null}}</th>
            <th class="{{ $class['status'] }}">{{$list_comment['status'] ?? null}}</th>
            <th class="{{ $class['action'] }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($pases as $item)

    @php
        $estudiant = $item->estudiant;
        $full_inscripcion = ($estudiant) ? $estudiant->full_inscripcion: null;
        $profesor = $item->profesor;
        $pensum = $item->pensum;
    @endphp

        <tr data-id="{{$item->id}}">
            <td id="td-count" class="{{ $class['index'] ?? null }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class['estudiant_id']  ?? null}}">
                {{ ($estudiant) ? $estudiant->fullname : null }}
                <hr class="m-0 p-0">
                <div class="text-muted small">
                    {{ $full_inscripcion ?? null }}
                </div>   
                <div class="text-muted small">
                    Prof: {{ ($profesor) ? $profesor->fullname : null }} || {{ ($pensum) ? $pensum->asignatura->name : null }}
                </div>          
            </td>
            <td class="{{ $class['type']  ?? null}}">
                {{ ($item) ? $item->type : null}}
            </td>
            <td class="{{ $class['motive']  ?? null}}">
                {{$item->motive ?? ''}}
            </td>
            <td class="{{ $class['description']  ?? null}}">
                {{$item->description ?? ''}}
            </td>
            <td class="{{ $class['date']  ?? null}}">
                {{ ($item->date_time) ? $item->date_time->format('d-m-Y h:i a'): null}}
            </td>
            <td class="{{ $class['status']  ?? null}}">
                {{ $item->status ?? null}}
                <div class="text-muted font-weight-bold">{{($item->status_notifications) ? 'Notificado' : null}}</div>
            </td>
            <td class="{{ $class['action'] ?? null }}">

                <div class="btn-group btn-group-sm">

                    <a title="" name="print" id="print" class="btn btn-dark" href="{{route('permissions.pases.pdf.certificate',$item->id)}}" role="button" target="_blank">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>

                    <a title="" name="edit" id="edit" class="btn btn-warning {{($item->status_notifications) ? 'disabled' : null}}" href="{{route('evaluacions.permissions.pases.edit',$item->id)}}" role="button">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>

                    <a title="Enviar notificación" name="send" id="send" class="btn btn-success {{($item->status_notifications) ? 'disabled' : null}}" href="{{route('evaluacions.permissions.pases.send',$item->id)}}" role="button">
                        <i class="fa fa-inbox" aria-hidden="true"></i>
                    </a>

                    <a title="Vista previa" name="view" id="view" class="btn btn-info" href="{{route('evaluacions.permissions.pases.view',$item->id)}}" role="button" target="_blank">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')
