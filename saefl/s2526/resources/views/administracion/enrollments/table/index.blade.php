@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['ci_estudiant']="d-none d-sm-table-cell";
    $class['fullname']="d-none d-sm-table-cell";
    $class['representant']="d-none d-sm-table-cell";
    $class['gender']="d-none d-sm-table-cell";
    $class['date_birth']="d-none d-sm-table-cell";
    $class['description']="d-none d-md-table-cell";
    $class['observation']="d-none d-md-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{-- 'user_id','planpago_id','grado_inicial_id','seccion_inicial','type_ci_id','ci_estudiant','ci_estudiant_temp','lastname','name','gender',
        'date_birth','city_birth','town_hall_birth','state_birth','country_birth','dir_address','phone','cellphone','email','gsemail','representant_ci',
        'fullname','date_birth_active'

--}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['ci_estudiant'] ?? ''}}">{{$list_comment['ci_estudiant'] ?? ''}}</th>
            <th class="{{ $class['fullname'] ?? ''}}">{{$list_comment['fullname'] ?? ''}}</th>
            <th class="{{ $class['representant'] ?? ''}}">{{$list_comment['representant'] ?? ''}}</th>
            <th class="{{ $class['gender'] ?? ''}}">{{$list_comment['gender'] ?? ''}}</th>
            <th class="{{ $class['date_birth'] ?? ''}}">{{$list_comment['date_birth'] ?? ''}}</th>
            <th class="{{ $class['observation'] ?? ''}}">Registro</th>
            <th class="{{ $class['observation'] ?? ''}}">Aseg.Matícula</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($estudiants as $estudiant)

    @php
        $representant = $estudiant->representant;
        $enrollment = $estudiant->enrollment;
    @endphp

        <tr data-id="{{$estudiant->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
            <td class="{{ $class['ci_estudiant'] ?? ''}}">{{ $estudiant->ci_estudiant ?? ''}}</td>
            <td class="{{ $class['fullname'] ?? ''}}">{{ $estudiant->fullname ?? ''}}</td>
            <td class="{{ $class['representant'] ?? ''}}">{{ $representant->name ?? ''}} <small>{{ $representant->ci_representant ?? ''}}</small></td>
            <td class="{{ $class['gender'] ?? ''}}">{{ $estudiant->gender ?? '' }}</td>
            <td class="{{ $class['date_birth'] ?? ''}}">{{ f_date($estudiant->date_birth) }}</td>
            <td class="{{ $class['observation'] ?? ''}}">{{ (isset($enrollment)) ? '-SI-':'-NO-' }}</td>
            <td class="{{ $class['observation'] ?? ''}}">{{ ($estudiant->status_prosecution) ? '|SI|':'|NO|' }}</td>
            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">
                    <a title="PDF" class="btn btn-dark btn-sm"  href="{{route('administracion.enrollments.pdf.individual',$estudiant->id)}}" role="button" target="_blank">
                        <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
