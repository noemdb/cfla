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

            @php $grado = $seccion->grado; @endphp
            @php $pestudio = ($grado) ? $grado->pestudio : null; @endphp

            <tr data-id="{{$seccion->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_asignatura  ?? ''}}">
                    {{$pestudio->name ?? ''}} <small>[{{$pestudio->code ?? ''}}]</small>                    
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$grado->name ?? ''}}
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$seccion->name ?? ''}}
                </td>
                <td class="{{ $class_ht  ?? ''}}">
                    {{$seccion->description ?? ''}}
                </td>
                <td class="{{ $class_ht  ?? ''}}">
                    {{$seccion->getCountEstudiants() ?? ''}}
                </td>
                <td class="{{ $class_ht  ?? ''}}">
                    {{($seccion->status_active=='true') ? 'Activo':'Desactivo'}}
                </td>
                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">

                        {{-- @php $disabled = ($pestudio->code<>'31059') ? true:false ; @endphp --}}
                        @php $disabled = false ; @endphp

                        <a title="Informe Final" target="_blank" class="btn btn-dark {{ ($disabled) ? 'disabled':null }}" href="{{ $route=route('administracion.boletins.resumen_final.pdf',[$seccion->id])}}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>
                        {{-- @php $disabled = ($pestudio->code_oficial<>'31059') ? true:false ; @endphp --}}
                        <a title="Informe Revisión" target="_blank" class="btn btn-success {{ ($disabled) ? 'disabled':null }}" href="{{ $route=route('administracion.boletin_revisions.resumen_revision.pdf',['seccion_id'=>$seccion->id,'type'=>'REVISIÓN'])}}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>

                        <a title="Informe Transferencia" target="_blank" class="btn btn-info {{ ($disabled) ? 'disabled':null }}" href="{{ $route=route('administracion.boletin_revisions.resumen_revision.pdf',['seccion_id'=>$seccion->id,'type'=>'TRANSFERENCIA'])}}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>

                        <a title="Informe Traslados" target="_blank" class="btn btn-warning {{ ($disabled) ? 'disabled':null }}" href="{{ $route=route('administracion.boletin_revisions.resumen_revision.pdf',['seccion_id'=>$seccion->id,'type'=>'EQUIVALENCIA'])}}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>

                        <a title="Informe Otros" target="_blank" class="btn btn-danger {{ ($disabled) ? 'disabled':null }}" href="{{ $route=route('administracion.boletin_revisions.resumen_revision.pdf',['seccion_id'=>$seccion->id,'type'=>'ÁREA PENDIENTE'])}}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>

                        <a title="Informe Otros" target="_blank" class="btn btn-primary {{ ($disabled) ? 'disabled':null }}" href="{{ $route=route('administracion.boletin_revisions.resumen_revision.pdf',['seccion_id'=>$seccion->id,'type'=>'MATERIA QUEDADA'])}}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>

{{-- 
TRANSFERENCIA
EQUIVALENCIA
ÁREA PENDIENTE
MATERIA QUEDADA

FINAL 
REVISION 
--}}

                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
