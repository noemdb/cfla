@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Plan Estudio</th>
                <th class="{{ $class_code_sm }}">Grado</th>
                <th class="{{ $class_code_sm }}">Nombre</th>
                <th class="{{ $class_ht }}">Descripción</th>
                <th class="{{ $class_ht }}">N. Estudiantes</th>
                <th class="{{ $class_code_sm }}">Estado</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($seccions as $seccion)

            @php $pestudio = $seccion->grado->pestudio; @endphp

            <tr data-id="{{$seccion->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_asignatura  ?? ''}}">
                    {{$seccion->grado->pestudio->name ?? ''}}
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$seccion->grado->name ?? ''}}
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$seccion->name ?? ''}}
                </td>
                <td class="{{ $class_ht  ?? ''}}">
                    {{$seccion->description ?? ''}}
                </td>
                <td class="{{ $class_ht  ?? ''}}">
                    {{$seccion->amount_student ?? ''}}
                </td>
                <td class="{{ $class_ht  ?? ''}}">
                    {{($seccion->status_active=='true') ? 'Activo':'Desactivo'}}
                </td>
                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">

                        @php $disabled = ($pestudio->code_oficial<>'31059') ? true:false ; @endphp

                        <a target="_blank" class="btn btn-dark {{ ($disabled) ? 'disabled':null }}" href="{{ $route=route('administracion.boletin_revisions.resumen_final.pdf',[$seccion->id])}}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>

                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
