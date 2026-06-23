@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_profesor }}">Estudiante</th>
            {{-- <th class="{{ $class_profesor }}">Grado</th> --}}

            @foreach ($pensums as $pensum)
                @php $asignatura = $pensum->asignatura; @endphp
                <th class="{{ $class_asignatura ?? '' }} text-center">{{$asignatura->code_sm ?? ''}}</th>
            @endforeach

            <th class="{{ $class_asignatura }} text-center">Aplazadas</th>
            <th class="{{ $class_asignatura }} text-center">Revisiones</th>
            <th class="{{ $class_action }} text-center">&nbsp;</th>

        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($estudiants as $estudiant)
            @php
                $pestudio = $estudiant->pestudio;
                $escala = $pestudio->escala;
                $aprobacion = ($escala->aprobacion) ? : '';
                $count_revision = (!empty($estudiant->boletin_revisions)) ? $estudiant->boletin_revisions->count() : null ;
            @endphp
            <tr data-id="{{$estudiant->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $estudiant->fullname ?? ''}} <br>
                    <small class="text-muted">{{ $estudiant->ci_estudiant ?? ''}}</small>
                </td>
                @php $count_aplazadas = 0; @endphp
                @foreach ($pensums as $pensum)

                    @php
                        $asignatura = $pensum->asignatura;
                        $nota_valor = $estudiant->getNotaFinal($pensum->id,0,false);
                        $nota_literal = $baremo->getLiteral($pestudio->id,$nota_valor) ;
                        $enable_academic_index = $asignatura->enable_academic_index;
                        $count_aplazadas = ($nota_valor < $aprobacion) ? ($count_aplazadas+1) : $count_aplazadas ;
                        $nota_pf = (is_numeric($nota_valor)) ? str_pad($nota_valor, 2, "0", STR_PAD_LEFT) : $nota_valor ;
                    @endphp

                    <td class="{{ $class_asignatura ?? '' }} {{ ($nota_valor < $aprobacion) ? 'alert-danger':null }} text-center">
                        {!! ($enable_academic_index=="true") ? $nota_pf : $nota_literal !!}
                    </td>

                @endforeach
                <td class="{{ $class_asignatura ?? '' }} text-center">
                    {{ $count_aplazadas ?? '' }}
                </td>
                <td class="{{ $class_asignatura ?? '' }} text-center">
                    {{ $count_revision ?? '' }}
                </td>
                <td class="{{ $class_action ?? '' }}  text-right">
                    <div class="btn-group btn-group-sm">
                        @php $route = ($estudiant) ? route('administracion.historico_notas.certificacion.pdf',$estudiant->id) : "#"; @endphp
                        @php $disabled = ($count_revision>0) ? null:'disabled' @endphp
                        @include('elements.buttons.crud.default.info',['route'=>$route,'disabled'=>$disabled])

                        @php $route = ($estudiant) ? route('administracion.boletin_revisions.create',$estudiant->id) : "#"; @endphp
                        @php $disabled = ($count_aplazadas>0) ? null:'disabled' @endphp
                        @include('elements.buttons.crud.default.create',['route'=>$route,'disabled'=>$disabled,'title'=>'Registrar nueva revisión'])

                        {{-- <a title="Crear Revisión" class="btn-print btn btn-dark btn-sm {{ $disabled ?? ''}}" data-url="{{$route ?? ''}}" href="{{$route ?? ''}}" target="_blank" role="button" >
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a> --}}

                    </div>
                </td>

            </tr>

        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple_search')

