@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_email="d-none d-xl-table-cell";
    $class_saldo="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_planpago }}">Grado</th>
                <th class="{{ $class_planpago }}">Sección</th>
                <th class="{{ $class_email }}">Tipo</th>
                <th class="{{ $class_email }}">Escolaridad</th>
                <th class="{{ $class_email }}" title="Correo Electrónico Classroom">Email CR</th>
                <th class="{{ $class_saldo }}">Saldo</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($inscripcions as $inscripcion)

                @php
                    $estudiant = $inscripcion->estudiant;
                    $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill,2);
                @endphp

                <tr data-id="{{$inscripcion->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->fullname ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>
                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->grado->name ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->seccion->name ?? ''}}
                    </td>

                    <td class="{{ $class_email ?? '' }}">
                        {{ $inscripcion->tinscripcion->name ?? ''}}
                    </td>

                    <td class="{{ $class_email ?? '' }}">
                        {{ $inscripcion->escolaridad->name ?? ''}}
                    </td>

                    <td class="{{ $class_email ?? '' }}">
                        {{ $estudiant->gsemail ?? ''}}
                    </td>

                    <td class="{{ $class_saldo ?? '' }}">
                        @if ($exchange_ammount_expire_bill > 0)
                            <span class="badge badge-danger">$ {{ f_float($exchange_ammount_expire_bill) }}</span>
                        @else
                            <span class="badge badge-success">SOLVENTE</span>
                        @endif
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">
                            <a title="Contancia de Inscripción" class="btn btn-{{ ($exchange_ammount_expire_bill>0) ? 'secondary disabled' : 'primary' }}" href="{{route('representants.constancia.inscripcions.pdf',$estudiant->id)}}" target="_blank" role="button">
                                <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x text-light"></i>
                            </a>
                            <a title="Contancia de Estudio" class="btn btn-{{ ($exchange_ammount_expire_bill>0) ? 'secondary disabled' : 'info' }}" href="{{route('representants.inscripcions.constancia.estudio.pdf',$estudiant->id)}}" target="_blank" role="button">
                                <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x text-light"></i>
                            </a>
                            {{-- @if ($estudiant->promocion)
                                <a  title="Contancia de Prosecución"class="btn btn-danger" href="{{route('representants.inscripcions.constancia.prosecucion.pdf',$estudiant->id)}}" target="_blank" role="button">
                                    <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x text-light"></i>
                                </a>
                            @endif
                            @if ($estudiant->prosecucion)
                                <a title="Contancia de Pomosión" class="btn btn-secondary" href="{{route('representants.inscripcions.constancia.promocion.pdf',$estudiant->id)}}" target="_blank" role="button">
                                    <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x text-light"></i>
                                </a>
                            @endif --}}
                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('representants.datatables.simple')
