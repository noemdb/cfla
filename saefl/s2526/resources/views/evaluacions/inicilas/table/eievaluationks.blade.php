{{--
'profesor_id' => 'Profesor',
'grado_id' => 'Grado',
'lapso_id' => 'Momento',
'seccion_id' => 'Sección',
'finicial' => 'Fecha inicial',
'ffinal' => 'Fecha final',
'observaciones' => 'Observaciones del docente',
'recomendacion' => 'Recomendación del docente',
'asistencia' => 'Control de Asistencia', 

'eievaluationk_id' => 'Plan de Evaluación',
'pevaluacion_id' => 'Área de aprendizaje/Año',
'fecha' => 'Fecha de evaluación',
'nombre_ninos' => 'Nombre de los niños',
'aprendizaje_alcanzado' => 'Aprendizaje a ser alcanzado',
'componente' => 'Componente del área de aprendizaje',
'indicadores' => 'Indicadores de evaluación',
'instrumento' => 'Instrumento de evaluación',
'observacion' => 'Observaciones adicionales del docente',
'lapso_id' => 'Momento',
--}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>Profesor</th>
            <th>Inscripción</th>
            <th>Momento</th>
            <th>F.Inicial</th>
            <th>F.Final</th>
            <th>observaciones</th>
            <th>Justificación</th>
            {{-- <th>Observación</th> --}}
            {{-- <th>Recomendación</th> --}}
            {{-- <th>C.Asistencia</th> --}}
            <th>Actividaes</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eievaluationks as $item)
            @php
                $grado = $item->grado;
                $seccion = $item->seccion;
                $lapso = $item->lapso;
            @endphp
            <tr class="">
                <td>{{ $item->profesor->fullname}}</td>
                <td>{{ $grado->name ?? null}} {{ $seccion->name ?? null}}</td>
                <td>{{ $lapso->name}}</td>
                <td class="text-nowrap">{{ $item->finicial->format('d-m-Y')}}</td>
                <td class="text-nowrap">{{ $item->ffinal->format('d-m-Y')}}</td>
                <td>{{ $item->observaciones}}</td>
                <td>{{ $item->justificacion}}</td>
                {{-- <td>{{ $item->observacion}}</td> --}}
                {{-- <td>{{ $item->recomendacion}}</td> --}}
                {{-- <td>{{ $item->asistencia}}</td> --}}

                <td>
                    <ul class="list-group">
                        @php $eievaluationps = $item->eievaluationps; @endphp
                        @forelse ($eievaluationps as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="text-wrap">{{$loop->iteration}}.- {{$subItem->nombre_ninos}} <div>{{$subItem->aprendizaje_alcanzado}}</div></div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>
                </td>             

                <td>
                    <div class="d-flex justify-content-start">

                        <a title="Formato Plan de Evaluación" class="btn btn-dark btn-sm mr-1" href="{{route('evaluacions.eievaluationks.format.index',$item->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No hay datos</td>
            </tr>
        @endforelse
        
    </tbody>
</table>