@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['ident']="d-none d-sm-table-cell";
    $class['work_id']="d-none d-sm-table-cell";
    $class['date']="d-none d-sm-table-cell";
    $class['time']="d-none d-sm-table-cell";
    $class['date_time']="d-none d-sm-table-cell";
    $class['in_out']="d-none d-sm-table-cell";
    $class['event_code']="d-none d-sm-table-cell";
    $class['event_string']="d-none d-sm-table-cell";
    $class['firstname']="d-none d-md-table-cell";
    $class['lastname']="d-none d-md-table-cell";
    $class['status_registrer']="d-none d-sm-table-cell";
@endphp

{!! Form::open(['route'=>'administracion.asisst_controls.assit_attendances.storeCSV','method'=>'POST','id'=>'form-assit_attendances','class'=>'pb-2', 'role'=>'form-signin']) !!}

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class['iteration'] ?? ''}}">N</th>
                <th class="{{ $class['firstname'] ?? ''}}">{{$list_comment['firstname'] ?? ''}}</th>
                <th class="{{ $class['date_time'] ?? ''}}">{{$list_comment['date_time'] ?? ''}}</th>
                <th class="{{ $class['event_string'] ?? ''}}">{{$list_comment['event_string'] ?? ''}}</th>
                <th class="{{ $class['event_code'] ?? ''}}">{{$list_comment['event_code'] ?? ''}}</th>
                <th class="{{ $class['ident'] ?? ''}}">{{$list_comment['ident'] ?? ''}}</th>
                <th class="{{ $class['work_id'] ?? ''}}">{{$list_comment['work_id'] ?? ''}}</th>
                <th class="{{ $class['in_out'] ?? ''}}">{{$list_comment['in_out'] ?? ''}}</th>
                <th class="{{ $class['status_registrer'] ?? ''}}">{{$list_comment['status_registrer'] ?? ''}}</th>
            </tr>
        </thead>

        <tbody id="tdatos">
            @php $index = 0; @endphp
            @foreach($assitAttendanceCSV as $assitAttendance)
                <tr data-id="{{$assitAttendance->work_id}}" class="{{ ($assitAttendance->status_registrer) ? 'table-success':null ;}}">
                    <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                    <td class="{{ $class['firstname'] ?? ''}}">
                        {{ $assitAttendance->firstname ?? null }} {{ $assitAttendance->lastname ?? null }}
                    </td>
                    <td class="{{ $class['date_time'] ?? ''}}">
                        {{ $assitAttendance->date_time ?? null }}
                    </td>
                    <td class="{{ $class['event_string'] ?? ''}}">
                        @php $name = 'event_string['.$index.']' ; $value = $assitAttendance->event_string; @endphp
                        {{-- {{Form::hidden($name,$value)}} --}}
                        {{ $value ?? null}}
                    </td>
                    <td class="{{ $class['event_code'] ?? ''}}">
                        {{ $assitAttendance->event_code ?? null }}
                    </td>
                    <td class="{{ $class['ident'] ?? ''}}">
                        {{ $assitAttendance->ident ?? null }}
                    </td>
                    <td class="{{ $class['work_id'] ?? ''}}">
                        @php $name = 'work_id['.$index.']' ; $value = $assitAttendance->work_id; @endphp
                        {{-- {{Form::hidden($name,$value)}} --}}
                        {{ $value ?? null}}
                    </td>
                    <td class="{{ $class['in_out'] ?? ''}}">
                        @php $name = 'in_out['.$index.']' ; $value = $assitAttendance->in_out; @endphp
                        {{-- {{Form::hidden($name,$value)}} --}}
                        {{ $value ?? null}}
                    </td>
                    <td class="{{ $class['status_registrer'] ?? ''}}">
                        @php $name = 'status_registrer['.$index.']' ; $value = $assitAttendance->status_registrer; @endphp
                        {{-- {{Form::hidden($name,$value)}} --}}
                        {{ ($value) ? 'SI':'NO' ;}}
                    </td>
                </tr>
                @php $index++; @endphp
            @endforeach

        </tbody>

    </table>

    {{Form::hidden('file_path',$file_path)}}
    {{Form::hidden('file_name',$file_name)}}

    <fieldset {{ ( $assitAttendanceCSV->count() > 0 ) ? null : 'disabled' }} >
        <div class="btn-group btn-block">
            <button type="submit" class="btn-boletin btn btn-primary w-100">
                <i class="fa fa-save" aria-hidden="true"></i>
                Guardar
            </button>
        </div>
    </fieldset>

{!! Form::close() !!}

@include('administracion.datatables.simple_search')
