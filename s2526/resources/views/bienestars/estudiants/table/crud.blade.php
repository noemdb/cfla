@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_representant="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Nombres Apellidos</th>
                <th class="{{ $class_ci }}">Cédula</th>                
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_action }}">GSEmail</th>
                <th class="{{ $class_representant }}">Representante</th>
                <th class="{{ $class_representant }}">Contacto</th>
                {{-- <th class="{{ $class_action }}">Acción</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)
                @php
                    $representant = $estudiant->representant;
                @endphp
                <tr data-estudiant_id="{{$estudiant->id ?? ''}}" data-id="{{$estudiant->id ?? ''}}" class="{{($status_active) ? '':'table->danger'}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_ci  ?? ''}}">
                        {{$estudiant->fullname}}
                    </td>

                    <td id="td-estudiant-gender-{{ $estudiant->id }}" class="{{ $class_ci  ?? ''}}" title="{{$estudiant->gender ?? 'Sin género registrado'}}">
                        {{$estudiant->ci_estudiant ?? null}}
                    </td>

                    <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                        @if ($estudiant->getInscripcion())
                            <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                                {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                            </span>
                        @else
                        -SIN SECCION-
                        @endif
                    </td>

                    <td style="white-space: wrap !important">
                        <span>{{ $estudiant->gsemail ?? ''}}</span>
                    </td>

                    <td class="{{ $class_representant  ?? ''}}">
                        <span>{{ $representant->name ?? ''}}</span>
                        <div class="text-muted">{{ $representant->ci_representant ?? ''}}</div>
                    </td>
                    <td class="{{ $class_representant  ?? ''}}">
                        <span>{{ $representant->email ?? ''}}</span>
                        <div class="text-muted">{{ $representant->phone ?? ''}}</div>
                    </td>

                    {{-- <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                            <a title="Resumen" class="btn-card btn btn-info bnt-sm" href="#">
                                <i class="{{ $icon_menus['profile'] }} fa-1x"></i>
                            </a>

                        </div>

                    </td> --}}
                </tr>
            @endforeach

        </tbody>
    </table>

    {{-- <div id="container_modal"></div> --}}

{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')

