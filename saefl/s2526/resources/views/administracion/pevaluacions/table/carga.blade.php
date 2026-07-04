@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm p-1 small" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_ci }} text-center">Profesor</th>
                <th class="{{ $class_action }} text-right">Asignar</th>
            </tr>
        </thead>

        <tbody id="tdatos">


        @foreach($pensums as $pensum)

            @php
                $grado = $pensum->grado;
                $asignatura = $pensum->asignatura;
                $class_btn_1er = ($pensum->exist_seccion($pensum->id,1,$seccion_id)) ? 'btn-primary' : 'btn-outline-primary' ;
                $class_btn_2do = ($pensum->exist_seccion($pensum->id,2,$seccion_id)) ? 'btn-success' : 'btn-outline-success' ;
                $class_btn_3er = ($pensum->exist_seccion($pensum->id,3,$seccion_id)) ? 'btn-danger' : 'btn-outline-danger' ;
            @endphp

            <tr data-asignatura="{{$asignatura->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_asignatura  ?? ''}}">
                    {{$asignatura->fullname}}
                </td>
                <td class="{{ $class_ci  ?? ''}}">

                    <div class="row">

                        @php $profesors = $pensum->getProfesors($seccion_id); @endphp
                        @foreach ($profesors as $profesor)

                            <div class="col-4 list-group-item text-capitalize">
                                <span class=" font-weight-bold">
                                    {{ $profesor->lapso_code ?? '' }}
                                </span>
                                {{ $profesor->md_name ?? ''  }}
                            </div>

                        @endforeach

                    </div>

                </td>

                <td class="{{ $class_action ?? '' }} text-right" id="btn-action-{{ $asignatura->id }}">
                        <a title="Asignar Plan de Evaluación/Evaluaciones 1er Lapso" class="btn {{$class_btn_1er ?? ''}} btn-sm" href="{{ route('administracion.pevaluacions.create',['grado_id'=>$grado->id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum->id,'lapso_id'=>1]) }}" role="button">1</a>
                        <a title="Asignar Plan de Evaluación/Evaluaciones 2do Lapso" class="btn {{$class_btn_2do ?? ''}} btn-sm" href="{{ route('administracion.pevaluacions.create',['grado_id'=>$grado->id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum->id,'lapso_id'=>2]) }}" role="button">2</a>
                        <a title="Asignar Plan de Evaluación/Evaluaciones 3er Lapso" class="btn {{$class_btn_3er ?? ''}}  btn-sm" href="{{ route('administracion.pevaluacions.create',['grado_id'=>$grado->id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum->id,'lapso_id'=>3]) }}" role="button">3</a>
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>
