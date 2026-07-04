@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_representant_name="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_grado }}">Grado/Sección [P.A.]</th>
                <th class="{{ $class_representant_name }}">CI</th>
                <th class="{{ $class_representant_name }}">Correo</th>
                <th class="{{ $class_representant_name }}">Representante</th>
                <th class="{{ $class_representant_name }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

                @php $representant = $estudiant->representant; @endphp

                <tr data-id="{{$estudiant->id ?? ''}}" data-ci_estudiant="{{$estudiant->ci_estudiant ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->fullname}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                        {{ $estudiant->pgname ?? ''}} {{ $estudiant->psname ?? ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_representant_name ?? '' }}">
                        {{ $representant->ci_representant}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_representant_name ?? '' }}">
                        {{ $representant->email}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_representant_name ?? '' }}">
                        {{ $representant->name}} ||{{ $representant->phone}} || {{ $representant->whatsapp}}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">
                            <a title="Habilitar para solicitud de matrícula" class="btn btn-warning btn-sm" href="{{ route('administracion.inscripciones.register',['id'=>$estudiant->id]) }}" role="button">
                                <i class="{{ $icon_menus['nuevo'] }} fa-1x"></i>
                            </a>
                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')

